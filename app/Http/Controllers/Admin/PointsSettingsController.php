<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointsSetting;
use App\Models\Service;
use App\Models\Consultation;
use App\Models\Subscription;
use App\Models\ServicePointsPricing;
use App\Models\PointsTransaction;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Http\Request;

class PointsSettingsController extends Controller
{
    /**
     * Display points settings
     */
    public function index()
    {
        $settings = PointsSetting::getActive();
        $services = Service::with('pointsPricing')->get();
        $consultations = Consultation::with('pointsPricing')->get();
        $subscriptions = Subscription::with('pointsPricing')->get();
        
        return view('admin.points.settings', compact('settings', 'services', 'consultations', 'subscriptions'));
    }

    /**
     * Update points settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'points_per_riyal' => 'required|numeric|min:0.01',
            'is_active' => 'nullable|boolean',
        ]);

        $settings = PointsSetting::getActive();
        if (!$settings) {
            $settings = PointsSetting::create([
                'points_per_riyal' => $request->points_per_riyal,
                'is_active' => $request->has('is_active') ? true : false,
            ]);
        } else {
            $settings->update([
                'points_per_riyal' => $request->points_per_riyal,
                'is_active' => $request->has('is_active') ? true : false,
            ]);
        }

        return redirect()->route('admin.points.settings')
            ->with('success', __('messages.settings_updated_successfully'));
    }

    /**
     * Update service points pricing
     */
    public function updateServicePricing(Request $request, $serviceId)
    {
        $request->validate([
            'points_price' => 'required|numeric|min:0',
        ]);

        $service = Service::findOrFail($serviceId);
        
        $pricing = ServicePointsPricing::where('service_id', $serviceId)
            ->where('item_type', 'service')
            ->first();

        if ($pricing) {
            $pricing->update([
                'points_price' => $request->points_price,
                'is_active' => $request->has('is_active') ? true : false,
            ]);
        } else {
            ServicePointsPricing::create([
                'service_id' => $serviceId,
                'item_type' => 'service',
                'points_price' => $request->points_price,
                'is_active' => $request->has('is_active') ? true : false,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.pricing_updated_successfully')
            ]);
        }

        return redirect()->route('admin.points.settings')
            ->with('success', __('messages.pricing_updated_successfully'));
    }

    /**
     * Update consultation points pricing
     */
    public function updateConsultationPricing(Request $request, $consultationId)
    {
        $request->validate([
            'points_price' => 'required|numeric|min:0',
        ]);

        $consultation = Consultation::findOrFail($consultationId);
        
        $pricing = ServicePointsPricing::where('consultation_id', $consultationId)
            ->where('item_type', 'consultation')
            ->first();

        if ($pricing) {
            $pricing->update([
                'points_price' => $request->points_price,
                'is_active' => $request->has('is_active') ? true : false,
            ]);
        } else {
            ServicePointsPricing::create([
                'consultation_id' => $consultationId,
                'item_type' => 'consultation',
                'points_price' => $request->points_price,
                'is_active' => $request->has('is_active') ? true : false,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.pricing_updated_successfully')
            ]);
        }

        return redirect()->route('admin.points.settings')
            ->with('success', __('messages.pricing_updated_successfully'));
    }

    /**
     * Update subscription points pricing
     */
    public function updateSubscriptionPricing(Request $request, $subscriptionId)
    {
        $request->validate([
            'points_price' => 'required|numeric|min:0',
        ]);

        $subscription = Subscription::findOrFail($subscriptionId);
        
        $pricing = ServicePointsPricing::where('subscription_id', $subscriptionId)
            ->where('item_type', 'subscription')
            ->first();

        if ($pricing) {
            $pricing->update([
                'points_price' => $request->points_price,
                'is_active' => $request->has('is_active') ? true : false,
            ]);
        } else {
            ServicePointsPricing::create([
                'subscription_id' => $subscriptionId,
                'item_type' => 'subscription',
                'points_price' => $request->points_price,
                'is_active' => $request->has('is_active') ? true : false,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.pricing_updated_successfully')
            ]);
        }

        return redirect()->route('admin.points.settings')
            ->with('success', __('messages.pricing_updated_successfully'));
    }

    /**
     * Get all points transactions
     */
    public function transactions(Request $request)
    {
        $query = PointsTransaction::with(['user', 'booking'])->latest();

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->paginate(10);

        // Get all customers for filter dropdown
        $customers = User::where('role', 'customer')
            ->orderBy('name')
            ->get(['id', 'name', 'phone', 'email']);

        return view('admin.points.transactions', compact('transactions', 'customers'));
    }

    /**
     * Get all wallets
     */
    public function wallets(Request $request)
    {
        $query = Wallet::with('user');

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by user name
        if ($request->has('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Sort by balance
        $sortBy = $request->get('sort_by', 'balance');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $wallets = $query->paginate(10);

        // Statistics
        $totalWallets = Wallet::count();
        $totalBalance = Wallet::sum('balance');
        $activeWallets = Wallet::where('balance', '>', 0)->count();
        $emptyWallets = Wallet::where('balance', '=', 0)->count();

        return view('admin.points.wallets', compact('wallets', 'totalWallets', 'totalBalance', 'activeWallets', 'emptyWallets'));
    }
}
