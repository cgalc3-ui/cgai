<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of subscriptions
     */
    public function index()
    {
        $subscriptions = Subscription::latest()->paginate(15);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new subscription
     */
    public function create()
    {
        return view('admin.subscriptions.create');
    }

    /**
     * Store a newly created subscription
     */
    public function store(StoreSubscriptionRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        // Set default values for old fields
        $data['max_debtors'] = 0;
        $data['max_messages'] = 0;
        $data['ai_enabled'] = false;
        $data['duration_type'] = $data['duration_type'] ?? 'monthly';

        $subscription = Subscription::create($data);

        // Notify all admins except the creator - Store translation keys, not translated text
        $this->notificationService->notifyAdmins(
            'subscription_created',
            'messages.new_subscription_package_created',
            'messages.new_subscription_package_created_with_name',
            [
                'subscription_id' => $subscription->id,
                'name' => $subscription->name, // Store subscription name in data for translation
            ]
        );

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم إنشاء الباقة بنجاح');
    }

    /**
     * Display the specified subscription
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['requests', 'userSubscriptions']);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified subscription
     */
    public function edit(Subscription $subscription)
    {
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    /**
     * Update the specified subscription
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        // Preserve old fields if not provided
        if (!isset($data['max_debtors'])) {
            $data['max_debtors'] = $subscription->max_debtors;
        }
        if (!isset($data['max_messages'])) {
            $data['max_messages'] = $subscription->max_messages;
        }
        if (!isset($data['ai_enabled'])) {
            $data['ai_enabled'] = $subscription->ai_enabled;
        }
        if (!isset($data['duration_type'])) {
            $data['duration_type'] = $subscription->duration_type;
        }

        $subscription->update($data);

        // Notify all admins except the updater
        $this->notificationService->notifyAdmins(
            'subscription_updated',
            'تم تحديث باقة',
            "تم تحديث باقة: {$subscription->name}",
            [
                'subscription_id' => $subscription->id,
            ]
        );

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم تحديث الباقة بنجاح');
    }

    /**
     * Remove the specified subscription
     */
    public function destroy(Subscription $subscription)
    {
        // Check if there are any requests
        if ($subscription->requests()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الباقة لأنها مرتبطة بطلبات اشتراك');
        }

        // Check if there are any active subscriptions
        if ($subscription->userSubscriptions()->where('status', 'active')->count() > 0) {
            return back()->with('error', 'لا يمكن حذف الباقة لأنها مرتبطة باشتراكات نشطة');
        }

        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'تم حذف الباقة بنجاح');
    }
}
