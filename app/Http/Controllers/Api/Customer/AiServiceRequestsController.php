<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\AiServiceRequest;
use App\Models\AiServiceCategory;
use App\Models\AiServiceAttachment;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AiServiceRequestsController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get list of categories for custom requests
     */
    public function categories(Request $request)
    {
        $locale = app()->getLocale();

        $categories = AiServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function ($category) use ($locale) {
                return $this->filterLocaleColumns($category);
            });

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
            ],
            'message' => __('messages.ai_service_categories_loaded_success'),
        ]);
    }

    /**
     * Get customer's custom requests
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $locale = app()->getLocale();

        $query = AiServiceRequest::where('user_id', $user->id)
            ->with(['category', 'attachments']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $perPage = $request->get('per_page', 10);
        $requests = $query->paginate($perPage);

        // Transform data
        $requests->getCollection()->transform(function ($request) use ($locale) {
            return $this->formatRequestData($request, $locale);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'requests' => $requests->items(),
                'pagination' => [
                    'current_page' => $requests->currentPage(),
                    'per_page' => $requests->perPage(),
                    'total' => $requests->total(),
                    'last_page' => $requests->lastPage(),
                    'from' => $requests->firstItem(),
                    'to' => $requests->lastItem(),
                ],
            ],
            'message' => __('messages.ai_service_requests_loaded_success'),
        ]);
    }

    /**
     * Get request details
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $locale = app()->getLocale();

        $aiServiceRequest = AiServiceRequest::where('user_id', $user->id)
            ->with(['category', 'attachments', 'processor'])
            ->findOrFail($id);

        $data = $this->formatRequestDetails($aiServiceRequest, $locale);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => __('messages.ai_service_request_details_loaded_success'),
        ]);
    }

    /**
     * Create new custom request
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $request->validate([
            'category_id' => 'required|exists:ai_service_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'use_case' => 'required|string|max:2000',
            'expected_features' => 'nullable|array',
            'expected_features.*' => 'string|max:255',
            'budget_range' => 'required|in:low,medium,high,custom',
            'custom_budget' => 'nullable|numeric|min:0|required_if:budget_range,custom',
            'urgency' => 'required|in:low,medium,high',
            'deadline' => 'nullable|date|after:today',
            'contact_preference' => 'required|in:phone,email,both',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $aiServiceRequest = AiServiceRequest::create([
                'user_id' => $user->id,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'use_case' => $request->use_case,
                'expected_features' => $request->expected_features,
                'budget_range' => $request->budget_range,
                'custom_budget' => $request->budget_range === 'custom' ? $request->custom_budget : null,
                'urgency' => $request->urgency,
                'deadline' => $request->deadline,
                'contact_preference' => $request->contact_preference,
                'status' => 'pending',
                'currency' => 'SAR',
            ]);

            // Handle attachments
            if ($request->hasFile('attachments')) {
                $order = 1;
                foreach ($request->file('attachments') as $file) {
                    $filePath = $file->store('ai-service-requests/' . $aiServiceRequest->id, 'public');
                    
                    // Determine file type
                    $mimeType = $file->getMimeType();
                    $fileType = 'other';
                    if (str_starts_with($mimeType, 'image/')) {
                        $fileType = 'image';
                    } elseif (str_starts_with($mimeType, 'video/')) {
                        $fileType = 'video';
                    } elseif (in_array($mimeType, ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
                        $fileType = 'document';
                    }

                    AiServiceAttachment::create([
                        'ai_service_request_id' => $aiServiceRequest->id,
                        'file_path' => Storage::url($filePath),
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $fileType,
                        'file_size' => $file->getSize(),
                        'order' => $order++,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'request_id' => $aiServiceRequest->id,
                    'status' => $aiServiceRequest->status,
                    'created_at' => $aiServiceRequest->created_at->toIso8601String(),
                ],
                'message' => __('messages.ai_service_request_created_success'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('messages.error'),
            ], 500);
        }
    }

    /**
     * Update request (only if status is pending)
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $aiServiceRequest = AiServiceRequest::where('user_id', $user->id)
            ->findOrFail($id);

        // Only allow update if status is pending
        if ($aiServiceRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => __('messages.ai_service_request_cannot_update'),
            ], 400);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:5000',
            'use_case' => 'sometimes|required|string|max:2000',
            'expected_features' => 'nullable|array',
            'expected_features.*' => 'string|max:255',
            'budget_range' => 'sometimes|required|in:low,medium,high,custom',
            'custom_budget' => 'nullable|numeric|min:0|required_if:budget_range,custom',
            'urgency' => 'sometimes|required|in:low,medium,high',
            'deadline' => 'nullable|date|after:today',
            'contact_preference' => 'sometimes|required|in:phone,email,both',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $updateData = $request->only([
                'title', 'description', 'use_case', 'expected_features',
                'budget_range', 'urgency', 'deadline', 'contact_preference'
            ]);

            if ($request->budget_range === 'custom') {
                $updateData['custom_budget'] = $request->custom_budget;
            } else {
                $updateData['custom_budget'] = null;
            }

            $aiServiceRequest->update($updateData);

            // Handle new attachments
            if ($request->hasFile('attachments')) {
                $maxOrder = $aiServiceRequest->attachments()->max('order') ?? 0;
                $order = $maxOrder + 1;
                
                foreach ($request->file('attachments') as $file) {
                    $filePath = $file->store('ai-service-requests/' . $aiServiceRequest->id, 'public');
                    
                    $mimeType = $file->getMimeType();
                    $fileType = 'other';
                    if (str_starts_with($mimeType, 'image/')) {
                        $fileType = 'image';
                    } elseif (str_starts_with($mimeType, 'video/')) {
                        $fileType = 'video';
                    } elseif (in_array($mimeType, ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
                        $fileType = 'document';
                    }

                    AiServiceAttachment::create([
                        'ai_service_request_id' => $aiServiceRequest->id,
                        'file_path' => Storage::url($filePath),
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $fileType,
                        'file_size' => $file->getSize(),
                        'order' => $order++,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'request_id' => $aiServiceRequest->id,
                    'status' => $aiServiceRequest->status,
                    'updated_at' => $aiServiceRequest->updated_at->toIso8601String(),
                ],
                'message' => __('messages.ai_service_request_updated_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('messages.error'),
            ], 500);
        }
    }

    /**
     * Delete/Cancel request
     */
    public function destroy($id)
    {
        $user = request()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $aiServiceRequest = AiServiceRequest::where('user_id', $user->id)
            ->findOrFail($id);

        // Only allow delete if status is pending or cancelled
        if (!in_array($aiServiceRequest->status, ['pending', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => __('messages.ai_service_request_cannot_delete'),
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Delete attachments
            foreach ($aiServiceRequest->attachments as $attachment) {
                $path = str_replace(Storage::url(''), '', $attachment->file_path);
                if (empty($path) || $path === $attachment->file_path) {
                    $path = str_replace('/storage/', '', $attachment->file_path);
                }
                if (!empty($path)) {
                    Storage::disk('public')->delete($path);
                }
                $attachment->delete();
            }

            $aiServiceRequest->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('messages.ai_service_request_deleted_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('messages.error'),
            ], 500);
        }
    }

    /**
     * Accept quote
     */
    public function acceptQuote(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $aiServiceRequest = AiServiceRequest::where('user_id', $user->id)
            ->findOrFail($id);

        if ($aiServiceRequest->status !== 'quoted') {
            return response()->json([
                'success' => false,
                'message' => __('messages.ai_service_request_not_quoted'),
            ], 400);
        }

        $aiServiceRequest->update([
            'status' => 'approved',
            'final_price' => $aiServiceRequest->estimated_price,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'request_id' => $aiServiceRequest->id,
                'status' => $aiServiceRequest->status,
            ],
            'message' => __('messages.ai_service_request_quote_accepted'),
        ]);
    }

    /**
     * Reject quote
     */
    public function rejectQuote(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $aiServiceRequest = AiServiceRequest::where('user_id', $user->id)
            ->findOrFail($id);

        if ($aiServiceRequest->status !== 'quoted') {
            return response()->json([
                'success' => false,
                'message' => __('messages.ai_service_request_not_quoted'),
            ], 400);
        }

        $aiServiceRequest->update([
            'status' => 'rejected',
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'request_id' => $aiServiceRequest->id,
                'status' => $aiServiceRequest->status,
            ],
            'message' => __('messages.ai_service_request_quote_rejected'),
        ]);
    }

    /**
     * Format request data for list
     */
    private function formatRequestData($request, $locale)
    {
        return [
            'id' => $request->id,
            'title' => $request->title,
            'category' => $request->category ? [
                'id' => $request->category->id,
                'name' => $request->category->trans('name'),
                'name_en' => $request->category->name_en,
                'slug' => $request->category->slug,
            ] : null,
            'status' => $request->status,
            'budget_range' => $request->budget_range,
            'custom_budget' => $request->custom_budget ? (float) $request->custom_budget : null,
            'urgency' => $request->urgency,
            'estimated_price' => $request->estimated_price ? (float) $request->estimated_price : null,
            'created_at' => $request->created_at->toIso8601String(),
            'updated_at' => $request->updated_at->toIso8601String(),
        ];
    }

    /**
     * Format request details
     */
    private function formatRequestDetails($request, $locale)
    {
        $data = $this->formatRequestData($request, $locale);
        
        $data['description'] = $request->description;
        $data['use_case'] = $request->use_case;
        $data['expected_features'] = $request->expected_features ?? [];
        $data['deadline'] = $request->deadline ? $request->deadline->toIso8601String() : null;
        $data['contact_preference'] = $request->contact_preference;
        $data['final_price'] = $request->final_price ? (float) $request->final_price : null;
        $data['admin_notes'] = $request->admin_notes;
        $data['quoted_at'] = $request->quoted_at ? $request->quoted_at->toIso8601String() : null;
        $data['started_at'] = $request->started_at ? $request->started_at->toIso8601String() : null;
        $data['completed_at'] = $request->completed_at ? $request->completed_at->toIso8601String() : null;
        $data['processed_at'] = $request->processed_at ? $request->processed_at->toIso8601String() : null;
        
        // Add attachments
        $data['attachments'] = $request->attachments->map(function ($attachment) {
            return [
                'id' => $attachment->id,
                'file_path' => $attachment->file_path,
                'file_name' => $attachment->file_name,
                'file_type' => $attachment->file_type,
                'file_size' => $attachment->file_size,
            ];
        });

        return $data;
    }
}
