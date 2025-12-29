<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Employee;
use App\Models\TimeSlot;
use App\Models\Specialization;
use App\Models\Booking;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Service;
use App\Models\EmployeeSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        // Users Statistics
        $totalCustomers = User::where('role', 'customer')->count();
        $totalStaff = User::where('role', 'staff')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalEmployees = Employee::count();

        // Bookings Statistics
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        // Revenue Statistics
        $totalRevenue = Booking::where('payment_status', 'paid')->sum('total_price');
        $paidBookings = Booking::where('payment_status', 'paid')->count();
        $unpaidBookings = Booking::where('payment_status', 'unpaid')->count();

        // Services Statistics
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();
        $totalSubCategories = SubCategory::count();
        $activeSubCategories = SubCategory::where('is_active', true)->count();
        $totalServices = Service::count();
        $activeServices = Service::where('is_active', true)->count();

        // Specializations Statistics
        $totalSpecializations = Specialization::count();
        $activeSpecializations = Specialization::where('is_active', true)->count();

        // Time Slots Statistics
        $totalTimeSlots = TimeSlot::count();
        $availableTimeSlots = TimeSlot::where('is_available', true)->count();

        // Recent Data
        $recentBookings = Booking::with(['customer', 'employee.user', 'service'])
            ->latest()
            ->limit(10)
            ->get();

        $recentCustomers = User::where('role', 'customer')
            ->latest()
            ->limit(10)
            ->get();

        $recentStaff = User::where('role', 'staff')
            ->with('employee')
            ->latest()
            ->limit(10)
            ->get();

        // Today's Statistics
        $todayBookings = Booking::whereDate('booking_date', Carbon::today())->count();
        $todayRevenue = Booking::whereDate('created_at', Carbon::today())
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // This Month Statistics
        $monthBookings = Booking::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $monthRevenue = Booking::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $stats = [
            // Users
            'total_customers' => $totalCustomers,
            'total_staff' => $totalStaff,
            'total_admins' => $totalAdmins,
            'total_employees' => $totalEmployees,

            // Bookings
            'total_bookings' => $totalBookings,
            'pending_bookings' => $pendingBookings,
            'confirmed_bookings' => $confirmedBookings,
            'completed_bookings' => $completedBookings,
            'cancelled_bookings' => $cancelledBookings,

            // Revenue
            'total_revenue' => $totalRevenue,
            'paid_bookings' => $paidBookings,
            'unpaid_bookings' => $unpaidBookings,

            // Services
            'total_categories' => $totalCategories,
            'active_categories' => $activeCategories,
            'total_sub_categories' => $totalSubCategories,
            'active_sub_categories' => $activeSubCategories,
            'total_services' => $totalServices,
            'active_services' => $activeServices,

            // Specializations
            'total_specializations' => $totalSpecializations,
            'active_specializations' => $activeSpecializations,

            // Time Slots
            'total_time_slots' => $totalTimeSlots,
            'available_time_slots' => $availableTimeSlots,

            // Today
            'today_bookings' => $todayBookings,
            'today_revenue' => $todayRevenue,

            // This Month
            'month_bookings' => $monthBookings,
            'month_revenue' => $monthRevenue,
        ];

        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentCustomers', 'recentStaff'));
    }

    /**
     * Show users main page - redirect to admins
     */
    public function users()
    {
        return redirect()->route('admin.users.admins');
    }

    // ==================== Admins Management ====================

    /**
     * Show all admins
     */
    public function admins(Request $request)
    {
        $query = User::where('role', User::ROLE_ADMIN);

        // Search
        $searchQuery = $request->get('search', '');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.admins.index', compact('users', 'searchQuery'));
    }

    /**
     * Show create admin form
     */
    public function createAdmin()
    {
        return view('admin.users.admins.create');
    }

    /**
     * Store new admin
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => User::ROLE_ADMIN,
            'password' => Hash::make($request->password),
            'phone_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.admins')
            ->with('success', 'تم إنشاء الأدمن بنجاح');
    }

    /**
     * Show admin profile page
     */
    public function showAdmin(User $user)
    {
        if (!$user->isAdmin()) {
            abort(404);
        }

        return view('admin.users.admins.show', compact('user'));
    }

    /**
     * Show edit admin form
     */
    public function editAdmin(User $user)
    {
        if (!$user->isAdmin()) {
            abort(404);
        }

        return view('admin.users.admins.edit', compact('user'));
    }

    /**
     * Update admin
     */
    public function updateAdmin(Request $request, User $user)
    {
        if (!$user->isAdmin()) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);

        $user->update($request->only(['name', 'email', 'phone']));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.admins.show', $user)
            ->with('success', 'تم تحديث الأدمن بنجاح');
    }

    /**
     * Delete admin
     */
    public function deleteAdmin(User $user)
    {
        if (!$user->isAdmin()) {
            abort(404);
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.admins')
                ->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        $user->delete();

        return redirect()->route('admin.users.admins')
            ->with('success', 'تم حذف الأدمن بنجاح');
    }

    // ==================== Staff Management ====================

    /**
     * Show all staff
     */
    public function staff(Request $request)
    {
        $query = User::where('role', User::ROLE_STAFF);

        // Search
        $searchQuery = $request->get('search', '');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->with('employee.specializations')->latest()->paginate(20)->withQueryString();
        $specializations = Specialization::where('is_active', true)->get();

        return view('admin.users.staff.index', compact('users', 'searchQuery', 'specializations'));
    }

    /**
     * Show create staff form
     */
    public function createStaff()
    {
        $specializations = Specialization::where('is_active', true)->get();
        return view('admin.users.staff.create', compact('specializations'));
    }

    /**
     * Store new staff
     */
    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
            'employee.bio' => 'nullable|string',
            'employee.hourly_rate' => 'nullable|numeric|min:0',
            'employee.is_available' => 'nullable|boolean',
            'employee.specializations' => 'nullable|array',
            'employee.specializations.*' => 'nullable|exists:specializations,id',
            'bio' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'specializations' => 'nullable|array',
            'specializations.*' => 'required|exists:specializations,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => User::ROLE_STAFF,
            'password' => Hash::make($request->password),
            'phone_verified_at' => now(),
        ]);

        // Create employee record - support both formats
        $bio = $request->input('employee.bio') ?? $request->input('bio');
        $hourlyRate = $request->input('employee.hourly_rate') ?? $request->input('hourly_rate');
        $isAvailable = $request->has('employee.is_available') || $request->has('is_available');

        $employee = Employee::create([
            'user_id' => $user->id,
            'bio' => $bio,
            'hourly_rate' => $hourlyRate,
            'is_available' => $isAvailable,
        ]);

        // Sync specializations - support both formats
        $specializations = $request->input('employee.specializations') ?? $request->input('specializations', []);

        // Ensure specializations is an array and filter out empty values
        if (is_array($specializations)) {
            $specializations = array_filter($specializations, function ($value) {
                return !empty($value) && is_numeric($value);
            });
            $specializations = array_values($specializations); // Re-index array
        } else {
            $specializations = [];
        }

        // Always sync specializations (even if empty array to detach)
        $employee->specializations()->sync($specializations);

        return redirect()->route('admin.users.staff')
            ->with('success', 'تم إنشاء الموظف بنجاح');
    }

    /**
     * Show staff profile page
     */
    public function showStaff(User $user)
    {
        if (!$user->isStaff()) {
            abort(404);
        }

        $user->load(['employee.specializations']);

        return view('admin.users.staff.show', compact('user'));
    }

    /**
     * Show edit staff form
     */
    public function editStaff(User $user)
    {
        if (!$user->isStaff()) {
            abort(404);
        }

        $user->load(['employee.specializations']);
        $specializations = Specialization::where('is_active', true)->get();
        return view('admin.users.staff.edit', compact('user', 'specializations'));
    }

    /**
     * Update staff
     */
    public function updateStaff(Request $request, User $user)
    {
        if (!$user->isStaff()) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:8',
            'employee.specializations' => 'nullable|array',
            'employee.specializations.*' => 'exists:specializations,id',
            'employee.bio' => 'nullable|string',
            'employee.hourly_rate' => 'nullable|numeric|min:0',
            'employee.is_available' => 'nullable|boolean',
            'specializations' => 'nullable|array',
            'specializations.*' => 'exists:specializations,id',
            'bio' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric|min:0',
            'is_available' => 'nullable|boolean',
        ]);

        $user->update($request->only(['name', 'email', 'phone']));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Update employee info - support both formats
        $employee = $user->employee()->firstOrCreate(['user_id' => $user->id]);
        $bio = $request->input('employee.bio') ?? $request->input('bio');
        $hourlyRate = $request->input('employee.hourly_rate') ?? $request->input('hourly_rate');
        $isAvailable = $request->has('employee.is_available') || $request->has('is_available');

        $employee->update([
            'bio' => $bio,
            'hourly_rate' => $hourlyRate,
            'is_available' => $isAvailable,
        ]);

        // Sync specializations - support both formats
        $specializations = $request->input('employee.specializations') ?? $request->input('specializations', []);

        // Ensure specializations is an array and filter out empty values
        if (is_array($specializations)) {
            $specializations = array_filter($specializations, function ($value) {
                return !empty($value) && is_numeric($value);
            });
            $specializations = array_values($specializations); // Re-index array
        } else {
            $specializations = [];
        }

        if (!empty($specializations)) {
            $employee->specializations()->sync($specializations);
        } else {
            // If no specializations provided, detach all
            $employee->specializations()->detach();
        }

        return redirect()->route('admin.users.staff.show', $user)
            ->with('success', 'تم تحديث الموظف بنجاح');
    }

    /**
     * Delete staff
     */
    public function deleteStaff(User $user)
    {
        if (!$user->isStaff()) {
            abort(404);
        }

        $user->delete();

        return redirect()->route('admin.users.staff')
            ->with('success', 'تم حذف الموظف بنجاح');
    }

    // ==================== Customers Management ====================

    /**
     * Show all customers
     */
    public function customers(Request $request)
    {
        $query = User::where('role', User::ROLE_CUSTOMER);

        // Search
        $searchQuery = $request->get('search', '');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.customers.index', compact('users', 'searchQuery'));
    }

    /**
     * Show create customer form
     */
    public function createCustomer()
    {
        return view('admin.users.customers.create');
    }

    /**
     * Store new customer
     */
    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
        ]);

        // Generate a random password for customer
        $password = Str::random(12);

        User::create([
            'name' => $request->name,
            'email' => $request->email ?? $request->phone . '@customer.local',
            'phone' => $request->phone,
            'role' => User::ROLE_CUSTOMER,
            'password' => Hash::make($password),
            'phone_verified_at' => now(),
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

        return redirect()->route('admin.users.customers')
            ->with('success', 'تم إنشاء العميل بنجاح');
    }

    /**
     * Show customer profile page
     */
    public function showCustomer(User $user)
    {
        if (!$user->isCustomer()) {
            abort(404);
        }

        return view('admin.users.customers.show', compact('user'));
    }

    /**
     * Show edit customer form
     */
    public function editCustomer(User $user)
    {
        if (!$user->isCustomer()) {
            abort(404);
        }

        return view('admin.users.customers.edit', compact('user'));
    }

    /**
     * Update customer
     */
    public function updateCustomer(Request $request, User $user)
    {
        if (!$user->isCustomer()) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'date_of_birth', 'gender']));

        return redirect()->route('admin.users.customers.show', $user)
            ->with('success', 'تم تحديث العميل بنجاح');
    }

    /**
     * Delete customer
     */
    public function deleteCustomer(User $user)
    {
        if (!$user->isCustomer()) {
            abort(404);
        }

        $user->delete();

        return redirect()->route('admin.users.customers')
            ->with('success', 'تم حذف العميل بنجاح');
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user (admin or staff)
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users',
            'role' => 'required|in:admin,staff,customer',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'phone_verified_at' => now(),
        ]);

        return redirect()->route('admin.users', request()->only(['role', 'search']))
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Show user profile page
     */
    public function showUser(User $user)
    {
        $user->load(['employee.specializations']);

        // Get user statistics based on role
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show edit user form
     */
    public function editUser(User $user)
    {
        $user->load(['employee.specializations']);
        $specializations = Specialization::where('is_active', true)->get();
        return view('admin.users.edit', compact('user', 'specializations'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'role' => 'required|in:admin,staff,customer',
            'password' => 'nullable|string|min:8',
            'employee.specializations' => 'nullable|array',
            'employee.specializations.*' => 'exists:specializations,id',
            'employee.bio' => 'nullable|string',
            'employee.hourly_rate' => 'nullable|numeric|min:0',
            'employee.is_available' => 'nullable|boolean',
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'role']));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Update employee info if exists
        if ($user->employee && $request->has('employee')) {
            $user->employee->update([
                'bio' => $request->input('employee.bio'),
                'hourly_rate' => $request->input('employee.hourly_rate'),
                'is_available' => $request->has('employee.is_available') ? true : false,
            ]);

            // Sync specializations
            if ($request->has('employee.specializations')) {
                $user->employee->specializations()->sync($request->input('employee.specializations', []));
            } else {
                $user->employee->specializations()->detach();
            }
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    // ==================== Bookings Management ====================

    /**
     * Show all bookings
     */
    public function bookings(Request $request)
    {
        $query = Booking::with(['customer', 'employee.user', 'service', 'timeSlots']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest('booking_date')->latest('start_time')->paginate(20)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Show booking details
     */
    public function showBooking(Booking $booking)
    {
        $booking->load(['customer', 'employee.user', 'service.subCategory.category', 'timeSlot']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $oldStatus = $booking->status;
        $booking->update(['status' => $request->status]);

        if ($request->status === 'cancelled' && $booking->timeSlot) {
            $booking->timeSlot->update(['is_available' => true]);
        }

        // Send notification if status changed
        if ($oldStatus !== $booking->status) {
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->bookingStatusUpdated($booking->fresh()->load(['customer', 'service', 'employee.user']), $oldStatus);
            } catch (\Exception $e) {
                \Log::error('Failed to send booking status notification: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'تم تحديث حالة الحجز بنجاح');
    }

    /**
     * Update booking payment status
     */
    public function updateBookingPaymentStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_status' => 'required|in:paid,unpaid,refunded',
        ]);

        $booking->update(['payment_status' => $request->payment_status]);

        if ($request->payment_status === 'paid' && $booking->status === 'pending') {
            $booking->update(['status' => 'confirmed']);
        }

        return redirect()->back()->with('success', 'تم تحديث حالة الدفع بنجاح');
    }

    // ==================== Time Slots Management ====================

    /**
     * Show all time slots
     */
    public function timeSlots(Request $request)
    {
        $query = TimeSlot::with('employee.user');

        // Filter by employee
        $employeeFilter = $request->get('employee_id', 'all');
        if ($request->filled('employee_id') && $employeeFilter !== 'all') {
            $query->where('employee_id', $employeeFilter);
        }

        // Filter by date
        $dateFilter = $request->get('date', '');
        if ($request->filled('date')) {
            $query->where('date', $dateFilter);
        }

        $timeSlots = $query->latest('date')->latest('start_time')->paginate(20)->withQueryString();
        $employees = Employee::with('user')->get();

        return view('admin.time-slots.index', compact('timeSlots', 'employeeFilter', 'dateFilter', 'employees'));
    }

    /**
     * Show create time slot form
     */
    public function createTimeSlot()
    {
        $employees = Employee::with('user')->where('is_available', true)->get();
        return view('admin.time-slots.create', compact('employees'));
    }

    /**
     * Store new time slot
     */
    public function storeTimeSlot(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'nullable|boolean',
        ]);

        TimeSlot::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => $request->has('is_available') ? true : false,
        ]);

        return redirect()->route('admin.time-slots')
            ->with('success', 'تم إنشاء الوقت المتاح بنجاح');
    }

    /**
     * Show edit time slot form
     */
    public function editTimeSlot(TimeSlot $timeSlot)
    {
        $timeSlot->load('employee.user');
        $employees = Employee::with('user')->where('is_available', true)->get();
        return view('admin.time-slots.edit', compact('timeSlot', 'employees'));
    }

    /**
     * Update time slot
     */
    public function updateTimeSlot(Request $request, TimeSlot $timeSlot)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'nullable|boolean',
        ]);

        $timeSlot->update([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_available' => $request->has('is_available') ? true : false,
        ]);

        return redirect()->route('admin.time-slots')
            ->with('success', 'تم تحديث الوقت المتاح بنجاح');
    }

    /**
     * Delete time slot
     */
    public function deleteTimeSlot(TimeSlot $timeSlot)
    {
        $timeSlot->delete();
        return redirect()->route('admin.time-slots')
            ->with('success', 'تم حذف الوقت المتاح بنجاح');
    }

    /**
     * Bulk create time slots
     */
    public function bulkCreateTimeSlots(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'in:0,1,2,3,4,5,6',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $created = 0;

        while ($startDate->lte($endDate)) {

            if (in_array($startDate->dayOfWeek, $request->days_of_week)) {

                $dayStart = Carbon::parse(
                    $startDate->format('Y-m-d') . ' ' . $request->start_time
                );

                $dayEnd = Carbon::parse(
                    $startDate->format('Y-m-d') . ' ' . $request->end_time
                );

                // ⬅️ تقسيم الوقت ساعة ساعة
                $period = CarbonPeriod::create($dayStart, '1 hour', $dayEnd->copy()->subHour());

                foreach ($period as $slotStart) {

                    $slotEnd = $slotStart->copy()->addHour();

                    $exists = TimeSlot::where('employee_id', $request->employee_id)
                        ->where('date', $startDate->format('Y-m-d'))
                        ->where('start_time', $slotStart->format('H:i:s'))
                        ->where('end_time', $slotEnd->format('H:i:s'))
                        ->exists();

                    if (!$exists) {
                        TimeSlot::create([
                            'employee_id' => $request->employee_id,
                            'date' => $startDate->format('Y-m-d'),
                            'start_time' => $slotStart->format('H:i:s'),
                            'end_time' => $slotEnd->format('H:i:s'),
                            'is_available' => true,
                        ]);

                        $created++;
                    }
                }
            }

            $startDate->addDay();
        }

        return redirect()->route('admin.time-slots')
            ->with('success', "تم إنشاء {$created} فترة زمنية بنجاح");
    }




    /**
     * Show employee schedules page
     */
    public function employeeSchedules(Request $request)
    {
        $query = EmployeeSchedule::with('employee.user');

        // Filter by employee
        $employeeFilter = $request->get('employee_id', 'all');
        if ($request->filled('employee_id') && $employeeFilter !== 'all') {
            $query->where('employee_id', $employeeFilter);
        }

        $schedules = $query->latest()->paginate(20)->withQueryString();
        $employees = Employee::with('user')->get();

        return view('admin.time-slots.schedules', compact('schedules', 'employeeFilter', 'employees'));
    }

    /**
     * Show create schedule form
     */
    public function createSchedule()
    {
        $employees = Employee::with('user')->where('is_available', true)->get();
        return view('admin.time-slots.create-schedule', compact('employees'));
    }

    /**
     * Store new schedule
     */
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'in:0,1,2,3,4,5,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'nullable|boolean',
        ]);

        $schedule = EmployeeSchedule::create([
            'employee_id' => $request->employee_id,
            'days_of_week' => $request->days_of_week, // Will be converted to JSON by setter
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        // Generate time slots for the next 30 days based on this schedule
        $this->generateTimeSlotsFromSchedule($request->employee_id, $request->days_of_week, $request->start_time, $request->end_time);

        return redirect()->route('admin.time-slots.schedules')
            ->with('success', 'تم إنشاء المواعيد المتكررة بنجاح وتم إنشاء الأوقات المتاحة للـ 30 يوم القادمة');
    }

    /**
     * Generate time slots from schedule
     */
    private function generateTimeSlotsFromSchedule(
        int $employeeId,
        array $daysOfWeek,
        string $startTime,
        string $endTime,
        int $daysAhead = 30
    ) {
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays($daysAhead);
        $created = 0;

        while ($startDate->lte($endDate)) {

            if (in_array($startDate->dayOfWeek, $daysOfWeek)) {

                $dayStart = Carbon::parse(
                    $startDate->format('Y-m-d') . ' ' . $startTime
                );

                $dayEnd = Carbon::parse(
                    $startDate->format('Y-m-d') . ' ' . $endTime
                );

                // ⬅️ تقسيم ساعة ساعة
                $period = CarbonPeriod::create(
                    $dayStart,
                    '1 hour',
                    $dayEnd->copy()->subHour()
                );

                foreach ($period as $slotStart) {

                    $slotEnd = $slotStart->copy()->addHour();

                    $exists = TimeSlot::where('employee_id', $employeeId)
                        ->where('date', $startDate->format('Y-m-d'))
                        ->where('start_time', $slotStart->format('H:i:s'))
                        ->where('end_time', $slotEnd->format('H:i:s'))
                        ->exists();

                    if (!$exists) {
                        TimeSlot::create([
                            'employee_id' => $employeeId,
                            'date' => $startDate->format('Y-m-d'),
                            'start_time' => $slotStart->format('H:i:s'),
                            'end_time' => $slotEnd->format('H:i:s'),
                            'is_available' => true,
                        ]);

                        $created++;
                    }
                }
            }

            $startDate->addDay();
        }

        return $created;
    }


    /**
     * Show edit schedule form
     */
    public function editSchedule(EmployeeSchedule $schedule)
    {
        $schedule->load('employee.user');
        $employees = Employee::with('user')->where('is_available', true)->get();
        return view('admin.time-slots.edit-schedule', compact('schedule', 'employees'));
    }

    /**
     * Update schedule
     */
    public function updateSchedule(Request $request, EmployeeSchedule $schedule)
    {
        $request->validate([
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'in:0,1,2,3,4,5,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'nullable|boolean',
        ]);

        $schedule->update([
            'days_of_week' => $request->days_of_week, // Will be converted to JSON by setter
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        // Regenerate time slots for the next 30 days
        $this->generateTimeSlotsFromSchedule(
            $schedule->employee_id,
            $request->days_of_week,
            $request->start_time,
            $request->end_time
        );

        return redirect()->route('admin.time-slots.schedules')
            ->with('success', 'تم تحديث المواعيد المتكررة بنجاح');
    }

    /**
     * Delete schedule
     */
    public function deleteSchedule(EmployeeSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.time-slots.schedules')
            ->with('success', 'تم حذف المواعيد المتكررة بنجاح');
    }

    /**
     * Generate time slots for all active schedules (can be called via cron)
     */
    public function generateTimeSlotsForAllSchedules()
    {
        $schedules = EmployeeSchedule::where('is_active', true)->with('employee')->get();
        $totalCreated = 0;

        foreach ($schedules as $schedule) {
            if ($schedule->employee && $schedule->employee->is_available) {
                $daysOfWeek = $schedule->days_of_week_array;

                $created = $this->generateTimeSlotsFromSchedule(
                    $schedule->employee_id,
                    $daysOfWeek,
                    $schedule->start_time,
                    $schedule->end_time,
                    30
                );
                $totalCreated += $created;
            }
        }

        return $totalCreated;
    }

    // ==================== Specializations Management ====================

    /**
     * Show all specializations
     */
    public function specializations(Request $request)
    {
        $query = Specialization::query();

        // Search
        $searchQuery = $request->get('search', '');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $specializations = $query->latest()->paginate(20)->withQueryString();

        return view('admin.specializations.index', compact('specializations', 'searchQuery'));
    }

    /**
     * Show create specialization form
     */
    public function createSpecialization()
    {
        return view('admin.specializations.create');
    }

    /**
     * Store new specialization
     */
    public function storeSpecialization(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specializations,name',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Specialization::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.specializations')
            ->with('success', 'تم إنشاء التخصص بنجاح');
    }

    /**
     * Show edit specialization form
     */
    public function editSpecialization(Specialization $specialization)
    {
        return view('admin.specializations.edit', compact('specialization'));
    }

    /**
     * Update specialization
     */
    public function updateSpecialization(Request $request, Specialization $specialization)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:specializations,name,' . $specialization->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $specialization->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.specializations')
            ->with('success', 'تم تحديث التخصص بنجاح');
    }

    /**
     * Delete specialization
     */
    public function deleteSpecialization(Specialization $specialization)
    {
        $specialization->delete();
        return redirect()->route('admin.specializations')
            ->with('success', 'تم حذف التخصص بنجاح');
    }

    // ==================== Tickets Management ====================

    /**
     * Show all tickets
     */
    public function tickets(Request $request)
    {
        $query = \App\Models\Ticket::with(['user', 'assignedUser', 'latestMessage']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->latest()->paginate(20)->withQueryString();

        return view('admin.tickets.index', compact('tickets'));
    }

    /**
     * Show ticket details
     */
    public function showTicket(\App\Models\Ticket $ticket)
    {
        $ticket->load([
            'user',
            'assignedUser',
            'messages.user',
            'messages.attachments',
            'attachments',
        ]);

        return view('admin.tickets.show', compact('ticket'));
    }

    /**
     * Update ticket status
     */
    public function updateTicketStatus(Request $request, \App\Models\Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket->update([
            'status' => $request->status,
            'assigned_to' => $request->assigned_to ?? $ticket->assigned_to,
            'resolved_at' => $request->status === 'resolved' ? now() : null,
        ]);

        return back()->with('success', 'تم تحديث حالة التذكرة بنجاح');
    }

}
