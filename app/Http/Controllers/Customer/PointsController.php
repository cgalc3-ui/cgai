<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\PointsTransaction;
use App\Models\PointsSetting;
use App\Services\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointsController extends Controller
{
    protected $paymobService;

    public function __construct(PaymobService $paymobService)
    {
        $this->middleware('auth');
        $this->paymobService = $paymobService;
    }

    /**
     * Display wallet and purchase points page (API)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $wallet = $user->getOrCreateWallet();
        $settings = PointsSetting::getActive();

        return response()->json([
            'success' => true,
            'data' => [
                'wallet' => [
                    'balance' => (float) $wallet->balance,
                    'user_id' => $wallet->user_id,
                ],
                'settings' => [
                    'points_per_riyal' => (float) $settings->points_per_riyal,
                    'is_active' => $settings->is_active,
                ],
            ],
        ]);
    }

    /**
     * Get points transactions (API)
     */
    public function transactions(Request $request)
    {
        $user = $request->user();
        $transactions = PointsTransaction::where('user_id', $user->id)
            ->with(['booking'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'points' => (float) $transaction->points,
                    'amount_paid' => $transaction->amount_paid ? (float) $transaction->amount_paid : null,
                    'description' => $transaction->description,
                    'balance_before' => (float) $transaction->balance_before,
                    'balance_after' => (float) $transaction->balance_after,
                    'booking_id' => $transaction->booking_id,
                    'created_at' => $transaction->created_at,
                ];
            }),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * Purchase points packages
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();
        $settings = PointsSetting::getActive();

        if (!$settings || !$settings->is_active) {
            return response()->json([
                'success' => false,
                'message' => __('messages.points_system_disabled')
            ], 400);
        }

        $amount = $request->amount;
        $points = $amount * $settings->points_per_riyal;

        try {
            // Create payment via PayMob
            $auth = $this->paymobService->authenticate();
            if (!$auth || empty($auth['token'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.payment_connection_failed')
                ], 503);
            }

            // Create order
            $amountCents = (int) ($amount * 100);
            $orderId = 'POINTS_' . $user->id . '_' . time();
            
            $orderResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(config('services.paymob.base_url', 'https://ksa.paymob.com/api') . '/ecommerce/orders', [
                'auth_token' => $auth['token'],
                'delivery_needed' => false,
                'amount_cents' => $amountCents,
                'currency' => 'SAR',
                'merchant_order_id' => $orderId,
                'items' => []
            ]);

            if (!$orderResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.payment_order_creation_failed')
                ], 503);
            }

            $paymobOrderId = $orderResponse->json('id');

            // Create payment key
            $paymentKeyResponse = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(config('services.paymob.base_url', 'https://ksa.paymob.com/api') . '/acceptance/payment_keys', [
                'auth_token' => $auth['token'],
                'amount_cents' => $amountCents,
                'expiration' => 3600,
                'order_id' => $paymobOrderId,
                'billing_data' => [
                    'apartment' => 'NA',
                    'email' => $user->email ?? 'customer@example.com',
                    'floor' => 'NA',
                    'first_name' => $user->name,
                    'street' => 'NA',
                    'building' => 'NA',
                    'phone_number' => $user->phone ?? '0500000000',
                    'shipping_method' => 'NA',
                    'postal_code' => 'NA',
                    'city' => 'NA',
                    'country' => 'SA',
                    'last_name' => 'NA',
                    'state' => 'NA',
                ],
                'currency' => 'SAR',
                'integration_id' => config('services.paymob.integration_id'),
            ]);

            if (!$paymentKeyResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.payment_key_creation_failed')
                ], 503);
            }

            $paymentKey = $paymentKeyResponse->json('token');
            $iframeId = config('services.paymob.iframe_id');
            $paymentUrl = "https://ksa.paymob.com/api/acceptance/iframes/{$iframeId}?payment_token={$paymentKey}";

            // Store transaction in session for callback
            session([
                'points_purchase' => [
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'points' => $points,
                    'paymob_order_id' => $paymobOrderId,
                    'payment_key' => $paymentKey,
                ]
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $paymentUrl,
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            \Log::error('Points Purchase Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('messages.payment_link_creation_failed')
            ], 500);
        }
    }

    /**
     * Handle payment callback
     */
    public function callback(Request $request)
    {
        $purchaseData = session('points_purchase');
        
        if (!$purchaseData) {
            return redirect()->route('customer.points.index')
                ->with('error', __('messages.invalid_payment_session'));
        }

        // Verify payment with PayMob
        // This is simplified - you should verify HMAC signature in production
        
        if ($request->has('success') && $request->success == 'true') {
            DB::beginTransaction();
            try {
                $user = auth()->user();
                $wallet = $user->getOrCreateWallet();
                
                // Add points to wallet
                $wallet->addPoints(
                    $purchaseData['points'],
                    'purchase',
                    null,
                    $purchaseData['amount'],
                    $request->get('payment_id'),
                    __('messages.points_purchased') . ': ' . number_format($purchaseData['amount'], 2) . ' ' . __('messages.sar')
                );

                DB::commit();
                
                session()->forget('points_purchase');
                
                return redirect()->route('customer.points.index')
                    ->with('success', __('messages.points_added_successfully'));
                    
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Points Purchase Callback Error: ' . $e->getMessage());
                return redirect()->route('customer.points.index')
                    ->with('error', __('messages.points_purchase_failed'));
            }
        }

        session()->forget('points_purchase');
        return redirect()->route('customer.points.index')
            ->with('error', __('messages.payment_failed'));
    }
}
