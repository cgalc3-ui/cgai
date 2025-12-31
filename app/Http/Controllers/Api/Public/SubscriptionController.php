<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Api\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Get all active subscriptions (public endpoint for frontend)
     */
    public function index()
    {
        $subscriptions = Subscription::active()
            ->orderBy('price', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
        ]);
    }

    /**
     * Get subscription details
     */
    public function show(Subscription $subscription)
    {
        if (!$subscription->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'الباقة غير متاحة',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $subscription,
        ]);
    }
}

