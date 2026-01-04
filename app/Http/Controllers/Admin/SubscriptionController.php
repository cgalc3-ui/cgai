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
    public function index(Request $request)
    {
        $query = Subscription::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by duration type
        if ($request->filled('duration_type')) {
            $query->where('duration_type', $request->duration_type);
        }

        // Filter by AI enabled
        if ($request->filled('ai_enabled')) {
            $query->where('ai_enabled', $request->ai_enabled === 'enabled');
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest()->paginate(15)->withQueryString();
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new subscription
     */
    public function create(Request $request)
    {
        $view = view('admin.subscriptions.create-modal');
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    /**
     * Store a newly created subscription
     */
    public function store(StoreSubscriptionRequest $request)
    {
        $data = $request->validated();
        $data['ai_enabled'] = $request->has('ai_enabled') ? true : false;
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Set default values for old fields
        $data['max_debtors'] = 0;
        $data['max_messages'] = 0;
        $data['ai_enabled'] = false;
        $data['duration_type'] = $data['duration_type'] ?? 'monthly';

        $subscription = Subscription::create($data);

        // Notify all admins except the creator
        $this->notificationService->notifyAdmins(
            'subscription_created',
            'تم إنشاء باقة جديدة',
            "تم إنشاء باقة جديدة: {$subscription->name}",
            [
                'subscription_id' => $subscription->id,
                'name' => $subscription->name,
                'name_en' => $subscription->name_en,
            ]
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الباقة بنجاح',
                'redirect' => route('admin.subscriptions.index')
            ]);
        }

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
    public function edit(Request $request, Subscription $subscription)
    {
        $view = view('admin.subscriptions.edit-modal', compact('subscription'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    /**
     * Update the specified subscription
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription)
    {
        $data = $request->validated();
        $data['ai_enabled'] = $request->has('ai_enabled') ? true : false;
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
            'messages.subscription_updated',
            'messages.subscription_updated_with_name',
            [
                'subscription_id' => $subscription->id,
                'name' => $subscription->name,
                'name_en' => $subscription->name_en,
            ]
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الباقة بنجاح',
                'redirect' => route('admin.subscriptions.index')
            ]);
        }

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
