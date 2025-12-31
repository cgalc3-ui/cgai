<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Get all subscriptions
     */
    public function index(Request $request)
    {
        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        $subscriptions = Subscription::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
        ]);
    }

    /**
     * Get subscription details
     */
    public function show(Request $request, Subscription $subscription)
    {
        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $subscription,
        ]);
    }

    /**
     * Create a new subscription
     */
    public function store(Request $request)
    {
        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'required|array|min:1',
            'features.*.name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_type' => 'nullable|in:monthly,3months,6months,yearly',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['duration_type'] = $data['duration_type'] ?? 'monthly';
        $data['is_active'] = $data['is_active'] ?? true;
        $data['max_debtors'] = 0;
        $data['max_messages'] = 0;
        $data['ai_enabled'] = false;

        $subscription = Subscription::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الباقة بنجاح',
            'data' => $subscription,
        ], 201);
    }

    /**
     * Update subscription
     */
    public function update(Request $request, Subscription $subscription)
    {
        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'features' => 'sometimes|required|array|min:1',
            'features.*.name' => 'required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'duration_type' => 'nullable|in:monthly,3months,6months,yearly',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات المدخلة غير صحيحة',
                'errors' => $validator->errors(),
            ], 422);
        }

        $subscription->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الباقة بنجاح',
            'data' => $subscription->fresh(),
        ]);
    }

    /**
     * Delete subscription
     */
    public function destroy(Request $request, Subscription $subscription)
    {
        // Check if user is admin
        if (!$request->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        // Check if there are any requests
        if ($subscription->requests()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الباقة لأنها مرتبطة بطلبات اشتراك',
            ], 422);
        }

        // Check if there are any active subscriptions
        if ($subscription->userSubscriptions()->where('status', 'active')->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف الباقة لأنها مرتبطة باشتراكات نشطة',
            ], 422);
        }

        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الباقة بنجاح',
        ]);
    }
}

