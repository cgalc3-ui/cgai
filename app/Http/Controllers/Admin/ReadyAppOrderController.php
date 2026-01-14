<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReadyAppOrder;
use Illuminate\Http\Request;

class ReadyAppOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ReadyAppOrder::with(['user', 'app', 'processor']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by app
        if ($request->filled('app_id')) {
            $query->where('ready_app_id', $request->app_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('admin.ready-apps.orders.index', compact('orders'));
    }

    public function show(ReadyAppOrder $order)
    {
        $order->load(['user', 'app.category', 'processor']);
        return view('admin.ready-apps.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, ReadyAppOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $order->status;

        $order->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        // Send notification if status changed
        if ($oldStatus !== $order->status) {
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->readyAppOrderStatusUpdated($order, $oldStatus);
        }

        return redirect()->route('admin.ready-apps.orders.show', $order)
            ->with('success', __('messages.ready_app_order_status_updated'));
    }
}
