<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequestRequest;
use App\Models\Subscription;
use App\Models\SubscriptionRequest;
use App\Models\UserSubscription;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\ApiResponseTrait;

class SubscriptionController extends Controller
{
    use ApiResponseTrait;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth:sanctum');
        $this->notificationService = $notificationService;
    }

    /**
     * Get all active subscriptions
     */
    public function index()
    {
        $subscriptions = Subscription::active()->get();
        $activeSubscription = auth()->user()->getActiveSubscription();
        $pendingRequest = SubscriptionRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->with(['subscription' => function($query) {
                $query->select('id', 'name', 'name_en', 'price', 'duration_type', 'description', 'description_en', 'is_active');
            }])
            ->first();

        // Filter locale columns
        $filteredSubscriptions = $subscriptions->map(function ($subscription) {
            return $this->filterLocaleColumns($subscription);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'subscriptions' => $filteredSubscriptions,
                'active_subscription' => $activeSubscription ? [
                    'id' => $activeSubscription->id,
                    'subscription' => $this->filterLocaleColumns($activeSubscription->subscription),
                    'status' => $activeSubscription->status,
                    'started_at' => $activeSubscription->started_at,
                    'expires_at' => $activeSubscription->expires_at,
                ] : null,
                'pending_request' => $pendingRequest ? [
                    'id' => $pendingRequest->id,
                    'subscription' => $this->filterLocaleColumns($pendingRequest->subscription),
                    'status' => $pendingRequest->status,
                    'created_at' => $pendingRequest->created_at,
                ] : null,
            ],
        ]);
    }

    /**
     * Get subscription details
     */
    public function show(Subscription $subscription)
    {
        return response()->json([
            'success' => true,
            'data' => $this->filterLocaleColumns($subscription),
        ]);
    }

    /**
     * Create a subscription request
     */
    public function store(StoreSubscriptionRequestRequest $request)
    {
        // Check if subscription is active
        $subscription = Subscription::findOrFail($request->subscription_id);
        if (!$subscription->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'الباقة غير متاحة حالياً',
            ], 400);
        }

        // Check if user has a pending request
        $pendingRequest = SubscriptionRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'لديك طلب اشتراك معلق بالفعل',
            ], 400);
        }

        // Upload payment proof
        $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        // Create subscription request
        $subscriptionRequest = SubscriptionRequest::create([
            'user_id' => auth()->id(),
            'subscription_id' => $request->subscription_id,
            'payment_proof' => $paymentProofPath,
            'status' => 'pending',
        ]);

        // Notify all admins - Store translation keys, not translated text
        $this->notificationService->notifyAdmins(
            'subscription_request_created',
            'messages.new_subscription_request',
            'messages.new_subscription_request_from_user',
            [
                'subscription_request_id' => $subscriptionRequest->id,
                'user_id' => auth()->id(),
                'subscription_id' => $request->subscription_id,
                'user' => auth()->user()->name,
                'package' => $subscription->name,
                'package_en' => $subscription->name_en,
            ]
        );

        $subscriptionRequest->load('subscription');

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال طلب الاشتراك بنجاح',
            'data' => $this->filterLocaleColumns($subscriptionRequest),
        ], 201);
    }

    /**
     * Get current user's active subscription
     */
    public function active()
    {
        $activeSubscription = auth()->user()->getActiveSubscription();

        if (!$activeSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد اشتراك نشط',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $activeSubscription->id,
                'subscription' => $this->filterLocaleColumns($activeSubscription->subscription),
                'status' => $activeSubscription->status,
                'started_at' => $activeSubscription->started_at,
                'expires_at' => $activeSubscription->expires_at,
                'is_active' => $activeSubscription->isActive(),
            ],
        ]);
    }

    /**
     * Get current user's subscription requests
     */
    public function requests()
    {
        $requests = SubscriptionRequest::where('user_id', auth()->id())
            ->with([
                'subscription' => function($query) {
                    $query->select('id', 'name', 'name_en', 'price', 'duration_type', 'description', 'description_en', 'is_active');
                },
                'approver' => function($query) {
                    $query->select('id', 'name', 'email');
                }
            ])
            ->latest()
            ->get();

        // Filter locale columns
        $filteredRequests = $requests->map(function ($request) {
            $requestData = $request->toArray();
            if (isset($requestData['subscription'])) {
                $requestData['subscription'] = $this->filterLocaleColumns($request->subscription);
            }
            return $this->filterLocaleColumns($requestData);
        });

        return response()->json([
            'success' => true,
            'data' => $filteredRequests,
        ]);
    }
}
