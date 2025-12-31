<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\StoreConsultationBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Requests\CancelBookingRequest;
use App\Models\Booking;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Consultation;
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
            $service = Service::with('subCategory.category')->findOrFail($request->service_id);

            if (!$service->subCategory || !$service->subCategory->category_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'الخدمة المختارة لا تحتوي على فئة محدد.',
                ], 422);
            }

            $categoryId = $service->subCategory->category_id;

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
            // ملاحظة: نسمح بإنشاء حجوزات متعددة لنفس الـ time slots (كلها pending)
            // أول من يدفع يحصل على الحجز، والباقي يحصلون على خطأ عند الدفع
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

            // 4. التحقق من أن الموظف لديه الفئة (التخصص) المطلوبة
            $employee = Employee::find($employeeId);
            if (!$employee || !$employee->categories()->where('categories.id', $categoryId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'الموظف المتاح لا يقدم الفئة المطلوبة لهذه الخدمة.',
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


            // 7. حفظ بيانات الحجز مؤقتاً في cache - لا يتم إنشاء الحجز في قاعدة البيانات إلا بعد الدفع
            $firstSlot = $timeSlots->first();
            $lastSlot = $timeSlots->last();

            // التحقق من أن الـ time slots لا تزال متاحة
            // ملاحظة: لا نحجز الـ time slots هنا - سيتم حجزها بعد الدفع فقط
            if ($timeSlots->where('is_available', false)->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بعض الوقت المحددة لم تعد متاحة. يرجى تحديث الصفحة والمحاولة مرة أخرى.',
                ], 422);
            }

            // حفظ بيانات الحجز مؤقتاً في cache (لمدة 30 دقيقة)
            $tempBookingId = 'temp_' . uniqid() . '_' . time();
            $tempBookingData = [
                'customer_id' => $customer->id,
                'employee_id' => $employeeId,
                'service_id' => $service->id,
                'time_slot_id' => $firstSlot->id,
                'time_slot_ids' => $requestedSlotIds,
                'booking_date' => $requestedBookingDate,
                'start_time' => $firstSlot->start_time,
                'end_time' => $lastSlot->end_time,
                'total_price' => $totalPrice,
                'notes' => $request->notes,
            ];

            // حفظ البيانات في cache
            \Illuminate\Support\Facades\Cache::put(
                'pending_booking_' . $tempBookingId,
                $tempBookingData,
                now()->addMinutes(30)
            );

            // إذا كان الدفع إلكتروني، إنشاء payment_url مباشرة
            if ($request->payment_method === 'online') {
                try {
                    $paymobService = app(\App\Services\PaymobService::class);
                    
                    // Authenticate
                    $auth = $paymobService->authenticate();
                    if (!$auth || empty($auth['token'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'فشل الاتصال بخادم الدفع',
                        ], 503);
                    }

                    // Create Order باستخدام temp_booking_id كـ merchant_order_id
                    $amountCents = (int) ($totalPrice * 100);
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])->timeout(30)->post(config('services.paymob.base_url', 'https://ksa.paymob.com/api') . '/ecommerce/orders', [
                        'auth_token' => $auth['token'],
                        'delivery_needed' => false,
                        'amount_cents' => $amountCents,
                        'currency' => config('services.paymob.currency', 'SAR'),
                        'merchant_order_id' => $tempBookingId,
                        'items' => []
                    ]);

                    if (!$response->successful()) {
                        \Log::error('PayMob Create Order Failed', [
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'فشل إنشاء طلب الدفع',
                        ], 503);
                    }

                    $paymobOrderId = $response->json('id');

                    // Create Payment Key
                    $billingData = [
                        'apartment' => 'NA',
                        'email' => $customer->email ?? 'customer@example.com',
                        'floor' => 'NA',
                        'first_name' => $customer->name ?? 'Customer',
                        'street' => 'NA',
                        'building' => 'NA',
                        'phone_number' => $customer->phone ?? 'NA',
                        'shipping_method' => 'NA',
                        'postal_code' => 'NA',
                        'city' => 'Riyadh',
                        'country' => 'SA',
                        'last_name' => 'NA',
                        'state' => 'NA'
                    ];

                    $paymentKeyResponse = \Illuminate\Support\Facades\Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])->timeout(30)->post(config('services.paymob.base_url', 'https://ksa.paymob.com/api') . '/acceptance/payment_keys', [
                        'auth_token' => $auth['token'],
                        'amount_cents' => $amountCents,
                        'expiration' => 3600,
                        'order_id' => (string) $paymobOrderId,
                        'billing_data' => $billingData,
                        'currency' => config('services.paymob.currency', 'SAR'),
                        'integration_id' => (int) config('services.paymob.integration_id'),
                        'lock_order_when_paid' => true
                    ]);

                    if (!$paymentKeyResponse->successful()) {
                        \Log::error('PayMob Create Payment Key Failed', [
                            'status' => $paymentKeyResponse->status(),
                            'body' => $paymentKeyResponse->body(),
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'فشل الحصول على مفتاح الدفع',
                        ], 503);
                    }

                    $paymentKey = $paymentKeyResponse->json('token');

                    // Get Payment URL
                    $iframeId = config('services.paymob.iframe_id');
                    $paymentUrl = "https://ksa.paymob.com/api/acceptance/iframes/{$iframeId}?payment_token={$paymentKey}";
                    
                    // حفظ paymob_order_id في cache
                    $tempBookingData['paymob_order_id'] = $paymobOrderId;
                    $tempBookingData['payment_key'] = $paymentKey;
                    \Illuminate\Support\Facades\Cache::put(
                        'pending_booking_' . $tempBookingId,
                        $tempBookingData,
                        now()->addMinutes(30)
                    );
                    
                    // إرجاع الاستجابة مع payment_url
                    return response()->json([
                        'success' => true,
                        'message' => 'يرجى إتمام الدفع. سيتم تأكيد الحجز بعد إتمام الدفع.',
                        'data' => [
                            'temp_booking_id' => $tempBookingId,
                            'total_price' => $totalPrice,
                            'payment_url' => $paymentUrl,
                        ],
                    ], 201);
                } catch (\Exception $e) {
                    \Log::error('Failed to create payment URL: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'فشل في إنشاء رابط الدفع: ' . $e->getMessage(),
                    ], 500);
                }
            }

            // للدفع اليدوي، إرجاع temp_booking_id فقط
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ بيانات الحجز. يرجى إتمام الدفع لتأكيد الحجز.',
                'data' => [
                    'temp_booking_id' => $tempBookingId,
                    'total_price' => $totalPrice,
                ],
            ], 201);
        });
    }

    /**
     * Handle online payment initiation (creates payment URL)
     */
    public function initiateOnlinePayment(Request $request)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        $request->validate([
            'temp_booking_id' => 'required|string',
        ]);

        $tempBookingId = $request->temp_booking_id;
        $cacheKey = 'pending_booking_' . $tempBookingId;
        $tempBookingData = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (!$tempBookingData) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الحجز غير موجودة أو انتهت صلاحيتها. يرجى إنشاء حجز جديد.',
            ], 404);
        }

        // التحقق من أن العميل هو صاحب الحجز
        if ($tempBookingData['customer_id'] !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول لهذا الحجز',
            ], 403);
        }

        // إنشاء payment_url للدفع الإلكتروني
        try {
            $paymobService = app(\App\Services\PaymobService::class);
            
            // Authenticate
            $auth = $paymobService->authenticate();
            if (!$auth || empty($auth['token'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل الاتصال بخادم الدفع',
                ], 503);
            }

            // Create Order باستخدام temp_booking_id كـ merchant_order_id
            $amountCents = (int) ($tempBookingData['total_price'] * 100);
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post(config('services.paymob.base_url', 'https://ksa.paymob.com/api') . '/ecommerce/orders', [
                'auth_token' => $auth['token'],
                'delivery_needed' => false,
                'amount_cents' => $amountCents,
                'currency' => config('services.paymob.currency', 'SAR'),
                'merchant_order_id' => $tempBookingId,
                'items' => []
            ]);

            if (!$response->successful()) {
                \Log::error('PayMob Create Order Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'فشل إنشاء طلب الدفع',
                ], 503);
            }

            $paymobOrderId = $response->json('id');

            // Create Payment Key
            $billingData = [
                'apartment' => 'NA',
                'email' => $customer->email ?? 'customer@example.com',
                'floor' => 'NA',
                'first_name' => $customer->name ?? 'Customer',
                'street' => 'NA',
                'building' => 'NA',
                'phone_number' => $customer->phone ?? 'NA',
                'shipping_method' => 'NA',
                'postal_code' => 'NA',
                'city' => 'Riyadh',
                'country' => 'SA',
                'last_name' => 'NA',
                'state' => 'NA'
            ];

            $paymentKeyResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(30)->post(config('services.paymob.base_url', 'https://ksa.paymob.com/api') . '/acceptance/payment_keys', [
                'auth_token' => $auth['token'],
                'amount_cents' => $amountCents,
                'expiration' => 3600,
                'order_id' => (string) $paymobOrderId,
                'billing_data' => $billingData,
                'currency' => config('services.paymob.currency', 'SAR'),
                'integration_id' => (int) config('services.paymob.integration_id'),
                'lock_order_when_paid' => true
            ]);

            if (!$paymentKeyResponse->successful()) {
                \Log::error('PayMob Create Payment Key Failed', [
                    'status' => $paymentKeyResponse->status(),
                    'body' => $paymentKeyResponse->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'فشل الحصول على مفتاح الدفع',
                ], 503);
            }

            $paymentKey = $paymentKeyResponse->json('token');

            // Get Payment URL
            $iframeId = config('services.paymob.iframe_id');
            $paymentUrl = "https://ksa.paymob.com/api/acceptance/iframes/{$iframeId}?payment_token={$paymentKey}";
            
            // حفظ paymob_order_id في cache
            $tempBookingData['paymob_order_id'] = $paymobOrderId;
            $tempBookingData['payment_key'] = $paymentKey;
            \Illuminate\Support\Facades\Cache::put(
                $cacheKey,
                $tempBookingData,
                now()->addMinutes(30)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'يرجى إتمام الدفع. سيتم تأكيد الحجز بعد إتمام الدفع.',
                'data' => [
                    'temp_booking_id' => $tempBookingId,
                    'total_price' => $tempBookingData['total_price'],
                    'payment_url' => $paymentUrl,
                ],
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Failed to create payment URL: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل في إنشاء رابط الدفع: ' . $e->getMessage(),
            ], 500);
        }
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

        $service = Service::with('subCategory.category')->findOrFail($request->service_id);

        if (!$service->subCategory || !$service->subCategory->category_id) {
            return response()->json([
                'success' => false,
                'message' => 'الخدمة المختارة لا تحتوي على فئة',
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

        $categoryId = $service->subCategory->category_id;

        // Get employees with this category (with user data)
        $employeesWithUser = Employee::where('is_available', true)
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
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

            // إنشاء قائمة بالأوقات كل ساعة (من 10 صباحاً إلى 6 مساءً)
            $timeSlots = [];
            $startHour = 10;
            $endHour = 18;
            $hasAvailableSlots = false;

            // للخدمات: استخدام الأوقات كل ساعة
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

        $service = Service::with('subCategory.category')->findOrFail($request->service_id);

        if (!$service->subCategory || !$service->subCategory->category_id) {
            return response()->json([
                'success' => false,
                'message' => 'الخدمة المختارة لا تحتوي على فئة',
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

        $categoryId = $service->subCategory->category_id;

        $date = Carbon::parse($request->date)->format('Y-m-d');

        $employees = Employee::where('is_available', true)
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
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

        // إنشاء قائمة بالأوقات كل ساعة (من 10 صباحاً إلى 6 مساءً)
        $timeSlots = [];
        $startHour = 10;
        $endHour = 18;

        // للخدمات: استخدام الأوقات كل ساعة
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

    /**
     * Get available dates for a consultation
     */
    public function availableConsultationDates(Request $request)
    {
        $customer = $request->user();

        if (!$customer || !$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول للوصول',
            ], 401);
        }

        $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
        ]);

        $consultation = Consultation::with('category')->findOrFail($request->consultation_id);

        if (!$consultation->category_id) {
            return response()->json([
                'success' => false,
                'message' => 'الاستشارة المختارة لا تحتوي على فئة',
            ], 422);
        }

        $categoryId = $consultation->category_id;

        // Get employees with this category (with user data)
        $employeesWithUser = Employee::where('is_available', true)
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
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

            // إنشاء قائمة بالأوقات
            $timeSlots = [];
            $startHour = 10;
            $endHour = 18;
            $hasAvailableSlots = false;

            // للاستشارات: استخدام time slots الموجودة مباشرة
            if (!$allTimeSlots->isEmpty()) {
                foreach ($allTimeSlots as $slot) {
                    $slotStart = Carbon::parse($slot->start_time);
                    $slotEnd = Carbon::parse($slot->end_time);
                    
                    $startTimeStr = $slotStart->format('H:i');
                    $endTimeStr = $slotEnd->format('H:i');
                    $startTimeFull = $slotStart->format('H:i:s');
                    $endTimeFull = $slotEnd->format('H:i:s');

                    // البحث عن موظف متاح لهذا time slot
                    $isAvailable = false;
                    $timeSlotId = null;

                    if (!$employeesWithUser->isEmpty()) {
                        foreach ($employeesWithUser as $employee) {
                            if ($slot->employee_id === $employee->id) {
                                // التحقق من أن time slot متاح
                                $slotIsAvailable = $slot->is_available &&
                                    $employee->isAvailableForTimeSlot(
                                        $slot->id,
                                        $dateStr,
                                        $startTimeFull,
                                        $endTimeFull
                                    );

                                if ($slotIsAvailable) {
                                    $isAvailable = true;
                                    $hasAvailableSlots = true;
                                    $timeSlotId = $slot->id;
                                    break;
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

                // إضافة الأوقات الفارغة (كل ساعة) إذا لم تكن موجودة في time slots
                $existingSlots = collect($timeSlots);
                for ($hour = $startHour; $hour < $endHour; $hour++) {
                    $startTime = Carbon::createFromTime($hour, 0, 0);
                    $endTime = $startTime->copy()->addHour();
                    $startTimeStr = $startTime->format('H:i');
                    $endTimeStr = $endTime->format('H:i');

                    // التحقق إذا كان هذا الوقت موجود في time slots
                    $exists = $existingSlots->contains(function ($slot) use ($startTimeStr, $endTimeStr) {
                        return $slot['start_time'] === $startTimeStr && $slot['end_time'] === $endTimeStr;
                    });

                    if (!$exists) {
                        $timeSlots[] = [
                            'time_slot_id' => null,
                            'start_time' => $startTimeStr,
                            'end_time' => $endTimeStr,
                            'is_available' => false,
                        ];
                    }
                }

                // ترتيب time slots حسب start_time
                usort($timeSlots, function ($a, $b) {
                    return strcmp($a['start_time'], $b['start_time']);
                });
            } else {
                // إذا لم يكن هناك time slots، نعرض الأوقات كل ساعة
                for ($hour = $startHour; $hour < $endHour; $hour++) {
                    $startTime = Carbon::createFromTime($hour, 0, 0);
                    $endTime = $startTime->copy()->addHour();
                    $startTimeStr = $startTime->format('H:i');
                    $endTimeStr = $endTime->format('H:i');

                    $timeSlots[] = [
                        'time_slot_id' => null,
                        'start_time' => $startTimeStr,
                        'end_time' => $endTimeStr,
                        'is_available' => false,
                    ];
                }
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

    /**
     * Get available time slots for a consultation on a specific date
     */
    public function availableConsultationTimeSlots(Request $request)
    {
        $customer = $request->user();

        if (!$customer || !$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تسجيل الدخول للوصول',
            ], 401);
        }

        $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $consultation = Consultation::with('category')->findOrFail($request->consultation_id);

        if (!$consultation->category_id) {
            return response()->json([
                'success' => false,
                'message' => 'الاستشارة المختارة لا تحتوي على فئة',
            ], 422);
        }

        $date = Carbon::parse($request->date)->format('Y-m-d');
        $categoryId = $consultation->category_id;

        $employees = Employee::where('is_available', true)
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
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

        // إنشاء قائمة بالأوقات
        $timeSlots = [];
        $startHour = 10;
        $endHour = 18;

        // للاستشارات: استخدام time slots الموجودة مباشرة
        if (!$allTimeSlots->isEmpty()) {
            foreach ($allTimeSlots as $slot) {
                $slotStart = Carbon::parse($slot->start_time);
                $slotEnd = Carbon::parse($slot->end_time);
                
                $startTimeStr = $slotStart->format('H:i');
                $endTimeStr = $slotEnd->format('H:i');
                $startTimeFull = $slotStart->format('H:i:s');
                $endTimeFull = $slotEnd->format('H:i:s');

                // البحث عن موظفين متاحين لهذا time slot
                $availableEmployees = [];
                $isAvailable = false;

                foreach ($employees as $employee) {
                    if ($slot->employee_id === $employee->id) {
                        // التحقق من أن time slot متاح
                        $slotIsAvailable = $slot->is_available &&
                            $employee->isAvailableForTimeSlot(
                                $slot->id,
                                $date,
                                $startTimeFull,
                                $endTimeFull
                            );

                        if ($slotIsAvailable) {
                            $isAvailable = true;
                            $availableEmployees[] = [
                                'id' => $employee->id,
                                'name' => $employee->user->name,
                                'time_slot_id' => $slot->id,
                            ];
                        }
                    }
                }

                $timeSlots[] = [
                    'time_slot_id' => $slot->id,
                    'start_time' => $startTimeStr,
                    'end_time' => $endTimeStr,
                    'date' => $date,
                    'is_available' => $isAvailable,
                    'employees' => $availableEmployees,
                ];
            }

            // إضافة الأوقات الفارغة (كل ساعة) إذا لم تكن موجودة في time slots
            $existingSlots = collect($timeSlots);
            for ($hour = $startHour; $hour < $endHour; $hour++) {
                $startTime = Carbon::createFromTime($hour, 0, 0);
                $endTime = $startTime->copy()->addHour();
                $startTimeStr = $startTime->format('H:i');
                $endTimeStr = $endTime->format('H:i');

                // التحقق إذا كان هذا الوقت موجود في time slots
                $exists = $existingSlots->contains(function ($slot) use ($startTimeStr, $endTimeStr) {
                    return $slot['start_time'] === $startTimeStr && $slot['end_time'] === $endTimeStr;
                });

                if (!$exists) {
                    $timeSlots[] = [
                        'time_slot_id' => null,
                        'start_time' => $startTimeStr,
                        'end_time' => $endTimeStr,
                        'date' => $date,
                        'is_available' => false,
                        'employees' => [],
                    ];
                }
            }

            // ترتيب time slots حسب start_time
            usort($timeSlots, function ($a, $b) {
                return strcmp($a['start_time'], $b['start_time']);
            });
        } else {
            // إذا لم يكن هناك time slots، نعرض الأوقات كل ساعة
            for ($hour = $startHour; $hour < $endHour; $hour++) {
                $startTime = Carbon::createFromTime($hour, 0, 0);
                $endTime = $startTime->copy()->addHour();
                $startTimeStr = $startTime->format('H:i');
                $endTimeStr = $endTime->format('H:i');

                $timeSlots[] = [
                    'time_slot_id' => null,
                    'start_time' => $startTimeStr,
                    'end_time' => $endTimeStr,
                    'date' => $date,
                    'is_available' => false,
                    'employees' => [],
                ];
            }
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

        // تحرير الـ time slots فقط إذا كان الحجز مؤكد (تم الدفع)
        // إذا كان الحجز pending ولم يتم الدفع، الـ time slots لم تُحجز أصلاً
        if ($booking->payment_status === 'paid' && $booking->timeSlots) {
            $timeSlotIds = $booking->timeSlots->pluck('id')->toArray();
            TimeSlot::whereIn('id', $timeSlotIds)->update(['is_available' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز بنجاح',
            'data' => $booking->fresh()->load(['service.subCategory.category', 'employee.user', 'timeSlot']),
        ]);
    }

    public function payment(Request $request)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        $request->validate([
            'temp_booking_id' => 'required|string',
            'payment_method' => 'required|string|in:cash,credit_card,debit_card',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $tempBookingId = $request->temp_booking_id;
        $cacheKey = 'pending_booking_' . $tempBookingId;
        $tempBookingData = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (!$tempBookingData) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الحجز غير موجودة أو انتهت صلاحيتها. يرجى إنشاء حجز جديد.',
            ], 404);
        }

        // التحقق من أن العميل هو صاحب الحجز
        if ($tempBookingData['customer_id'] !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول لهذا الحجز',
            ], 403);
        }

        // استخدام معاملة (Transaction) مع lockForUpdate لضمان أن أول من يدفع يحصل على الحجز
        return DB::transaction(function () use ($request, $tempBookingData, $tempBookingId, $cacheKey) {
            // للاستشارات، time_slot_id واحد فقط، للخدمات قد يكون array
            $timeSlotIds = $tempBookingData['time_slot_ids'] ?? [$tempBookingData['time_slot_id']];
            
            // التحقق من أن الـ time slots لا تزال متاحة قبل حجزها
            $unavailableSlots = TimeSlot::whereIn('id', $timeSlotIds)
                ->where('is_available', false)
                ->lockForUpdate()
                ->exists();
                
            if ($unavailableSlots) {
                \Illuminate\Support\Facades\Cache::forget($cacheKey);
                return response()->json([
                    'success' => false,
                    'message' => 'بعض الأوقات لم تعد متاحة. يرجى إنشاء حجز جديد.',
                ], 422);
            }

            // حجز الـ time slots بعد الدفع
            TimeSlot::whereIn('id', $timeSlotIds)->update(['is_available' => false]);

            // إنشاء الحجز (confirmed, paid) بعد الدفع
            $booking = Booking::create([
                'customer_id' => $tempBookingData['customer_id'],
                'employee_id' => $tempBookingData['employee_id'],
                'service_id' => $tempBookingData['service_id'] ?? null,
                'consultation_id' => $tempBookingData['consultation_id'] ?? null,
                'booking_type' => $tempBookingData['booking_type'] ?? 'service',
                'time_slot_id' => $tempBookingData['time_slot_id'],
                'booking_date' => $tempBookingData['booking_date'],
                'start_time' => $tempBookingData['start_time'],
                'end_time' => $tempBookingData['end_time'],
                'total_price' => $tempBookingData['total_price'],
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'paid_at' => now(),
                'notes' => $tempBookingData['notes'] ?? null,
                'payment_data' => [
                    'payment_method' => $request->payment_method,
                    'transaction_id' => $request->transaction_id,
                ],
            ]);

            // ربط الحجز بالـ time slots (للخدمات فقط، الاستشارات تستخدم time_slot_id مباشرة)
            if (isset($tempBookingData['time_slot_ids']) && is_array($tempBookingData['time_slot_ids'])) {
                $booking->timeSlots()->attach($timeSlotIds);
            }

            // حذف البيانات المؤقتة من cache
            \Illuminate\Support\Facades\Cache::forget($cacheKey);

            // إلغاء الحجوزات pending الأخرى لنفس الـ time slots (إذا كانت موجودة)
            $otherPendingBookings = Booking::whereIn('id', function($query) use ($timeSlotIds) {
                $query->select('booking_id')
                    ->from('booking_time_slot')
                    ->whereIn('time_slot_id', $timeSlotIds);
            })
            ->where('id', '!=', $booking->id)
            ->where('status', 'pending')
            ->where('payment_status', 'unpaid')
            ->get();

            foreach ($otherPendingBookings as $otherBooking) {
                $otherBooking->update([
                    'status' => 'cancelled',
                    'notes' => ($otherBooking->notes ?? '') . "\nتم الإلغاء تلقائياً: تم حجز نفس الأوقات من قبل عميل آخر.",
                ]);
            }

            // إرسال إشعارات
            try {
                $notificationService = app(NotificationService::class);
                $bookingFresh = $booking->fresh();
                if ($bookingFresh->booking_type === 'consultation') {
                    $bookingFresh->load(['customer', 'consultation', 'employee.user']);
                } else {
                    $bookingFresh->load(['customer', 'service', 'employee.user']);
                }
                $notificationService->bookingCreated($bookingFresh);
                $notificationService->paymentReceived($bookingFresh);
            } catch (\Exception $e) {
                \Log::error('Failed to send notification: ' . $e->getMessage());
            }

            // تحميل البيانات المناسبة حسب نوع الحجز
            $bookingFresh = $booking->fresh();
            if ($bookingFresh->booking_type === 'consultation') {
                $bookingFresh->load(['consultation.category', 'employee.user']);
            } else {
                $bookingFresh->load(['service.subCategory.category', 'employee.user', 'timeSlots']);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم الدفع بنجاح وتأكيد الحجز',
                'data' => $bookingFresh,
            ]);
        });
    }

    /**
     * Find available employee for category and time
     * يختار أول موظف متاح، ويترك الباقي متاحين للعملاء الآخرين
     */
    private function findAvailableEmployee($categoryId, $date, $startTime, $endTime)
    {
        $employees = Employee::where('is_available', true)
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
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

    /**
     * حجز استشارة
     */
    public function storeConsultation(StoreConsultationBookingRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $customer = $request->user();

            if (!$customer->isCustomer()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ليس لديك صلاحية للوصول',
                ], 403);
            }

            // جلب الاستشارة
            $consultation = Consultation::with('category')->findOrFail($request->consultation_id);

            if (!$consultation->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'الاستشارة المختارة غير متاحة حالياً',
                ], 422);
            }

            // جلب time slot
            $timeSlot = TimeSlot::lockForUpdate()->find($request->time_slot_id);
            
            if (!$timeSlot) {
                return response()->json([
                    'success' => false,
                    'message' => 'الوقت المختار غير موجود',
                ], 422);
            }

            if (!$timeSlot->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'الوقت المختار لم يعد متاحاً',
                ], 422);
            }

            // التحقق من أن التاريخ متطابق
            $requestedBookingDate = $request->booking_date;
            $timeSlotDate = $timeSlot->date->format('Y-m-d');
            $requestedDate = Carbon::parse($requestedBookingDate)->format('Y-m-d');
            
            if ($timeSlotDate !== $requestedDate) {
                return response()->json([
                    'success' => false,
                    'message' => "الوقت المختار لا يتطابق مع تاريخ الحجز. تاريخ الـ Time Slot: {$timeSlotDate}، تاريخ الحجز المطلوب: {$requestedDate}",
                ], 422);
            }

            // التحقق من أن الموظف لديه التخصص المطلوب
            $employee = Employee::find($timeSlot->employee_id);
            if (!$employee || !$employee->categories()->where('categories.id', $consultation->category_id)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'الموظف المتاح لا يقدم التخصص المطلوب لهذه الاستشارة',
                ], 422);
            }

            // استخدام مدة الـ time slot المختار مباشرة
            $start = Carbon::parse($timeSlot->start_time);
            $end = Carbon::parse($timeSlot->end_time);
            
            // حساب المدة بالدقائق (نفس الطريقة المستخدمة في حجز الخدمات)
            $slotDuration = $end->diffInMinutes($start, false);
            if ($slotDuration < 0) {
                // إذا كانت المدة سالبة، نستخدم القيمة المطلقة
                $slotDuration = abs($slotDuration);
            }
            
            if ($slotDuration <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => "الوقت المختار غير صالح. يرجى التحقق من أن وقت النهاية بعد وقت البداية.",
                ], 422);
            }

            // استخدام end_time من الـ time slot مباشرة
            $endTime = $timeSlot->end_time;

            // حفظ بيانات الحجز مؤقتاً في cache
            $tempBookingId = 'temp_consultation_' . uniqid() . '_' . time();
            $tempBookingData = [
                'customer_id' => $customer->id,
                'employee_id' => $employee->id,
                'consultation_id' => $consultation->id,
                'booking_type' => 'consultation',
                'time_slot_id' => $timeSlot->id,
                'booking_date' => $requestedBookingDate,
                'start_time' => $timeSlot->start_time,
                'end_time' => $endTime,
                'total_price' => $consultation->fixed_price,
                'notes' => $request->notes,
            ];

            \Illuminate\Support\Facades\Cache::put(
                'pending_booking_' . $tempBookingId,
                $tempBookingData,
                now()->addMinutes(30)
            );

            // إذا كان الدفع إلكتروني
            if ($request->payment_method === 'online') {
                try {
                    $paymobService = app(\App\Services\PaymobService::class);
                    
                    $auth = $paymobService->authenticate();
                    if (!$auth || empty($auth['token'])) {
                        return response()->json([
                            'success' => false,
                            'message' => 'فشل الاتصال بخادم الدفع',
                        ], 503);
                    }

                    $amountCents = (int) ($consultation->fixed_price * 100);
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])->timeout(30)->post(config('services.paymob.base_url', 'https://ksa.paymob.com/api') . '/ecommerce/orders', [
                        'auth_token' => $auth['token'],
                        'delivery_needed' => false,
                        'amount_cents' => $amountCents,
                        'currency' => config('services.paymob.currency', 'SAR'),
                        'merchant_order_id' => $tempBookingId,
                        'items' => []
                    ]);

                    if (!$response->successful()) {
                        \Log::error('PayMob Create Order Failed for Consultation', [
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'فشل إنشاء طلب الدفع',
                        ], 503);
                    }

                    $paymobOrderId = $response->json('id');

                    // Create Payment Key
                    $billingData = [
                        'apartment' => 'NA',
                        'email' => $customer->email ?? 'customer@example.com',
                        'floor' => 'NA',
                        'first_name' => $customer->name ?? 'Customer',
                        'street' => 'NA',
                        'building' => 'NA',
                        'phone_number' => $customer->phone ?? '0000000000',
                        'shipping_method' => 'NA',
                        'postal_code' => 'NA',
                        'city' => 'Riyadh',
                        'country' => 'SA',
                        'last_name' => 'NA',
                        'state' => 'NA'
                    ];

                    $paymentKeyResponse = \Illuminate\Support\Facades\Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])->timeout(30)->post(config('services.paymob.base_url', 'https://ksa.paymob.com/api') . '/acceptance/payment_keys', [
                        'auth_token' => $auth['token'],
                        'amount_cents' => $amountCents,
                        'expiration' => 3600,
                        'order_id' => (string) $paymobOrderId,
                        'billing_data' => $billingData,
                        'currency' => config('services.paymob.currency', 'SAR'),
                        'integration_id' => (int) config('services.paymob.integration_id'),
                        'lock_order_when_paid' => true
                    ]);

                    if (!$paymentKeyResponse->successful()) {
                        \Log::error('PayMob Create Payment Key Failed for Consultation', [
                            'status' => $paymentKeyResponse->status(),
                            'body' => $paymentKeyResponse->body(),
                        ]);
                        return response()->json([
                            'success' => false,
                            'message' => 'فشل إنشاء رابط الدفع',
                        ], 503);
                    }

                    $paymentKey = $paymentKeyResponse->json('token');

                    if (!$paymentKey) {
                        return response()->json([
                            'success' => false,
                            'message' => 'فشل إنشاء رابط الدفع',
                        ], 503);
                    }

                    $iframeId = config('services.paymob.iframe_id');
                    $paymentUrl = config('services.paymob.base_url', 'https://ksa.paymob.com/api') . '/acceptance/iframes/' . $iframeId . '?payment_token=' . $paymentKey;

                    return response()->json([
                        'success' => true,
                        'message' => 'تم إنشاء طلب الحجز بنجاح',
                        'data' => [
                            'temp_booking_id' => $tempBookingId,
                            'payment_url' => $paymentUrl,
                            'total_price' => $consultation->fixed_price,
                        ],
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Payment error for consultation: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'حدث خطأ أثناء معالجة الدفع',
                    ], 500);
                }
            }

            // إذا كان الدفع نقدي أو لم يتم تحديد طريقة الدفع
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء طلب الحجز بنجاح',
                'data' => [
                    'temp_booking_id' => $tempBookingId,
                    'total_price' => $consultation->fixed_price,
                ],
            ]);
        });
    }
}
