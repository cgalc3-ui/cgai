<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Requests\CancelBookingRequest;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\ServiceDuration;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        $service = Service::findOrFail($request->service_id);
        $serviceDuration = ServiceDuration::findOrFail($request->service_duration_id);

        if ($serviceDuration->service_id !== $service->id) {
            return response()->json([
                'success' => false,
                'message' => 'مدة الخدمة لا تنتمي للخدمة المختارة',
            ], 422);
        }

        if (!$service->specialization_id) {
            return response()->json([
                'success' => false,
                'message' => 'الخدمة لا تحتوي على تخصص',
            ], 422);
        }

        $bookingDate = Carbon::parse($request->booking_date);
        $startTime = Carbon::parse($request->start_time);
        
        $durationInHours = $this->calculateDurationInHours($serviceDuration);
        $endTime = $startTime->copy()->addHours($durationInHours);

        $employee = $this->findAvailableEmployee(
            $service->specialization_id,
            $bookingDate->format('Y-m-d'),
            $startTime->format('H:i:s'),
            $endTime->format('H:i:s')
        );

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد موظف متاح للتخصص المختار في هذا الوقت',
            ], 422);
        }

        $timeSlot = TimeSlot::where('employee_id', $employee->id)
            ->where('date', $bookingDate->format('Y-m-d'))
            ->where('start_time', $startTime->format('H:i:s'))
            ->where('is_available', true)
            ->first();

        if (!$timeSlot) {
            $timeSlot = TimeSlot::create([
                'employee_id' => $employee->id,
                'date' => $bookingDate,
                'start_time' => $startTime->format('H:i:s'),
                'end_time' => $endTime->format('H:i:s'),
                'is_available' => true,
            ]);
        } elseif (!$timeSlot->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'الوقت المختار غير متاح',
            ], 422);
        }

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'service_id' => $service->id,
            'service_duration_id' => $serviceDuration->id,
            'time_slot_id' => $timeSlot->id,
            'booking_date' => $bookingDate,
            'start_time' => $timeSlot->start_time,
            'end_time' => $timeSlot->end_time,
            'total_price' => $serviceDuration->price,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحجز بنجاح',
            'data' => $booking->load(['service', 'employee.user', 'timeSlot', 'serviceDuration']),
        ], 201);
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
            ->with(['service.subCategory.category', 'employee.user', 'timeSlot', 'serviceDuration']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings,
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
            'specialization_id' => 'required|exists:specializations,id',
            'date' => 'required|date|after_or_equal:today',
            'service_duration_id' => 'required|exists:service_durations,id',
        ]);

        $specializationId = $request->specialization_id;
        $date = Carbon::parse($request->date)->format('Y-m-d');
        $serviceDuration = ServiceDuration::findOrFail($request->service_duration_id);
        $durationInHours = $this->calculateDurationInHours($serviceDuration);

        $employees = Employee::where('is_available', true)
            ->whereHas('specializations', function ($query) use ($specializationId) {
                $query->where('specializations.id', $specializationId);
            })
            ->get();

        $availableSlots = [];

        foreach ($employees as $employee) {
            $timeSlots = $employee->timeSlots()
                ->where('date', $date)
                ->where('is_available', true)
                ->orderBy('start_time')
                ->get();

            foreach ($timeSlots as $slot) {
                $slotStartTime = Carbon::parse($slot->start_time);
                $slotEndTime = Carbon::parse($slot->end_time);
                $calculatedEndTime = $slotStartTime->copy()->addHours($durationInHours);

                if ($calculatedEndTime->lte($slotEndTime)) {
                    if ($employee->isAvailableForTimeSlot($slot->id, $date, $slotStartTime->format('H:i:s'), $calculatedEndTime->format('H:i:s'))) {
                        $availableSlots[] = [
                            'id' => $slot->id,
                            'employee' => [
                                'id' => $employee->id,
                                'name' => $employee->user->name,
                            ],
                            'start_time' => $slotStartTime->format('H:i'),
                            'end_time' => $calculatedEndTime->format('H:i'),
                            'date' => $date,
                        ];
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $availableSlots,
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

        return response()->json([
            'success' => true,
            'data' => $booking->load(['service.subCategory.category', 'employee.user', 'timeSlot', 'serviceDuration']),
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
            'data' => $booking->fresh()->load(['service.subCategory.category', 'employee.user', 'timeSlot', 'serviceDuration']),
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
            'data' => $booking->fresh()->load(['service.subCategory.category', 'employee.user', 'timeSlot', 'serviceDuration']),
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
            'data' => $booking->fresh()->load(['service.subCategory.category', 'employee.user', 'timeSlot', 'serviceDuration']),
        ]);
    }

    /**
     * Find available employee for specialization and time
     */
    private function findAvailableEmployee($specializationId, $date, $startTime, $endTime)
    {
        $employees = Employee::where('is_available', true)
            ->whereHas('specializations', function ($query) use ($specializationId) {
                $query->where('specializations.id', $specializationId);
            })
            ->get();

        foreach ($employees as $employee) {
            if ($employee->isAvailableForTimeSlot(null, $date, $startTime, $endTime)) {
                return $employee;
            }
        }

        return null;
    }

    /**
     * Calculate duration in hours from service duration
     */
    private function calculateDurationInHours($serviceDuration): float
    {
        switch ($serviceDuration->duration_type) {
            case 'hour':
                return $serviceDuration->duration_value;
            case 'day':
                return $serviceDuration->duration_value * 24;
            case 'week':
                return $serviceDuration->duration_value * 24 * 7;
            default:
                return 1;
        }
    }
}
