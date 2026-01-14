<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiServiceRequest;
use App\Models\AiServiceAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AiServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = AiServiceRequest::with(['user', 'category', 'processor']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $requests = $query->latest()->paginate(10)->withQueryString();

        // Get categories for filter
        $categories = \App\Models\AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.ai-services.requests.index', compact('requests', 'categories'));
    }

    public function show(AiServiceRequest $request)
    {
        $request->load(['user', 'category', 'processor', 'attachments']);
        return view('admin.ai-services.requests.show', compact('request'));
    }

    public function updateStatus(Request $request, AiServiceRequest $aiServiceRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewing,quoted,approved,in_progress,completed,cancelled,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ];

        // Update timestamps based on status
        if ($request->status === 'quoted' && !$aiServiceRequest->quoted_at) {
            $updateData['quoted_at'] = now();
        } elseif ($request->status === 'in_progress' && !$aiServiceRequest->started_at) {
            $updateData['started_at'] = now();
        } elseif ($request->status === 'completed' && !$aiServiceRequest->completed_at) {
            $updateData['completed_at'] = now();
        }

        $aiServiceRequest->update($updateData);

        return redirect()->route('admin.ai-services.requests.show', $aiServiceRequest)
            ->with('success', __('messages.ai_service_request_status_updated'));
    }

    public function updateQuote(Request $request, AiServiceRequest $aiServiceRequest)
    {
        $request->validate([
            'estimated_price' => 'required|numeric|min:0',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $aiServiceRequest->update([
            'estimated_price' => $request->estimated_price,
            'status' => 'quoted',
            'admin_notes' => $request->admin_notes,
            'quoted_at' => now(),
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return redirect()->route('admin.ai-services.requests.show', $aiServiceRequest)
            ->with('success', __('messages.ai_service_request_quote_updated'));
    }
}
