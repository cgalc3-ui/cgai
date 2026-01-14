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
    public function index(Request $request)
    {
        $query = SubscriptionRequest::with(['user', 'subscription', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by subscription
        if ($request->filled('subscription_id')) {
            $query->where('subscription_id', $request->subscription_id);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Search by user name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        // Get subscriptions for filter dropdown
        $subscriptions = \App\Models\Subscription::orderBy('name')->get();

        return view('admin.subscription-requests.index', compact('requests', 'subscriptions'));
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
        } elseif ($request->subscription->duration_type === 'year') {
            $expiresAt = now()->addYear();
        }
        // lifetime: expiresAt remains null

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
                'package' => $request->subscription->name,
                'package_en' => $request->subscription->name_en,
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
                'package' => $request->subscription->name,
                'package_en' => $request->subscription->name_en,
                'reason' => $validated['admin_notes'],
            ]
        );

        return redirect()->route('admin.subscription-requests.index')
            ->with('success', 'تم رفض طلب الاشتراك');
    }
}
