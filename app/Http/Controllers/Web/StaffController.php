<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Employee;
use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:staff,admin');
    }

    /**
     * Show staff dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        $employee = $user->employee;

        // Get bookings for the employee (if admin has employee record, show their bookings)
        if (!$employee) {
            // If no employee record, show empty bookings
            $bookings = collect([]);
            $totalBookings = 0;
            $todayBookings = 0;
            $pendingBookings = 0;
        } else {
            $bookings = Booking::with(['customer', 'service.subCategory.category', 'consultation.category'])
                ->where('employee_id', $employee->id)
                ->latest()
                ->limit(10)
                ->get();

            $totalBookings = Booking::where('employee_id', $employee->id)->count();
            $todayBookings = Booking::where('employee_id', $employee->id)
                ->whereDate('booking_date', Carbon::today())
                ->count();
            $pendingBookings = Booking::where('employee_id', $employee->id)
                ->where('status', 'pending')
                ->count();
        }

        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'total_bookings' => $totalBookings,
            'today_bookings' => $todayBookings,
            'pending_bookings' => $pendingBookings,
        ];

        return view('staff.dashboard', compact('stats', 'bookings'));
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->update(['status' => $request->status]);

        // If status is cancelled, make time slot available again
        if ($request->status === 'cancelled' && $booking->timeSlot) {
            $booking->timeSlot->update(['is_available' => true]);
        }

        // If status changed to something else from cancelled, make time slot unavailable
        if ($booking->wasChanged('status') && $booking->getOriginal('status') === 'cancelled' && $request->status !== 'cancelled' && $booking->timeSlot) {
            $booking->timeSlot->update(['is_available' => false]);
        }

        return back()->with('success', 'تم تحديث حالة الحجز بنجاح');
    }

    /**
     * Show all customers
     */
    public function customers()
    {
        $customers = User::where('role', 'customer')->latest()->paginate(20);
        return view('staff.customers.index', compact('customers'));
    }

    /**
     * Show customer details
     */
    public function showCustomer(User $customer)
    {
        if (!$customer->isCustomer()) {
            return redirect()->route('staff.customers')
                ->with('error', 'المستخدم المطلوب ليس عميلاً');
        }

        return view('staff.customers.show', compact('customer'));
    }

    /**
     * Show my bookings
     */
    public function myBookings()
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('staff.dashboard')
                ->with('error', 'ملف الموظف غير موجود');
        }

        $bookings = Booking::with(['customer', 'service.subCategory.category', 'consultation.category', 'timeSlots'])
            ->where('employee_id', $employee->id)
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(15);

        // Update statuses automatically for all bookings
        foreach ($bookings as $booking) {
            $booking->updateStatusAutomatically();
        }

        // Refresh bookings to get updated statuses
        $bookings->load(['customer', 'service.subCategory.category', 'consultation.category', 'timeSlots']);

        return view('staff.bookings.index', compact('bookings'));
    }

    /**
     * Show booking details
     */
    public function showBooking(Booking $booking)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('staff.dashboard')
                ->with('error', 'ملف الموظف غير موجود');
        }

        // Check if booking belongs to this employee
        if ($booking->employee_id !== $employee->id) {
            return redirect()->route('staff.my-bookings')
                ->with('error', 'ليس لديك صلاحية للوصول لهذا الحجز');
        }

        if ($booking->booking_type === 'consultation') {
            $booking->load(['customer', 'consultation.category', 'timeSlot']);
        } else {
            $booking->load(['customer', 'service.subCategory.category', 'timeSlot']);
        }
        
        // Update status automatically
        $booking->updateStatusAutomatically();
        $booking->refresh();

        return view('staff.bookings.show', compact('booking'));
    }

    /**
     * Show my work days/schedule
     */
    public function mySchedule(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return redirect()->route('staff.dashboard')
                ->with('error', 'ملف الموظف غير موجود');
        }

        $query = $employee->timeSlots()
            ->where('date', '>=', Carbon::today());

        $selectedDate = null;
        $selectedDayNames = [];

        if ($request->has('day_names') && is_array($request->day_names)) {
            $selectedDayNames = $request->day_names;
            // Build placeholders for whereRaw
            $placeholders = implode(',', array_fill(0, count($selectedDayNames), '?'));
            $query->whereRaw("DAYNAME(date) IN ($placeholders)", $selectedDayNames);
        } elseif ($request->has('date')) {
            $selectedDate = Carbon::parse($request->input('date'));
            $query->whereDate('date', $selectedDate);
        } else {
            // Default: Show today
            $selectedDate = Carbon::today();
            $query->whereDate('date', $selectedDate);
        }

        $timeSlots = $query->orderBy('date')->orderBy('start_time')->get();

        // Get all future dates having slots for the calendar and checkboxes
        $allWorkDates = $employee->timeSlots()
            ->where('date', '>=', Carbon::today())
            ->pluck('date');

        $workDays = $allWorkDates
            ->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })
            ->unique()
            ->values()
            ->toArray();

        // Calculate unique weekdays for the checkboxes
        $weekDays = $allWorkDates->map(function ($date) {
            $d = Carbon::parse($date);
            return [
                'name_en' => $d->englishDayOfWeek, // For value
                'name_ar' => $d->locale('ar')->dayName // For display
            ];
        })->unique('name_en')->sortBy('name_en')->values();

        return view('staff.schedule.index', compact('timeSlots', 'workDays', 'weekDays', 'selectedDate', 'selectedDayNames'));
    }

    /**
     * Get tickets for staff (AJAX)
     */
    public function getTickets(Request $request)
    {
        $user = auth()->user();

        $query = Ticket::where('user_id', $user->id)
            ->with(['assignedUser', 'messages' => function($q) {
                $q->latest()->limit(1);
            }]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        // Always return JSON for AJAX requests
        if ($request->ajax() || $request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tickets,
            ]);
        }

        return $tickets;
    }
}
