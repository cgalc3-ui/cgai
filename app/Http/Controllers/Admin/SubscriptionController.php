<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $subscriptions = $query->latest()->paginate(10)->withQueryString();
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
        $data['is_pro'] = $request->has('is_pro') ? true : false;

        // Ensure numeric values are properly cast
        if (isset($data['price'])) {
            $data['price'] = (float) $data['price'];
        }

        // Ensure duration_type is valid and is a string
        if (isset($data['duration_type'])) {
            $durationType = trim((string) $data['duration_type']);
            if (in_array($durationType, ['month', 'year', 'lifetime'])) {
                $data['duration_type'] = $durationType;
            } else {
                $data['duration_type'] = 'month';
            }
        } else {
            $data['duration_type'] = 'month';
        }
        
        // Force string type for duration_type
        $data['duration_type'] = (string) $data['duration_type'];

        // Filter out empty features
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_filter($data['features'], function($feature) {
                return !empty(trim($feature ?? ''));
            });
            $data['features'] = array_values($data['features']); // Re-index array
        } else {
            $data['features'] = [];
        }

        if (isset($data['features_en']) && is_array($data['features_en'])) {
            $data['features_en'] = array_filter($data['features_en'], function($feature) {
                return !empty(trim($feature ?? ''));
            });
            $data['features_en'] = array_values($data['features_en']); // Re-index array
        } else {
            $data['features_en'] = [];
        }

        $subscription = Subscription::create($data);

        // Notify all admins except the creator
        $this->notificationService->notifyAdmins(
            'subscription_created',
            __('messages.new_subscription_package_created'),
            __('messages.new_subscription_package_created_with_name', ['name' => $subscription->name]),
            [
                'subscription_id' => $subscription->id,
                'name' => $subscription->name,
                'name_en' => $subscription->name_en,
            ]
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.subscription_created_success'),
                'redirect' => route('admin.subscriptions.index')
            ]);
        }

        return redirect()->route('admin.subscriptions.index')
            ->with('success', __('messages.subscription_created_success'));
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
        $data['is_pro'] = $request->has('is_pro') ? true : false;

        // Ensure numeric values are properly cast
        if (isset($data['price'])) {
            $data['price'] = (float) $data['price'];
        }

        // Ensure duration_type is valid and is a string
        $durationType = null;
        if (isset($data['duration_type']) && !empty($data['duration_type'])) {
            $durationType = trim((string) $data['duration_type']);
            if (!in_array($durationType, ['month', 'year', 'lifetime'])) {
                $durationType = $subscription->duration_type ?? 'month';
            }
        } else {
            $durationType = $subscription->duration_type ?? 'month';
        }
        
        // Remove duration_type from data array to update it separately
        unset($data['duration_type']);

        // Filter out empty features
        if (isset($data['features']) && is_array($data['features'])) {
            $data['features'] = array_filter($data['features'], function($feature) {
                return !empty(trim($feature ?? ''));
            });
            $data['features'] = array_values($data['features']); // Re-index array
        }

        if (isset($data['features_en']) && is_array($data['features_en'])) {
            $data['features_en'] = array_filter($data['features_en'], function($feature) {
                return !empty(trim($feature ?? ''));
            });
            $data['features_en'] = array_values($data['features_en']); // Re-index array
        }

        // Update all fields except duration_type
        $subscription->fill($data);
        $subscription->save();
        
        // Update duration_type using DB facade to ensure it's treated as string
        DB::table('subscriptions')
            ->where('id', $subscription->id)
            ->update(['duration_type' => $durationType]);
        
        // Refresh the model to get updated values
        $subscription->refresh();

        // Notify all admins except the updater
        $this->notificationService->notifyAdmins(
            'subscription_updated',
            __('messages.subscription_updated'),
            __('messages.subscription_updated_with_name', ['name' => $subscription->name]),
            [
                'subscription_id' => $subscription->id,
                'name' => $subscription->name,
                'name_en' => $subscription->name_en,
            ]
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.subscription_updated_success'),
                'redirect' => null
            ]);
        }

        return redirect()->route('admin.subscriptions.index')
            ->with('success', __('messages.subscription_updated_success'));
    }

    /**
     * Remove the specified subscription
     */
    public function destroy(Subscription $subscription)
    {
        // Check if there are any requests
        if ($subscription->requests()->count() > 0) {
            return back()->with('error', __('messages.subscription_cannot_delete_requests'));
        }

        // Check if there are any active subscriptions
        if ($subscription->userSubscriptions()->where('status', 'active')->count() > 0) {
            return back()->with('error', __('messages.subscription_cannot_delete_active'));
        }

        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', __('messages.subscription_deleted_success'));
    }
}
