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
use App\Models\Wallet;
use App\Models\ServicePointsPricing;
use App\Models\PointsSetting;
use App\Services\NotificationService;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BookingController extends Controller
{
    use ApiResponseTrait;
    public function store(StoreBookingRequest $request)
    {
        // استخدام معاملة (Transaction) لضمان أن جميع العمليات تنجح معًا أو تفشل معًا
        return DB::transaction(function () use ($request) {
            $customer = $request->user();

            // 1. التحقق من صلاحيات المستخدم
            if (!$customer->isCustomer()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.unauthorized_access'),
                ], 403);
            }

            // 2. جلب والتحقق من الخدمة
            $service = Service::with('subCategory.category')->findOrFail($request->service_id);

            if (!$service->subCategory || !$service->subCategory->category_id) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.service_no_category_specified'),
                ], 422);
            }

            $categoryId = $service->subCategory->category_id;

            $hourlyRate = $service->getHourlyRate();
            if (!$hourlyRate) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.service_no_hourly_rate_specified'),
                ], 422);
            }

            // 3. التحقق من الوقت المحددة (Time Slots) واختيار الموظف المتاح تلقائياً
            $requestedSlotIds = $request->time_slot_ids;
            $requestedBookingDate = $request->booking_date;

            // جلب كل الوقت المحددة من قاعدة البيانات
            $timeSlots = TimeSlot::whereIn('id', $requestedSlotIds)->lockForUpdate()->get(); // lockForUpdate لمنع التعديل المتزامن

            // التحقق من أن جميع المعرفات المطلوبة موجودة
            if ($timeSlots->count() !== count($requestedSlotIds)) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.booking_invalid_time_slot_ids'),
                ], 422);
            }

            // التحقق من أن جميع الوقت المحددة في نفس تاريخ الحجز المطلوب
            if ($timeSlots->first(fn($slot) => $slot->date->format('Y-m-d') !== Carbon::parse($requestedBookingDate)->format('Y-m-d'))) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.booking_time_slots_date_mismatch'),
                ], 422);
            }

            // ترتيب time slots حسب الوقت
            $timeSlots = $timeSlots->sortBy('start_time');
            $firstSlot = $timeSlots->first();
            $lastSlot = $timeSlots->last();
            $startTime = $firstSlot->start_time;
            $endTime = $lastSlot->end_time;

            // البحث عن موظف متاح تلقائياً
            $subCategoryId = $service->subCategory->id ?? null;
            $availableEmployee = $this->findAvailableEmployeeForBooking($categoryId, $subCategoryId, $requestedBookingDate, $startTime, $endTime, $timeSlots->pluck('id')->toArray());

            if (!$availableEmployee) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.no_available_employee_for_time'),
                ], 422);
            }

            $employeeId = $availableEmployee['employee_id'];
            $timeSlots = $availableEmployee['time_slots'];

            // التحقق من أن جميع الوقت المحددة في نفس تاريخ الحجز المطلوب
            if ($timeSlots->first(fn($slot) => $slot->date->format('Y-m-d') !== Carbon::parse($requestedBookingDate)->format('Y-m-d'))) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.booking_time_slots_date_mismatch'),
                ], 422);
            }

            // 4. التحقق من أن الموظف موجود ومتاح
            $employee = Employee::find($employeeId);
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.employee_not_found'),
                ], 422);
            }

            if (!$employee->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.employee_not_available'),
                ], 422);
            }

            // ملاحظة: التحقق من الفئة تم إزالته لأن availableTimeSlots يضمن أن time slots المعروضة
            // هي فقط لموظفين لديهم الفئة المطلوبة (الرئيسية أو الفرعية)
            // إذا تم إرسال time slot ID يدوياً لموظف ليس لديه الفئة، سيتم رفض الحجز في مرحلة الدفع

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
                    'message' => __('messages.booking_invalid_duration'),
                ], 422);
            }

            if ($hourlyRate <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.service_invalid_hourly_rate'),
                ], 422);
            }

            $totalPrice = $totalDurationInHours * $hourlyRate;
            
            // التأكد من أن السعر الإجمالي موجب
            if ($totalPrice <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.booking_invalid_total_price'),
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
                    'message' => __('messages.booking_invalid_duration'),
                ], 422);
            }

            if ($hourlyRate <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.service_invalid_hourly_rate'),
                ], 422);
            }

            $totalPrice = $totalDurationInHours * $hourlyRate;
            
            // التأكد من أن السعر الإجمالي موجب
            if ($totalPrice <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.booking_invalid_total_price'),
                ], 422);
            }

            // 6. حفظ بيانات الحجز مؤقتاً في cache - لا يتم إنشاء الحجز في قاعدة البيانات إلا بعد الدفع
            $firstSlot = $timeSlots->first();
            $lastSlot = $timeSlots->last();

            // التحقق من أن الـ time slots لا تزال متاحة
            // ملاحظة: لا نحجز الـ time slots هنا - سيتم حجزها بعد الدفع فقط
            if ($timeSlots->where('is_available', false)->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.booking_time_slots_unavailable'),
                ], 422);
            }

            // حفظ بيانات الحجز مؤقتاً في cache (لمدة 30 دقيقة)
            $tempBookingId = 'temp_' . uniqid() . '_' . time();
            $tempBookingData = [
                'customer_id' => $customer->id,
                'employee_id' => $employeeId,
                'service_id' => $service->id,
                'time_slot_id' => $firstSlot->id,
                'time_slot_ids' => $timeSlots->pluck('id')->toArray(),
                'booking_date' => $requestedBookingDate,
                'start_time' => $firstSlot->start_time,
                'end_time' => $lastSlot->end_time,
                'total_price' => $totalPrice,
                'notes' => $request->notes,
                'booking_type' => 'service',
            ];

            // إذا كان الدفع بالنقاط، معالجة الدفع مباشرة
            if ($request->payment_method === 'points') {
                // الحصول على سعر النقاط للخدمة
                $pointsPricing = ServicePointsPricing::getPricing('service', $service->id);
                
                if (!$pointsPricing || !$pointsPricing->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.points_pricing_not_available'),
                    ], 422);
                }

                $pointsNeeded = $pointsPricing->points_price;
                $wallet = $customer->getOrCreateWallet();

                // التحقق من الرصيد
                if ($wallet->balance < $pointsNeeded) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.insufficient_points_balance'),
                        'data' => [
                            'required' => $pointsNeeded,
                            'available' => $wallet->balance,
                        ],
                    ], 422);
                }

                // خصم النقاط
                $wallet->deductPoints(
                    $pointsNeeded,
                    'usage',
                    null, // سيتم تحديثه بعد إنشاء الحجز
                    __('messages.points_used_for_booking')
                );

                // حجز الـ time slots
                TimeSlot::whereIn('id', $requestedSlotIds)->update(['is_available' => false]);

                // إنشاء الحجز مباشرة (confirmed, paid)
                $booking = Booking::create([
                    'customer_id' => $customer->id,
                    'employee_id' => $employeeId,
                    'service_id' => $service->id,
                    'time_slot_id' => $firstSlot->id,
                    'booking_date' => $requestedBookingDate,
                    'start_time' => $firstSlot->start_time,
                    'end_time' => $lastSlot->end_time,
                    'total_price' => $totalPrice,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_method' => 'points',
                    'points_used' => $pointsNeeded,
                    'points_price' => $pointsPricing->points_price,
                    'paid_at' => now(),
                    'notes' => $request->notes,
                ]);

                // ربط الحجز بالـ time slots
                $booking->timeSlots()->attach($requestedSlotIds);

                // تحديث transaction بالنقاط برقم الحجز
                \App\Models\PointsTransaction::where('user_id', $customer->id)
                    ->where('type', 'usage')
                    ->whereNull('booking_id')
                    ->latest()
                    ->first()
                    ->update(['booking_id' => $booking->id]);

                // إرسال إشعار
                $notificationService = app(NotificationService::class);
                $notificationService->bookingCreated($booking);

                return response()->json([
                    'success' => true,
                    'message' => __('messages.booking_created_and_paid_successfully'),
                    'data' => [
                        'booking' => $booking->load(['service', 'employee.user', 'timeSlots']),
                        'points_used' => $pointsNeeded,
                        'points_balance' => $wallet->fresh()->balance,
                    ],
                ], 201);
            }

            // حفظ البيانات في cache للدفع لاحقاً
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
                            'message' => __('messages.payment_connection_failed'),
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
                            'message' => __('messages.payment_order_creation_failed'),
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
                            'message' => __('messages.payment_key_creation_failed'),
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
                        'message' => __('messages.payment_complete_required'),
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
                        'message' => __('messages.payment_link_creation_failed') . ': ' . $e->getMessage(),
                    ], 500);
                }
            }

            // للدفع اليدوي، إرجاع temp_booking_id فقط
            return response()->json([
                'success' => true,
                'message' => __('messages.booking_saved_success'),
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
                'message' => __('messages.booking_data_not_found'),
            ], 404);
        }

        // التحقق من أن العميل هو صاحب الحجز
        if ($tempBookingData['customer_id'] !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_booking_access'),
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

        $bookings = $query->orderBy('created_at', 'desc')
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate($request->get('per_page', 10));

        // Format the response to include only required data
        $formattedBookings = $bookings->getCollection()->map(function ($booking) {
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
                'payment_method' => $booking->payment_method,
                'points_used' => $booking->points_used ? (float) $booking->points_used : null,
                'points_price' => $booking->points_price ? (float) $booking->points_price : null,
                'payment_id' => $booking->payment_id,
                'payment_data' => $booking->payment_data,
                'paid_at' => $booking->paid_at,
                'notes' => $booking->notes,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
                'service' => $booking->service ? [
                    'id' => $booking->service->id,
                    'name' => $booking->service->name,
                    'name_en' => $booking->service->name_en,
                    'description' => $booking->service->description,
                    'description_en' => $booking->service->description_en,
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

        // Filter locale columns
        $filteredBookings = $formattedBookings->map(function ($booking) {
            return $this->filterLocaleColumns($booking);
        });

        // Create paginated response
        $paginatedBookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredBookings,
            $bookings->total(),
            $bookings->perPage(),
            $bookings->currentPage(),
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $paginatedBookings->items(),
            'pagination' => [
                'current_page' => $paginatedBookings->currentPage(),
                'per_page' => $paginatedBookings->perPage(),
                'total' => $paginatedBookings->total(),
                'last_page' => $paginatedBookings->lastPage(),
                'from' => $paginatedBookings->firstItem(),
                'to' => $paginatedBookings->lastItem(),
            ],
            'links' => [
                'first' => $paginatedBookings->url(1),
                'last' => $paginatedBookings->url($paginatedBookings->lastPage()),
                'prev' => $paginatedBookings->previousPageUrl(),
                'next' => $paginatedBookings->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Get past bookings for customer
     */
    public function pastBookings(Request $request)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        $now = Carbon::now();

        $query = Booking::where('customer_id', $customer->id)
            ->with([
                'service' => function($q) {
                    $q->with('subCategory.category');
                },
                'consultation' => function($q) {
                    $q->with('category');
                },
                'employee.user',
                'timeSlots',
                'rating'
            ])
            ->where(function($q) use ($now) {
                // Past bookings: completed, cancelled, or booking date/time has passed
                $q->whereIn('status', ['completed', 'cancelled'])
                  ->orWhere(function($subQ) use ($now) {
                      // Booking date has passed
                      $subQ->whereDate('booking_date', '<', $now->format('Y-m-d'))
                           ->orWhere(function($dateQ) use ($now) {
                               // Same date but end time has passed
                               $dateQ->whereDate('booking_date', '=', $now->format('Y-m-d'))
                                     ->whereTime('end_time', '<', $now->format('H:i:s'));
                           });
                  });
            });

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate($request->get('per_page', 10));

        // Format the response
        $formattedBookings = $bookings->getCollection()->map(function ($booking) {
            return [
                'id' => $booking->id,
                'customer_id' => $booking->customer_id,
                'employee_id' => $booking->employee_id,
                'service_id' => $booking->service_id,
                'consultation_id' => $booking->consultation_id,
                'booking_type' => $booking->booking_type,
                'booking_date' => $booking->booking_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'total_price' => $booking->total_price,
                'status' => $booking->status,
                'actual_status' => $booking->actual_status,
                'payment_status' => $booking->payment_status,
                'payment_id' => $booking->payment_id,
                'paid_at' => $booking->paid_at,
                'notes' => $booking->notes,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
                'service' => $booking->service ? [
                    'id' => $booking->service->id,
                    'name' => $booking->service->trans('name'),
                    'name_en' => $booking->service->name_en,
                    'description' => $booking->service->trans('description'),
                    'description_en' => $booking->service->description_en,
                    'price' => $booking->service->price, 
                ] : null,
                'consultation' => $booking->consultation ? [
                    'id' => $booking->consultation->id,
                    'name' => $booking->consultation->trans('name'),
                    'name_en' => $booking->consultation->name_en,
                    'description' => $booking->consultation->trans('description'),
                    'description_en' => $booking->consultation->description_en,
                    'price' => $booking->consultation->price,
                    'category' => $booking->consultation->category ? [
                        'id' => $booking->consultation->category->id,
                        'name' => $booking->consultation->category->trans('name'),
                    ] : null,
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
                'has_rating' => $booking->rating !== null,
                'rating' => $booking->rating ? [
                    'id' => $booking->rating->id,
                    'rating' => $booking->rating->rating,
                    'comment' => $booking->rating->comment,
                    'created_at' => $booking->rating->created_at,
                ] : null,
            ];
        });

        // Filter locale columns
        $filteredBookings = $formattedBookings->map(function ($booking) {
            return $this->filterLocaleColumns($booking);
        });

        return response()->json([
            'success' => true,
            'data' => $filteredBookings,
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
                'from' => $bookings->firstItem(),
                'to' => $bookings->lastItem(),
            ],
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
                'message' => __('messages.login_required'),
            ], 401);
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
        ]);

        $service = Service::with('subCategory.category')->findOrFail($request->service_id);

        if (!$service->subCategory || !$service->subCategory->category_id) {
            return response()->json([
                'success' => false,
                'message' => __('messages.service_no_category'),
            ], 422);
        }

        // الحصول على سعر الساعة (من hourly_rate أو من ServiceDuration)
        $hourlyRate = $service->getHourlyRate();

        if (!$hourlyRate) {
            return response()->json([
                'success' => false,
                'message' => __('messages.service_no_hourly_rate'),
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
                'message' => __('messages.login_required'),
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
                'message' => __('messages.service_no_category'),
            ], 422);
        }

        // الحصول على سعر الساعة (من hourly_rate أو من ServiceDuration)
        $hourlyRate = $service->getHourlyRate();

        if (!$hourlyRate) {
            return response()->json([
                'success' => false,
                'message' => __('messages.service_no_hourly_rate'),
            ], 422);
        }

        $categoryId = $service->subCategory->category_id;
        $subCategoryId = $service->sub_category_id;

        $date = Carbon::parse($request->date)->format('Y-m-d');

        // Find employees who have either the category OR the specific subcategory
        $employees = Employee::where('is_available', true)
            ->where(function ($query) use ($categoryId, $subCategoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                })
                ->orWhereHas('subCategories', function ($q) use ($subCategoryId) {
                    $q->where('sub_categories.id', $subCategoryId);
                });
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
                'message' => __('messages.login_required'),
            ], 401);
        }

        $request->validate([
            'consultation_id' => 'required|exists:consultations,id',
        ]);

        $consultation = Consultation::with('category')->findOrFail($request->consultation_id);

        if (!$consultation->category_id) {
            return response()->json([
                'success' => false,
                'message' => __('messages.consultation_no_category'),
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
                'message' => __('messages.login_required'),
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
                'message' => __('messages.consultation_no_category'),
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

    public function show(Request $request, $id)
    {
        // Set locale from request first
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized'),
            ], 403);
        }

        // Find booking and check if it belongs to customer
        $booking = Booking::where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => __('messages.booking_not_found'),
            ], 404);
        }

        $booking->load(['service', 'consultation', 'employee.user', 'timeSlots']);

        // Format the response to include only required data
        $formattedBooking = [
            'id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'employee_id' => $booking->employee_id,
            'service_id' => $booking->service_id,
            'consultation_id' => $booking->consultation_id,
            'booking_type' => $booking->booking_type,
            'booking_date' => $booking->booking_date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'total_price' => $booking->total_price,
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'payment_method' => $booking->payment_method,
            'points_used' => $booking->points_used ? (float) $booking->points_used : null,
            'points_price' => $booking->points_price ? (float) $booking->points_price : null,
            'payment_id' => $booking->payment_id,
            'payment_data' => $booking->payment_data,
            'paid_at' => $booking->paid_at,
            'notes' => $booking->notes,
            'created_at' => $booking->created_at,
            'updated_at' => $booking->updated_at,
            'service' => $booking->service ? [
                'id' => $booking->service->id,
                'name' => $booking->service->trans('name'),
                'name_en' => $booking->service->name_en,
                'description' => $booking->service->trans('description'),
                'description_en' => $booking->service->description_en,
                'price' => $booking->service->price,
            ] : null,
            'consultation' => $booking->consultation ? [
                'id' => $booking->consultation->id,
                'name' => $booking->consultation->trans('name'),
                'name_en' => $booking->consultation->name_en,
                'description' => $booking->consultation->trans('description'),
                'description_en' => $booking->consultation->description_en,
                'price' => $booking->consultation->fixed_price,
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

        // Filter locale columns
        $filteredBooking = $this->filterLocaleColumns($formattedBooking);

        return response()->json([
            'success' => true,
            'data' => $filteredBooking,
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
                'message' => __('messages.unauthorized_booking_access'),
            ], 403);
        }

        if (in_array($booking->status, ['cancelled', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => __('messages.booking_cannot_update_status'),
            ], 422);
        }

        $booking->update($request->only(['notes']));

        return response()->json([
            'success' => true,
            'message' => __('messages.booking_updated_success'),
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
                'message' => __('messages.unauthorized_booking_access'),
            ], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => __('messages.booking_already_cancelled'),
            ], 422);
        }

        if ($booking->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => __('messages.booking_cannot_cancel_completed'),
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
            'message' => __('messages.booking_cancelled_success'),
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
            'payment_method' => 'required|string|in:online,points',
            'transaction_id' => 'nullable|string|max:255',
        ]);

        $tempBookingId = $request->temp_booking_id;
        $cacheKey = 'pending_booking_' . $tempBookingId;
        $tempBookingData = \Illuminate\Support\Facades\Cache::get($cacheKey);

        if (!$tempBookingData) {
            return response()->json([
                'success' => false,
                'message' => __('messages.booking_data_not_found'),
            ], 404);
        }

        // التحقق من أن العميل هو صاحب الحجز
        if ($tempBookingData['customer_id'] !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_booking_access'),
            ], 403);
        }

        // استخدام معاملة (Transaction) مع lockForUpdate لضمان أن أول من يدفع يحصل على الحجز
        return DB::transaction(function () use ($request, $tempBookingData, $tempBookingId, $cacheKey, $customer) {
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
                    'message' => __('messages.booking_time_slots_no_longer_available'),
                ], 422);
            }

            // معالجة الدفع بالنقاط
            $pointsUsed = null;
            $pointsPrice = null;
            $paymentData = [
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
            ];

            if ($request->payment_method === 'points') {
                // الحصول على سعر النقاط للخدمة/الاستشارة
                $itemType = $tempBookingData['booking_type'] ?? 'service';
                $itemId = $itemType === 'service' 
                    ? ($tempBookingData['service_id'] ?? null)
                    : ($tempBookingData['consultation_id'] ?? null);

                if (!$itemId) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.booking_item_not_found'),
                    ], 404);
                }

                $pointsPricing = ServicePointsPricing::getPricing($itemType, $itemId);
                
                if (!$pointsPricing || !$pointsPricing->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.points_pricing_not_available'),
                    ], 422);
                }

                $pointsNeeded = $pointsPricing->points_price;
                $wallet = $customer->getOrCreateWallet();

                // التحقق من الرصيد
                if ($wallet->balance < $pointsNeeded) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.insufficient_points_balance'),
                        'data' => [
                            'required' => $pointsNeeded,
                            'available' => $wallet->balance,
                        ],
                    ], 422);
                }

                // خصم النقاط
                $wallet->deductPoints(
                    $pointsNeeded,
                    'usage',
                    null, // سيتم تحديثه بعد إنشاء الحجز
                    __('messages.points_used_for_booking')
                );

                $pointsUsed = $pointsNeeded;
                $pointsPrice = $pointsPricing->points_price;
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
                'payment_method' => $request->payment_method,
                'points_used' => $pointsUsed,
                'points_price' => $pointsPrice,
                'paid_at' => now(),
                'notes' => $tempBookingData['notes'] ?? null,
                'payment_data' => $paymentData,
            ]);

            // تحديث transaction بالنقاط برقم الحجز
            if ($pointsUsed) {
                \App\Models\PointsTransaction::where('user_id', $customer->id)
                    ->where('type', 'usage')
                    ->whereNull('booking_id')
                    ->latest()
                    ->first()
                    ->update(['booking_id' => $booking->id]);
            }

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
                'message' => __('messages.booking_payment_success'),
                'data' => $bookingFresh,
            ]);
        });
    }

    /**
     * Find available employee for booking automatically
     * يبحث عن موظف متاح تلقائياً للحجز
     */
    private function findAvailableEmployeeForBooking($categoryId, $subCategoryId = null, $date, $startTime, $endTime, $preferredSlotIds = [])
    {
        // البحث عن الموظفين المتاحين للكاتيجوري والصب كاتيجوري
        $employees = Employee::where('is_available', true)
            ->where(function ($query) use ($categoryId, $subCategoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
                
                if ($subCategoryId) {
                    $query->orWhereHas('subCategories', function ($q) use ($subCategoryId) {
                        $q->where('sub_categories.id', $subCategoryId);
                    });
                }
            })
            ->with('user')
            ->get();

        if ($employees->isEmpty()) {
            return null;
        }

        // محاولة استخدام time slots المفضلة أولاً (إذا كانت متاحة)
        if (!empty($preferredSlotIds)) {
            $preferredSlots = TimeSlot::whereIn('id', $preferredSlotIds)
                ->where('date', $date)
                ->where('is_available', true)
                ->lockForUpdate()
                ->get()
                ->sortBy('start_time');

            if ($preferredSlots->isNotEmpty()) {
                $preferredEmployeeId = $preferredSlots->first()->employee_id;
                $preferredEmployee = $employees->firstWhere('id', $preferredEmployeeId);

                if ($preferredEmployee) {
                    // التحقق من أن الموظف متاح في هذا الوقت
                    $firstSlot = $preferredSlots->first();
                    $lastSlot = $preferredSlots->last();
                    
                    if ($preferredEmployee->isAvailableForTimeSlot($firstSlot->id, $date, $firstSlot->start_time, $lastSlot->end_time)) {
                        // التحقق من أن جميع time slots لنفس الموظف
                        if ($preferredSlots->pluck('employee_id')->unique()->count() === 1) {
                            return [
                                'employee_id' => $preferredEmployeeId,
                                'time_slots' => $preferredSlots,
                            ];
                        }
                    }
                }
            }
        }

        // إذا لم تكن time slots المفضلة متاحة، البحث عن موظف آخر متاح
        foreach ($employees as $employee) {
            // البحث عن time slots متاحة لهذا الموظف في نفس الوقت
            $availableSlots = TimeSlot::where('employee_id', $employee->id)
                ->where('date', $date)
                ->where('is_available', true)
                ->lockForUpdate()
                ->get()
                ->filter(function ($slot) use ($startTime, $endTime) {
                    $slotStart = Carbon::parse($slot->start_time);
                    $slotEnd = Carbon::parse($slot->end_time);
                    $requestStart = Carbon::parse($startTime);
                    $requestEnd = Carbon::parse($endTime);

                    // التحقق من أن time slot يغطي الوقت المطلوب
                    return $slotStart->lte($requestStart) && $slotEnd->gte($requestEnd);
                })
                ->sortBy('start_time');

            if ($availableSlots->isNotEmpty()) {
                $firstSlot = $availableSlots->first();
                $lastSlot = $availableSlots->last();
                
                // التحقق من أن الموظف متاح في هذا الوقت (لا يوجد تعارض مع حجوزات أخرى)
                if ($employee->isAvailableForTimeSlot($firstSlot->id, $date, $firstSlot->start_time, $lastSlot->end_time)) {
                    // إذا كنا نحتاج عدة time slots، نحاول العثور عليها
                    $neededSlots = $this->findConsecutiveTimeSlots($availableSlots, $startTime, $endTime);
                    
                    if ($neededSlots->isNotEmpty()) {
                        return [
                            'employee_id' => $employee->id,
                            'time_slots' => $neededSlots,
                        ];
                    }
                }
            }
        }

        return null;
    }

    /**
     * Find consecutive time slots covering the required time period
     */
    private function findConsecutiveTimeSlots($availableSlots, $startTime, $endTime)
    {
        $requestStart = Carbon::parse($startTime);
        $requestEnd = Carbon::parse($endTime);
        $neededDuration = $requestEnd->diffInMinutes($requestStart);

        $sortedSlots = $availableSlots->sortBy('start_time');
        $selectedSlots = collect();
        $currentStart = $requestStart->copy();

        foreach ($sortedSlots as $slot) {
            $slotStart = Carbon::parse($slot->start_time);
            $slotEnd = Carbon::parse($slot->end_time);

            // التحقق من أن time slot يغطي جزء من الوقت المطلوب
            if ($slotStart->lte($currentStart) && $slotEnd->gt($currentStart)) {
                $selectedSlots->push($slot);
                $currentStart = $slotEnd;

                // إذا غطينا الوقت المطلوب بالكامل، نعود
                if ($currentStart->gte($requestEnd)) {
                    return $selectedSlots;
                }
            }
        }

        // إذا لم نجد time slots متتالية كافية، نعود بفارغ
        if ($currentStart->lt($requestEnd)) {
            return collect();
        }

        return $selectedSlots;
    }

    /**
     * Find available employee for consultation booking automatically
     * يبحث عن موظف متاح تلقائياً لحجز الاستشارة
     */
    private function findAvailableEmployeeForConsultation($categoryId, $date, $startTime, $endTime, $preferredTimeSlotId = null)
    {
        // البحث عن الموظفين المتاحين للكاتيجوري
        $employees = Employee::where('is_available', true)
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('categories.id', $categoryId);
            })
            ->with('user')
            ->get();

        if ($employees->isEmpty()) {
            return null;
        }

        // محاولة استخدام time slot المفضل أولاً (إذا كان متاحاً)
        if ($preferredTimeSlotId) {
            $preferredTimeSlot = TimeSlot::where('id', $preferredTimeSlotId)
                ->where('date', $date)
                ->where('is_available', true)
                ->lockForUpdate()
                ->first();

            if ($preferredTimeSlot) {
                $preferredEmployee = $employees->firstWhere('id', $preferredTimeSlot->employee_id);

                if ($preferredEmployee && $preferredEmployee->isAvailableForTimeSlot($preferredTimeSlot->id, $date, $startTime, $endTime)) {
                    return [
                        'employee' => $preferredEmployee,
                        'time_slot' => $preferredTimeSlot,
                    ];
                }
            }
        }

        // إذا لم يكن time slot المفضل متاحاً، البحث عن موظف آخر متاح
        foreach ($employees as $employee) {
            // البحث عن time slot متاح لهذا الموظف في نفس الوقت
            $availableSlot = TimeSlot::where('employee_id', $employee->id)
                ->where('date', $date)
                ->where('is_available', true)
                ->lockForUpdate()
                ->get()
                ->first(function ($slot) use ($startTime, $endTime) {
                    $slotStart = Carbon::parse($slot->start_time);
                    $slotEnd = Carbon::parse($slot->end_time);
                    $requestStart = Carbon::parse($startTime);
                    $requestEnd = Carbon::parse($endTime);

                    // التحقق من أن time slot يغطي الوقت المطلوب
                    return $slotStart->lte($requestStart) && $slotEnd->gte($requestEnd);
                });

            if ($availableSlot && $employee->isAvailableForTimeSlot($availableSlot->id, $date, $startTime, $endTime)) {
                return [
                    'employee' => $employee,
                    'time_slot' => $availableSlot,
                ];
            }
        }

        return null;
    }

    /**
     * Find available employee for category and time
     * يختار أول موظف متاح، ويترك الباقي متاحين للعملاء الآخرين
     */
    private function findAvailableEmployee($categoryId, $subCategoryId = null, $date, $startTime, $endTime)
    {
        $employees = Employee::where('is_available', true)
            ->where(function ($query) use ($categoryId, $subCategoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
                
                if ($subCategoryId) {
                    $query->orWhereHas('subCategories', function ($q) use ($subCategoryId) {
                        $q->where('sub_categories.id', $subCategoryId);
                    });
                }
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
                    'message' => __('messages.unauthorized_access'),
                ], 403);
            }

            // جلب الاستشارة
            $consultation = Consultation::with('category')->findOrFail($request->consultation_id);

            if (!$consultation->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.consultation_not_available'),
                ], 422);
            }

            // البحث عن موظف متاح تلقائياً
            $requestedBookingDate = $request->booking_date;
            $requestedTimeSlotId = $request->time_slot_id;
            
            // جلب time slot المطلوب كمرجع للوقت
            $referenceTimeSlot = TimeSlot::find($requestedTimeSlotId);
            
            if (!$referenceTimeSlot) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.time_slot_not_found'),
                ], 422);
            }

            // التحقق من أن التاريخ متطابق
            $timeSlotDate = $referenceTimeSlot->date->format('Y-m-d');
            $requestedDate = Carbon::parse($requestedBookingDate)->format('Y-m-d');
            
            if ($timeSlotDate !== $requestedDate) {
                return response()->json([
                    'success' => false,
                    'message' => "الوقت المختار لا يتطابق مع تاريخ الحجز. تاريخ الـ Time Slot: {$timeSlotDate}، تاريخ الحجز المطلوب: {$requestedDate}",
                ], 422);
            }

            // البحث عن موظف متاح تلقائياً
            $categoryId = $consultation->category_id;
            $availableEmployee = $this->findAvailableEmployeeForConsultation($categoryId, $requestedBookingDate, $referenceTimeSlot->start_time, $referenceTimeSlot->end_time, $requestedTimeSlotId);

            if (!$availableEmployee) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.no_available_employee_for_time'),
                ], 422);
            }

            $employee = $availableEmployee['employee'];
            $timeSlot = $availableEmployee['time_slot'];

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

            // إذا كان الدفع بالنقاط، معالجة الدفع مباشرة
            if ($request->payment_method === 'points') {
                // الحصول على سعر النقاط للاستشارة
                $pointsPricing = ServicePointsPricing::getPricing('consultation', $consultation->id);
                
                if (!$pointsPricing || !$pointsPricing->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.points_pricing_not_available'),
                    ], 422);
                }

                $pointsNeeded = $pointsPricing->points_price;
                $wallet = $customer->getOrCreateWallet();

                // التحقق من الرصيد
                if ($wallet->balance < $pointsNeeded) {
                    return response()->json([
                        'success' => false,
                        'message' => __('messages.insufficient_points_balance'),
                        'data' => [
                            'required' => $pointsNeeded,
                            'available' => $wallet->balance,
                        ],
                    ], 422);
                }

                // خصم النقاط
                $wallet->deductPoints(
                    $pointsNeeded,
                    'usage',
                    null, // سيتم تحديثه بعد إنشاء الحجز
                    __('messages.points_used_for_booking')
                );

                // حجز الـ time slot
                $timeSlot->update(['is_available' => false]);

                // إنشاء الحجز مباشرة (confirmed, paid)
                $booking = Booking::create([
                    'customer_id' => $customer->id,
                    'employee_id' => $employee->id,
                    'consultation_id' => $consultation->id,
                    'booking_type' => 'consultation',
                    'time_slot_id' => $timeSlot->id,
                    'booking_date' => $requestedBookingDate,
                    'start_time' => $timeSlot->start_time,
                    'end_time' => $endTime,
                    'total_price' => $consultation->fixed_price,
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_method' => 'points',
                    'points_used' => $pointsNeeded,
                    'points_price' => $pointsPricing->points_price,
                    'paid_at' => now(),
                    'notes' => $request->notes,
                ]);

                // تحديث transaction بالنقاط برقم الحجز
                \App\Models\PointsTransaction::where('user_id', $customer->id)
                    ->where('type', 'usage')
                    ->whereNull('booking_id')
                    ->latest()
                    ->first()
                    ->update(['booking_id' => $booking->id]);

                // إرسال إشعار
                $notificationService = app(NotificationService::class);
                $notificationService->bookingCreated($booking);

                return response()->json([
                    'success' => true,
                    'message' => __('messages.booking_created_and_paid_successfully'),
                    'data' => [
                        'booking' => $booking->load(['consultation.category', 'employee.user']),
                        'points_used' => $pointsNeeded,
                        'points_balance' => $wallet->fresh()->balance,
                    ],
                ], 201);
            }

            // حفظ بيانات الحجز مؤقتاً في cache للدفع لاحقاً
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

            // إذا كان الدفع إلكتروني، إنشاء payment_url مباشرة
            if ($request->payment_method === 'online') {
                try {
                    $paymobService = app(\App\Services\PaymobService::class);
                    
                    $auth = $paymobService->authenticate();
                    if (!$auth || empty($auth['token'])) {
                        return response()->json([
                            'success' => false,
                            'message' => __('messages.payment_connection_failed'),
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
                            'message' => __('messages.payment_order_creation_failed'),
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
                            'message' => __('messages.payment_link_creation_failed'),
                        ], 503);
                    }

                    $paymentKey = $paymentKeyResponse->json('token');

                    if (!$paymentKey) {
                        return response()->json([
                            'success' => false,
                            'message' => __('messages.payment_link_creation_failed'),
                        ], 503);
                    }

                    $iframeId = config('services.paymob.iframe_id');
                    $paymentUrl = config('services.paymob.base_url', 'https://ksa.paymob.com/api') . '/acceptance/iframes/' . $iframeId . '?payment_token=' . $paymentKey;

                    return response()->json([
                        'success' => true,
                        'message' => __('messages.booking_created_success'),
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
                        'message' => __('messages.payment_processing_error'),
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
