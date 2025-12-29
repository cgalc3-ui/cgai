<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\PaymobService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymobService;

    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }

    public function initiatePayment(Request $request, $bookingId)
    {
        $user = $request->user();

        // 1. Find booking
        $booking = Booking::find($bookingId);
        
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'الحجز غير موجود'
            ], 404);
        }

        // 2. Authorization
        if ($booking->customer_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول لهذا الحجز'
            ], 403);
        }

        if ($booking->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'تم الدفع بالفعل'
            ], 400);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن الدفع لحجز ملغي'
            ], 422);
        }

        // Check if booking has a valid total price, if not or negative, try to recalculate it
        if (!$booking->total_price || $booking->total_price <= 0 || $booking->total_price < 0) {
            // Try to recalculate the price
            $service = $booking->service;
            if ($service) {
                $hourlyRate = $service->getHourlyRate();
                if ($hourlyRate) {
                    // Calculate duration from time slots
                    $timeSlots = $booking->timeSlots;
                    if ($timeSlots->isEmpty()) {
                        // Fallback to start_time and end_time
                        $start = Carbon::parse($booking->start_time);
                        $end = Carbon::parse($booking->end_time);
                        $durationInMinutes = $end->diffInMinutes($start, false);
                        if ($durationInMinutes < 0) {
                            $durationInMinutes = abs($durationInMinutes);
                        }
                        $totalDurationInHours = $durationInMinutes / 60.0;
                    } else {
                        $totalDurationInHours = 0;
                        foreach ($timeSlots as $slot) {
                            $start = Carbon::parse($slot->start_time);
                            $end = Carbon::parse($slot->end_time);
                            $durationInMinutes = $end->diffInMinutes($start, false);
                            if ($durationInMinutes < 0) {
                                $durationInMinutes = abs($durationInMinutes);
                            }
                            $totalDurationInHours += $durationInMinutes / 60.0;
                        }
                    }
                    
                    // Validate duration and hourly rate
                    if ($totalDurationInHours <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'المدة الزمنية للحجز غير صالحة'
                        ], 422);
                    }
                    
                    if ($hourlyRate <= 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'سعر الساعة للخدمة غير صالح'
                        ], 422);
                    }
                    
                    $totalPrice = $totalDurationInHours * $hourlyRate;
                    
                    if ($totalPrice > 0) {
                        // Update booking with calculated price
                        $booking->total_price = $totalPrice;
                        $booking->save();
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'لا يمكن حساب السعر الإجمالي للحجز. يرجى التحقق من بيانات الحجز.'
                        ], 422);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'الخدمة لا تحتوي على سعر ساعة محدد'
                    ], 422);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'الخدمة المرتبطة بالحجز غير موجودة'
                ], 422);
            }
        }

        try {
            // 3. Authenticate
            $auth = $this->paymobService->authenticate();
            if (!$auth || empty($auth['token'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل الاتصال بخادم الدفع'
                ], 503);
            }

            // 4. Create Order
            $paymobOrderId = $this->paymobService->createOrder($auth['token'], $booking);
            if (!$paymobOrderId) {
                Log::error('PayMob Create Order Failed', [
                    'booking_id' => $booking->id,
                    'total_price' => $booking->total_price,
                    'auth_token_exists' => !empty($auth['token'])
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'فشل إنشاء طلب الدفع. يرجى التحقق من إعدادات PayMob أو مراجعة السجلات.'
                ], 503);
            }

            // 5. Create Payment Key
            $paymentKey = $this->paymobService->createPaymentKey($auth['token'], $paymobOrderId, $booking);
            if (!$paymentKey) {
                // Get last error from service if available
                $errorDetails = $this->paymobService->getLastError();
                $errorMessage = 'فشل الحصول على مفتاح الدفع';
                if ($errorDetails) {
                    $errorMessage .= '. ' . $errorDetails;
                }
                $errorMessage .= ' يرجى مراجعة السجلات للتفاصيل.';
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 503);
            }

            // 6. Get URL
            $paymentUrl = $this->paymobService->getPaymentUrl($paymentKey);

            // 7. Update Booking
            $paymentData = $booking->payment_data ?? [];
            // Ensure payment_data is an array
            if (is_string($paymentData)) {
                $paymentData = json_decode($paymentData, true) ?? [];
            }
            if (!is_array($paymentData)) {
                $paymentData = [];
            }
            
            $booking->update([
                'payment_id' => $paymobOrderId,
                'payment_data' => array_merge($paymentData, [
                    'paymob_order_id' => $paymobOrderId,
                    'payment_key' => $paymentKey, // Optional to store
                ]),
                // Keep status as-is until callback
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء رابط الدفع بنجاح',
                'data' => [
                    'payment_url' => $paymentUrl,
                    'paymob_order_id' => $paymobOrderId
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Init Error: ' . $e->getMessage(), [
                'booking_id' => $bookingId,
                'user_id' => $user->id,
                'exception' => $e
            ]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الدفع'
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        Log::info('PayMob Callback', $request->all());

        $success = $request->boolean('success');
        $transactionId = $request->input('id');
        $paymobOrderId = $request->input('order'); // Paymob Order ID

        // Find booking
        $booking = Booking::where('payment_id', $paymobOrderId)
            ->orWhere('payment_data->paymob_order_id', $paymobOrderId)
            ->first();

        if (!$booking) {
            Log::error('PayMob Callback: Booking not found for order ' . $paymobOrderId);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Validate HMAC (Optional but recommended)
        $isValid = $this->paymobService->validateHmac($request->all());
        if (!$isValid) {
            Log::warning('PayMob HMAC Validation Failed for Booking ' . $booking->id);
            // Continue or stop based on strictness. Proceeding for now with warning.
        }

        if ($success) {
            $paymentData = $booking->payment_data ?? [];
            // Ensure payment_data is an array
            if (is_string($paymentData)) {
                $paymentData = json_decode($paymentData, true) ?? [];
            }
            if (!is_array($paymentData)) {
                $paymentData = [];
            }
            
            $booking->update([
                'payment_status' => 'paid',
                'payment_data' => array_merge($paymentData, [
                    'transaction_id' => $transactionId,
                    'callback_data' => $request->all()
                ]),
                'paid_at' => now(),
            ]);

            // Send payment notification
            try {
                $notificationService = app(NotificationService::class);
                $notificationService->paymentReceived($booking->fresh()->load(['customer', 'service']));
            } catch (\Exception $e) {
                Log::error('Failed to send payment notification: ' . $e->getMessage());
            }

            // You might want to return HTML for mobile webview or JSON
            // Since this is the "Redirect URL", user sees this.
            return response()->json(['message' => 'Payment Successful', 'booking_id' => $booking->id]);
        } else {
            $paymentData = $booking->payment_data ?? [];
            // Ensure payment_data is an array
            if (is_string($paymentData)) {
                $paymentData = json_decode($paymentData, true) ?? [];
            }
            if (!is_array($paymentData)) {
                $paymentData = [];
            }
            
            $booking->update([
                'payment_status' => 'failed',
                'payment_data' => array_merge($paymentData, [
                    'transaction_id' => $transactionId,
                    'callback_data' => $request->all(),
                    'error_message' => $request->input('data.message')
                ])
            ]);

            return response()->json(['message' => 'Payment Failed', 'booking_id' => $booking->id], 400);
        }
    }
}
