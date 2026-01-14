<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiServiceOrder;
use Illuminate\Http\Request;

class AiServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = AiServiceOrder::with(['user', 'service', 'processor']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by service
        if ($request->filled('service_id')) {
            $query->where('ai_service_id', $request->service_id);
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

        return view('admin.ai-services.orders.index', compact('orders'));
    }

    public function show(AiServiceOrder $order)
    {
        $order->load(['user', 'service.category', 'processor']);
        return view('admin.ai-services.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, AiServiceOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,approved,rejected,completed,cancelled',
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
            $notificationService->aiServiceOrderStatusUpdated($order, $oldStatus);
        }

        return redirect()->route('admin.ai-services.orders.show', $order)
            ->with('success', __('messages.ai_service_order_status_updated'));
    }
}
