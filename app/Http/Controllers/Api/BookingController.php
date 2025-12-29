<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Requests\CancelBookingRequest;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\TimeSlot;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BookingController extends Controller
{
    public function store(StoreBookingRequest $request)
    {
        // استخدام معاملة (Transaction) لضمان أن جميع العمليات تنجح معًا أو تفشل معًا
        return DB::transaction(function () use ($request) {
            $customer = $request->user();

            // 1. التحقق من صلاحيات المستخدم
            if (!$customer->isCustomer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ليس لديك صلاحية للوصول',
                ], 403);
            }

            // 2. جلب والتحقق من الخدمة
            $service = Service::findOrFail($request->service_id);

            if (!$service->specialization_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'الخدمة المختارة لا تحتوي على تخصص محدد.',
                ], 422);
            }

            $hourlyRate = $service->getHourlyRate();
            if (!$hourlyRate) {
                return response()->json([
                    'success' => false,
                    'message' => 'الخدمة المختارة لا تحتوي على سعر ساعة محدد.',
                ], 422);
            }

            // 3. التحقق من الوقت المحددة (Time Slots)
            $requestedSlotIds = $request->time_slot_ids;
            $requestedBookingDate = $request->booking_date;

            // جلب كل الوقت المحددة من قاعدة البيانات
            $timeSlots = TimeSlot::whereIn('id', $requestedSlotIds)->lockForUpdate()->get(); // lockForUpdate لمنع التعديل المتزامن

            // التحقق من أن جميع المعرفات المطلوبة موجودة
            if ($timeSlots->count() !== count($requestedSlotIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'واحد أو أكثر من معرفات الوقت المحددة غير صالحة.',
                ], 422);
            }

            // التحقق من أن جميع الوقت المحددة متاحة
            if ($timeSlots->where('is_available', false)->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بعض الوقت المحددة لم تعد متاحة. يرجى تحديث الصفحة والمحاولة مرة أخرى.',
                ], 422);
            }

            // التحقق من أن جميع الوقت المحددة لنفس الموظف
            $uniqueEmployees = $timeSlots->pluck('employee_id')->unique();
            if ($uniqueEmployees->count() > 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب أن تكون جميع المدد الزمنية المختارة لنفس الموظف.',
                ], 422);
            }
            $employeeId = $uniqueEmployees->first();

            // التحقق من أن جميع الوقت المحددة في نفس تاريخ الحجز المطلوب
            if ($timeSlots->first(fn($slot) => $slot->date->format('Y-m-d') !== Carbon::parse($requestedBookingDate)->format('Y-m-d'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'المدد الزمنية المختارة لا تتطابق مع تاريخ الحجز المحدد.',
                ], 422);
            }

            // 4. التحقق من أن الموظف لديه التخصص المطلوب
            $employee = Employee::find($employeeId);
            if (!$employee || !$employee->specializations()->where('specialization_id', $service->specialization_id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'الموظف المتاح لا يقدم التخصص المطلوب لهذه الخدمة.',
                ], 422);
            }

            // 5. حساب المدة الإجمالية والسعر الإجمالي
            $timeSlots = $timeSlots->sortBy('start_time');
            $totalDurationInHours = 0;
            foreach ($timeSlots as $slot) {
                $start = Carbon::parse($slot->start_time);
                $end = Carbon::parse($slot->end_time);
                $durationInMinutes = $end->diffInMinutes($start, false); // false للحصول على القيمة المطلقة
                if ($durationInMinutes < 0) {
                    // إذا كانت المدة سالبة، نستخدم القيمة المطلقة
                    $durationInMinutes = abs($durationInMinutes);
                }
                $totalDurationInHours += $durationInMinutes / 60.0;
            }

            // التأكد من أن المدة والسعر إيجابية
            if ($totalDurationInHours <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'المدة الزمنية للحجز غير صالحة',
                ], 422);
            }

            if ($hourlyRate <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'سعر الساعة للخدمة غير صالح',
                ], 422);
            }

            $totalPrice = $totalDurationInHours * $hourlyRate;
            
            // التأكد من أن السعر الإجمالي موجب
            if ($totalPrice <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'السعر الإجمالي المحسوب غير صالح',
                ], 422);
            }


            // 7. تحديث حالة الوقت المحددة إلى غير متاحة
            TimeSlot::whereIn('id', $requestedSlotIds)->update(['is_available' => false]);

            // 8. إنشاء الحجز
            $firstSlot = $timeSlots->first();
            $lastSlot = $timeSlots->last();

            $booking = Booking::create([
                'customer_id' => $customer->id,
                'employee_id' => $employeeId,
                'service_id' => $service->id,
                // يمكن تخزين معرف أول وقت كمرجع
                'time_slot_id' => $firstSlot->id,
                'booking_date' => $requestedBookingDate,
                'start_time' => $firstSlot->start_time,
                'end_time' => $lastSlot->end_time,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => $request->notes,
            ]);

            // 9. ربط الحجز بجميع الوقت المحددة (علاقة many-to-many)
            $booking->timeSlots()->attach($requestedSlotIds);

            // 10. إرسال الإشعارات
            try {
                $notificationService = app(NotificationService::class);
                $notificationService->bookingCreated($booking->fresh()->load(['customer', 'service', 'employee.user']));
            } catch (\Exception $e) {
                // Log error but don't fail the booking creation
                \Log::error('Failed to send booking notification: ' . $e->getMessage());
            }

            // 11. إرجاع الاستجابة (بيانات الدفع فقط)
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الحجز بنجاح',
                'data' => [
                    'id' => $booking->id,
                    'total_price' => $booking->total_price,
                ],
            ], 201);
        });
    }

    public function index(Request $request)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        $query = Booking::where('customer_id', $customer->id)
            ->with(['service', 'employee.user', 'timeSlots']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        // Format the response to include only required data
        $formattedBookings = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'employee_id' => $booking->employee_id,
                'service_id' => $booking->service_id,
                'booking_date' => $booking->booking_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'total_price' => $booking->total_price,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'payment_id' => $booking->payment_id,
                'payment_data' => $booking->payment_data,
                'paid_at' => $booking->paid_at,
                'notes' => $booking->notes,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
                'service' => $booking->service ? [
                    'id' => $booking->service->id,
                    'name' => $booking->service->name,
                    'description' => $booking->service->description,
                    'price' => $booking->service->price,
                ] : null,
                'employee' => $booking->employee && $booking->employee->user ? [
                    'name' => $booking->employee->user->name,
                ] : null,
                'time_slots' => $booking->timeSlots->map(function ($timeSlot) {
                    return [
                        'id' => $timeSlot->id,
                        'date' => $timeSlot->date,
                        'start_time' => $timeSlot->start_time,
                        'end_time' => $timeSlot->end_time,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedBookings,
        ]);
    }

    /**
     * Get available dates for a service
     */
    public function availableDates(Request $request)
    {
        $customer = $request->user();

        if (!$customer || !$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول للوصول',
            ], 401);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $service = Service::findOrFail($request->service_id);

        if (!$service->specialization_id) {
            return response()->json([
                'success' => false,
                'message' => 'الخدمة المختارة لا تحتوي على تخصص',
            ], 422);
        }

        // الحصول على سعر الساعة (من hourly_rate أو من ServiceDuration)
        $hourlyRate = $service->getHourlyRate();

        if (!$hourlyRate) {
            return response()->json([
                'success' => false,
                'message' => 'الخدمة المختارة لا تحتوي على سعر الساعة. يرجى إضافة سعر الساعة للخدمة أو إضافة مدة خدمة بساعة واحدة.',
            ], 422);
        }

        $specializationId = $service->specialization_id;
        // المدة ثابتة: ساعة واحدة
        $durationInHours = 1;

        // Get employees with this specialization (with user data)
        $employeesWithUser = Employee::where('is_available', true)
            ->whereHas('specializations', function ($query) use ($specializationId) {
                $query->where('specializations.id', $specializationId);
            })
            ->with('user')
            ->get();

        $employeesIds = $employeesWithUser->isEmpty() ? [] : $employeesWithUser->pluck('id')->toArray();

        // Get all dates for the next 30 days (even if no employees available)
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(30); // Next 30 days
        $availableDates = [];

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');

            // Get all time slots for this date (only if there are employees)
            $allTimeSlots = collect([]);
            if (!empty($employeesIds)) {
                $allTimeSlots = TimeSlot::whereIn('employee_id', $employeesIds)
                    ->where('date', $dateStr)
                    ->orderBy('start_time')
                    ->get();
            }

            // إنشاء قائمة بالأوقات كل ساعة (من 8 صباحاً إلى 8 مساءً)
            $timeSlots = [];
            $startHour = 10; // 8 AM
            $endHour = 18; // 8 PM
            $hasAvailableSlots = false;

            for ($hour = $startHour; $hour < $endHour; $hour++) {
                $startTime = Carbon::createFromTime($hour, 0, 0);
                $endTime = $startTime->copy()->addHour();

                $startTimeStr = $startTime->format('H:i');
                $endTimeStr = $endTime->format('H:i');
                $startTimeFull = $startTime->format('H:i:s');
                $endTimeFull = $endTime->format('H:i:s');

                // البحث عن time slots التي تغطي هذا الوقت
                $isAvailable = false;
                $timeSlotId = null;


                // فقط إذا كان هناك موظفين متاحين
                if (!$employeesWithUser->isEmpty()) {
                    foreach ($employeesWithUser as $employee) {
                        // البحث عن time slot يغطي هذا الوقت
                        $matchingSlot = $allTimeSlots->first(function ($slot) use ($employee, $startTimeFull, $endTimeFull) {
                            if ($slot->employee_id !== $employee->id) {
                                return false;
                            }

                            $slotStart = Carbon::parse($slot->start_time);
                            $slotEnd = Carbon::parse($slot->end_time);
                            $requestStart = Carbon::parse($startTimeFull);
                            $requestEnd = Carbon::parse($endTimeFull);

                            // التحقق من أن time slot يغطي الوقت المطلوب
                            return $slotStart->lte($requestStart) && $slotEnd->gte($requestEnd);
                        });

                        if ($matchingSlot) {
                            // التحقق من أن time slot متاح
                            $slotIsAvailable = $matchingSlot->is_available &&
                                $employee->isAvailableForTimeSlot(
                                    $matchingSlot->id,
                                    $dateStr,
                                    $startTimeFull,
                                    $endTimeFull
                                );

                            if ($slotIsAvailable) {
                                $isAvailable = true;
                                $hasAvailableSlots = true;
                                $timeSlotId = $matchingSlot->id;
                                break; // وجدنا موظف متاح، لا حاجة للبحث أكثر
                            }
                        }
                    }
                }

                $timeSlots[] = [
                    'time_slot_id' => $timeSlotId,
                    'start_time' => $startTimeStr,
                    'end_time' => $endTimeStr,
                    'is_available' => $isAvailable,
                ];
            }

            // إضافة التاريخ دائماً (حتى لو لم يكن هناك موظفين متاحين)
            $dateData = [
                'date' => $dateStr,
                'formatted_date' => $currentDate->format('Y-m-d'),
                'day_name' => $this->getDayNameArabic($currentDate->dayOfWeek),
                'time_slots' => $timeSlots,
            ];

            // إضافة رسالة إذا لم يكن هناك مواعيد متاحة
            if (!$hasAvailableSlots) {
                $dateData['message'] = 'لا يوجد مواعيد متاحة';
            }

            $availableDates[] = $dateData;

            $currentDate->addDay();
        }

        return response()->json([
            'success' => true,
            'data' => $availableDates,
        ]);
    }

    public function availableTimeSlots(Request $request)
    {
        $customer = $request->user();

        if (!$customer || !$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول للوصول',
            ], 401);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $service = Service::findOrFail($request->service_id);

        if (!$service->specialization_id) {
            return response()->json([
                'success' => false,
                'message' => 'الخدمة المختارة لا تحتوي على تخصص',
            ], 422);
        }

        // الحصول على سعر الساعة (من hourly_rate أو من ServiceDuration)
        $hourlyRate = $service->getHourlyRate();

        if (!$hourlyRate) {
            return response()->json([
                'success' => false,
                'message' => 'الخدمة المختارة لا تحتوي على سعر الساعة. يرجى إضافة سعر الساعة للخدمة أو إضافة مدة خدمة بساعة واحدة.',
            ], 422);
        }

        $date = Carbon::parse($request->date)->format('Y-m-d');
        $specializationId = $service->specialization_id;
        // المدة ثابتة: ساعة واحدة
        $durationInHours = 1;

        $employees = Employee::where('is_available', true)
            ->whereHas('specializations', function ($query) use ($specializationId) {
                $query->where('specializations.id', $specializationId);
            })
            ->with('user')
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        // Get all time slots for this date
        $allTimeSlots = TimeSlot::whereIn('employee_id', $employees->pluck('id'))
            ->where('date', $date)
            ->orderBy('start_time')
            ->get();

        // إنشاء قائمة بالأوقات كل ساعة (من 8 صباحاً إلى 8 مساءً)
        $timeSlots = [];
        $startHour = 8; // 8 AM
        $endHour = 20; // 8 PM

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $startTime = Carbon::createFromTime($hour, 0, 0);
            $endTime = $startTime->copy()->addHour();

            $startTimeStr = $startTime->format('H:i');
            $endTimeStr = $endTime->format('H:i');
            $startTimeFull = $startTime->format('H:i:s');
            $endTimeFull = $endTime->format('H:i:s');

            // البحث عن time slots التي تغطي هذا الوقت
            $availableEmployees = [];
            $isAvailable = false;

            foreach ($employees as $employee) {
                // البحث عن time slot يغطي هذا الوقت
                $matchingSlot = $allTimeSlots->first(function ($slot) use ($employee, $startTimeFull, $endTimeFull) {
                    if ($slot->employee_id !== $employee->id) {
                        return false;
                    }

                    $slotStart = Carbon::parse($slot->start_time);
                    $slotEnd = Carbon::parse($slot->end_time);
                    $requestStart = Carbon::parse($startTimeFull);
                    $requestEnd = Carbon::parse($endTimeFull);

                    // التحقق من أن time slot يغطي الوقت المطلوب
                    return $slotStart->lte($requestStart) && $slotEnd->gte($requestEnd);
                });

                if ($matchingSlot) {
                    // التحقق من أن time slot متاح
                    $slotIsAvailable = $matchingSlot->is_available &&
                        $employee->isAvailableForTimeSlot(
                            $matchingSlot->id,
                            $date,
                            $startTimeFull,
                            $endTimeFull
                        );

                    if ($slotIsAvailable) {
                        $isAvailable = true;
                        $availableEmployees[] = [
                            'id' => $employee->id,
                            'name' => $employee->user->name,
                        ];
                    }
                }
            }

            $timeSlots[] = [
                'start_time' => $startTimeStr,
                'end_time' => $endTimeStr,
                'date' => $date,
                'is_available' => $isAvailable,
                'employees' => $availableEmployees,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $timeSlots,
        ]);
    }

    public function show(Request $request, Booking $booking)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        if ($booking->customer_id !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول لهذا الحجز',
            ], 403);
        }

        $booking->load(['service', 'employee.user', 'timeSlots']);

        // Format the response to include only required data
        $formattedBooking = [
            'id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'employee_id' => $booking->employee_id,
            'service_id' => $booking->service_id,
            'booking_date' => $booking->booking_date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'total_price' => $booking->total_price,
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'payment_id' => $booking->payment_id,
            'payment_data' => $booking->payment_data,
            'paid_at' => $booking->paid_at,
            'notes' => $booking->notes,
            'created_at' => $booking->created_at,
            'updated_at' => $booking->updated_at,
            'service' => $booking->service ? [
                'id' => $booking->service->id,
                'name' => $booking->service->name,
                'description' => $booking->service->description,
                'price' => $booking->service->price,
            ] : null,
            'employee' => $booking->employee && $booking->employee->user ? [
                'name' => $booking->employee->user->name,
            ] : null,
            'time_slots' => $booking->timeSlots->map(function ($timeSlot) {
                return [
                    'id' => $timeSlot->id,
                    'date' => $timeSlot->date,
                    'start_time' => $timeSlot->start_time,
                    'end_time' => $timeSlot->end_time,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $formattedBooking,
        ]);
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        if ($booking->customer_id !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول لهذا الحجز',
            ], 403);
        }

        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تحديث الحجز في هذه الحالة',
            ], 422);
        }

        $booking->update($request->only(['notes']));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحجز بنجاح',
            'data' => $booking->fresh()->load(['service.subCategory.category', 'employee.user', 'timeSlot']),
        ]);
    }

    public function cancel(CancelBookingRequest $request, Booking $booking)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        if ($booking->customer_id !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول لهذا الحجز',
            ], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'الحجز ملغي بالفعل',
            ], 422);
        }

        if ($booking->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إلغاء حجز مكتمل',
            ], 422);
        }

        $booking->update([
            'status' => 'cancelled',
            'notes' => $request->reason ? ($booking->notes ? $booking->notes . "\nسبب الإلغاء: " . $request->reason : "سبب الإلغاء: " . $request->reason) : $booking->notes,
        ]);

        if ($booking->timeSlot) {
            $booking->timeSlot->update(['is_available' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح',
            'data' => $booking->fresh()->load(['service.subCategory.category', 'employee.user', 'timeSlot']),
        ]);
    }

    public function payment(Request $request, Booking $booking)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        if ($booking->customer_id !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول لهذا الحجز',
            ], 403);
        }

        if ($booking->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'تم الدفع بالفعل',
            ], 422);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن الدفع لحجز ملغي',
            ], 422);
        }

        $request->validate([
            'payment_method' => 'required|string|in:cash,credit_card,debit_card,online',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم الدفع بنجاح',
            'data' => $booking->fresh()->load(['service.subCategory.category', 'employee.user', 'timeSlot']),
        ]);
    }

    /**
     * Find available employee for specialization and time
     * يختار أول موظف متاح، ويترك الباقي متاحين للعملاء الآخرين
     */
    private function findAvailableEmployee($specializationId, $date, $startTime, $endTime)
    {
        $employees = Employee::where('is_available', true)
            ->whereHas('specializations', function ($query) use ($specializationId) {
                $query->where('specializations.id', $specializationId);
            })
            ->with('user')
            ->get();

        foreach ($employees as $employee) {
            // البحث عن time slot يغطي الوقت المطلوب
            $timeSlot = TimeSlot::where('employee_id', $employee->id)
                ->where('date', $date)
                ->where('is_available', true)
                ->get()
                ->first(function ($slot) use ($startTime, $endTime) {
                    $slotStart = Carbon::parse($slot->start_time);
                    $slotEnd = Carbon::parse($slot->end_time);
                    $requestStart = Carbon::parse($startTime);
                    $requestEnd = Carbon::parse($endTime);

                    // التحقق من أن time slot يغطي الوقت المطلوب
                    return $slotStart->lte($requestStart) && $slotEnd->gte($requestEnd);
                });

            if ($timeSlot) {
                // التحقق من أن الموظف متاح في هذا الوقت (لا يوجد تعارض مع حجوزات أخرى)
                if ($employee->isAvailableForTimeSlot($timeSlot->id, $date, $startTime, $endTime)) {
                    return $employee;
                }
            }
        }

        return null;
    }

    /**
     * Calculate duration in hours from service duration
     */
    private function calculateDurationInHours($booking): float
    {
        $start = Carbon::parse($booking->start_time);
        $end = Carbon::parse($booking->end_time);
        return $end->diffInMinutes($start) / 60.0;
    }

    /**
     * Get Arabic day name
     */
    private function getDayNameArabic($dayOfWeek): string
    {
        $days = [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];

        return $days[$dayOfWeek] ?? '';
    }
}
