<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionRequest;
use App\Models\UserSubscription;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SubscriptionRequestController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of subscription requests
     */
    public function index()
    {
        $requests = SubscriptionRequest::with(['user', 'subscription', 'approver'])
            ->latest()
            ->paginate(15);

        return view('admin.subscription-requests.index', compact('requests'));
    }

    /**
     * Display the specified subscription request
     */
    public function show(SubscriptionRequest $request)
    {
        $request->load(['user', 'subscription', 'approver']);
        return view('admin.subscription-requests.show', compact('request'));
    }

    /**
     * Approve a subscription request
     */
    public function approve(Request $httpRequest, SubscriptionRequest $request)
    {
        if ($request->status !== 'pending') {
            return back()->with('error', 'لا يمكن الموافقة على طلب تم معالجته مسبقاً');
        }

        $validated = $httpRequest->validate([
            'admin_notes' => 'nullable|string',
        ]);

        // Update request status
        $request->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        // Calculate expiration date
        $expiresAt = null;
        $durationType = $request->subscription->duration_type;
        
        if ($durationType === 'monthly') {
            $expiresAt = now()->addMonth();
        } elseif ($durationType === '3months') {
            $expiresAt = now()->addMonths(3);
        } elseif ($durationType === '6months') {
            $expiresAt = now()->addMonths(6);
        } elseif ($durationType === 'yearly') {
            $expiresAt = now()->addYear();
        }
        // If null, subscription doesn't expire

        // Cancel all previous active subscriptions
        UserSubscription::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Create new subscription
        UserSubscription::create([
            'user_id' => $request->user_id,
            'subscription_id' => $request->subscription_id,
            'subscription_request_id' => $request->id,
            'status' => 'active',
            'started_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        // Notify the user
        $this->notificationService->send(
            $request->user,
            'subscription_request_status',
            'تم قبول طلب الاشتراك',
            "تم قبول طلب الاشتراك في باقة: {$request->subscription->name}",
            [
                'subscription_request_id' => $request->id,
                'subscription_id' => $request->subscription_id,
                'status' => 'approved',
            ]
        );

        return redirect()->route('admin.subscription-requests.index')
            ->with('success', 'تم قبول طلب الاشتراك بنجاح');
    }

    /**
     * Reject a subscription request
     */
    public function reject(Request $httpRequest, SubscriptionRequest $request)
    {
        if ($request->status !== 'pending') {
            return back()->with('error', 'لا يمكن رفض طلب تم معالجته مسبقاً');
        }

        $validated = $httpRequest->validate([
            'admin_notes' => 'required|string|min:10',
        ]);

        // Update request status
        $request->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'approved_by' => auth()->id(),
            'admin_notes' => $validated['admin_notes'],
        ]);

        // Notify the user
        $this->notificationService->send(
            $request->user,
            'subscription_request_status',
            'تم رفض طلب الاشتراك',
            "تم رفض طلب الاشتراك في باقة: {$request->subscription->name}. السبب: {$validated['admin_notes']}",
            [
                'subscription_request_id' => $request->id,
                'subscription_id' => $request->subscription_id,
                'status' => 'rejected',
            ]
        );

        return redirect()->route('admin.subscription-requests.index')
            ->with('success', 'تم رفض طلب الاشتراك');
    }
}
