<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\AiService;
use App\Models\AiServiceCategory;
use App\Models\AiServiceOrder;
use App\Models\AiServiceFavorite;
use App\Models\Rating;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;

class AiServicesController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get list of AI services
     */
    public function index(Request $request)
    {
        // Locale is set by SetApiLocale middleware from header
        $locale = app()->getLocale();
        $user = $request->user();

        $query = AiService::with(['category', 'images'])
            ->where('is_active', true);

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $locale) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");

                if ($locale === 'en') {
                    $q->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhere('description_en', 'like', "%{$search}%");
                }
            });
        }

        // Filter by free/paid
        if ($request->has('type')) {
            $types = is_array($request->type) ? $request->type : explode(',', $request->type);
            if (count($types) == 1) {
                if ($types[0] === 'free')
                    $query->where('is_free', true);
                if ($types[0] === 'paid')
                    $query->where('is_free', false);
            }
            // If both are selected, no filter needed as it shows all
        } elseif ($request->has('is_free')) {
            $query->where('is_free', $request->boolean('is_free'));
        }

        // Filter by tag (Technologies)
        if ($request->has('tag')) {
            $tag = $request->tag;
            $query->where('tags', 'like', "%\"{$tag}\"%");
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $perPage = $request->get('per_page', 10);
        $services = $query->paginate($perPage);

        // Transform data
        $services->getCollection()->transform(function ($service) use ($locale, $user) {
            $isFavorite = false;
            if ($user) {
                $isFavorite = $service->favorites()->where('user_id', $user->id)->exists();
            }
            return $this->formatServiceCard($service, $locale, $isFavorite);
        });

        // Get categories with counts
        $categories = AiServiceCategory::where('is_active', true)
            ->withCount(['activeServices'])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($category) use ($locale) {
                $data = $this->filterLocaleColumns($category);
                return $data;
            });

        // Get popular tags (Technologies) from ai_service_tags table
        $tags = \App\Models\AiServiceTag::active()
            ->ordered()
            ->get()
            ->map(function ($tag) use ($locale) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->trans('name'),
                    'slug' => $tag->slug,
                    'image' => $tag->image ? (strpos($tag->image, '/storage/') === 0 ? asset($tag->image) : asset('storage/' . $tag->image)) : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services->items(),
                'pagination' => [
                    'current_page' => $services->currentPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                    'last_page' => $services->lastPage(),
                    'from' => $services->firstItem(),
                    'to' => $services->lastItem(),
                ],
                'categories' => $categories,
                'popular_tags' => $tags,
            ],
            'message' => __('messages.ai_services_loaded_success'),
        ]);
    }

    /**
     * Get service details
     */
    public function show(Request $request, $id)
    {
        // Locale is set by SetApiLocale middleware from header
        $locale = app()->getLocale();
        $user = $request->user(); // Can be null for public access

        $service = AiService::with([
            'category',
            'images',
            'features',
            'screenshots',
            'reviews.user'
        ])->findOrFail($id);

        // Increment views count
        $service->increment('views_count');

        // Check if favorited by user
        $isFavorite = false;
        if ($user) {
            $isFavorite = AiServiceFavorite::where('user_id', $user->id)
                ->where('ai_service_id', $service->id)
                ->exists();
        }

        // Get related services (same category, excluding current)
        $relatedServices = AiService::where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->where('is_active', true)
            ->with(['images'])
            ->limit(4)
            ->get()
            ->map(function ($relatedService) use ($locale) {
                return $this->formatServiceListItem($relatedService, $locale);
            });

        // Format rating breakdown
        $ratingBreakdown = $this->getRatingBreakdown($service->id);

        $data = $this->formatServiceDetails($service, $locale, $isFavorite, $relatedServices, $ratingBreakdown);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => __('messages.ai_service_details_loaded_success'),
        ]);
    }

    /**
     * Create purchase order
     */
    public function purchase(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $request->validate([
            'pricing_plan_id' => 'nullable|integer',
            'notes' => 'nullable|string|max:1000',
            'contact_preference' => 'required|in:phone,email,both',
        ]);

        $service = AiService::findOrFail($id);

        if (!$service->is_active) {
            return response()->json([
                'success' => false,
                'message' => __('messages.ai_service_not_available'),
            ], 400);
        }

        DB::beginTransaction();
        try {
            $order = AiServiceOrder::create([
                'user_id' => $user->id,
                'ai_service_id' => $service->id,
                'price' => $service->price,
                'currency' => $service->currency,
                'status' => 'pending',
                'notes' => $request->notes,
                'contact_preference' => $request->contact_preference,
                'pricing_plan_id' => $request->pricing_plan_id,
            ]);

            $service->increment('purchases_count');

            // Send notifications
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->aiServiceOrderCreated($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'service_id' => $service->id,
                    'service_name' => $service->trans('name'),
                    'price' => (float) $order->price,
                    'status' => $order->status,
                    'created_at' => $order->created_at->toIso8601String(),
                ],
                'message' => __('messages.ai_service_order_created_success'),
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
     * Create inquiry ticket
     */
    public function inquiry(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'contact_preference' => 'required|in:phone,email,both',
        ]);

        $service = AiService::findOrFail($id);

        DB::beginTransaction();
        try {
            $ticket = Ticket::create([
                'user_id' => $user->id,
                'subject' => $request->subject . ' - ' . $service->trans('name'),
                'subject_en' => $request->subject . ' - ' . ($service->name_en ?: $service->name),
                'description' => $request->message . "\n\n" . __('messages.ai_service_inquiry_service_info', ['service_name' => $service->trans('name')]),
                'description_en' => $request->message . "\n\n" . __('messages.ai_service_inquiry_service_info', ['service_name' => $service->name_en ?: $service->name]),
                'priority' => 'medium',
                'status' => 'open',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'ticket_id' => $ticket->id,
                    'service_id' => $service->id,
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at->toIso8601String(),
                ],
                'message' => __('messages.ai_service_inquiry_created_success'),
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
     * Toggle favorite
     */
    public function toggleFavorite(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $service = AiService::findOrFail($id);

        $favorite = AiServiceFavorite::where('user_id', $user->id)
            ->where('ai_service_id', $service->id)
            ->first();

        DB::beginTransaction();
        try {
            if ($favorite) {
                $favorite->delete();
                $service->decrement('favorites_count');
                $isFavorite = false;
            } else {
                AiServiceFavorite::create([
                    'user_id' => $user->id,
                    'ai_service_id' => $service->id,
                ]);
                $service->increment('favorites_count');
                $isFavorite = true;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'is_favorite' => $isFavorite,
                ],
                'message' => $isFavorite
                    ? __('messages.ai_service_added_to_favorites')
                    : __('messages.ai_service_removed_from_favorites'),
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
     * Get favorite services
     */
    public function favorites(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $locale = app()->getLocale();

        $favorites = AiServiceFavorite::where('user_id', $user->id)
            ->with(['service.images'])
            ->orderBy('created_at', 'desc')
            ->get();

        $services = $favorites->map(function ($favorite) use ($locale) {
            $service = $favorite->service;
            $data = $this->formatServiceListItem($service, $locale);
            $data['favorited_at'] = $favorite->created_at->toIso8601String();
            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'services' => $services,
            ],
            'message' => __('messages.ai_services_favorites_loaded_success'),
        ]);
    }

    /**
     * Get customer orders
     */
    public function orders(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 401);
        }

        $locale = app()->getLocale();

        $query = AiServiceOrder::where('user_id', $user->id)
            ->with(['service.category', 'service.images']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $perPage = $request->get('per_page', 10);
        $orders = $query->paginate($perPage);

        // Transform data
        $orders->getCollection()->transform(function ($order) use ($locale) {
            return $this->formatOrderData($order, $locale);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                ],
            ],
            'message' => __('messages.ai_service_orders_loaded_success'),
        ]);
    }

    /**
     * Format service data for list
     */
    private function formatServiceData($service, $locale)
    {
        $data = $this->filterLocaleColumns($service);

        // Get main image
        $mainImage = $service->images()->where('type', 'main')->first();
        $data['main_image'] = $mainImage ? $mainImage->url : null;

        // Get images array
        $data['images'] = $service->images->map(function ($image) use ($locale) {
            $imageData = $this->filterLocaleColumns($image);
            // Remove alt fields
            unset($imageData['alt'], $imageData['alt_en']);
            return $imageData;
        });

        // Get features
        $data['features'] = $service->features->map(function ($feature) use ($locale) {
            return $this->filterLocaleColumns($feature);
        });

        // Calculate discount percentage
        if ($service->original_price && $service->original_price > $service->price) {
            $data['discount_percentage'] = round((($service->original_price - $service->price) / $service->original_price) * 100, 2);
        } else {
            $data['discount_percentage'] = null;
        }

        // Format dates
        $data['created_at'] = $service->created_at->toIso8601String();
        $data['updated_at'] = $service->updated_at->toIso8601String();

        $data['is_free'] = (bool) $service->is_free;
        $data['type'] = $service->is_free ? 'free' : 'paid';

        // Remove unwanted fields
        unset($data['video_thumbnail'], $data['specifications'], $data['tags']);

        return $data;
    }

    /**
     * Format service list item (simplified)
     */
    private function formatServiceListItem($service, $locale)
    {
        $mainImage = $service->images()->where('type', 'main')->first();

        return [
            'id' => $service->id,
            'name' => $service->trans('name'),
            'main_image' => $mainImage ? $mainImage->url : null,
            'price' => (float) $service->price,
            'is_free' => (bool) $service->is_free,
            'type' => $service->is_free ? 'free' : 'paid',
        ];
    }

    /**
     * Format service data for a card (strictly matching UI design)
     */
    private function formatServiceCard($service, $locale, $isFavorite = false)
    {
        $mainImage = $service->images()->where('type', 'main')->first();

        return [
            'id' => $service->id,
            'name' => $service->trans('name'),
            'short_description' => $service->trans('short_description'),
            'main_image' => $mainImage ? $mainImage->url : null,
            'category' => [
                'id' => $service->category->id,
                'name' => $service->category->trans('name'),
                'slug' => $service->category->slug,
            ],
            'is_free' => (bool) $service->is_free,
            'is_favorite' => $isFavorite,
            'rating' => (float) $service->rating,
        ];
    }

    /**
     * Format service details
     */
    private function formatServiceDetails($service, $locale, $isFavorite, $relatedServices, $ratingBreakdown)
    {
        $data = $this->formatServiceData($service, $locale);

        // Add full description
        $data['full_description'] = $service->trans('full_description') ?: $service->trans('description');

        // Add screenshots
        $data['screenshots'] = $service->screenshots->map(function ($screenshot) use ($locale) {
            $screenshotData = $this->filterLocaleColumns($screenshot);
            return $screenshotData;
        });

        // Add rating details
        $data['rating'] = [
            'average' => (float) $service->rating,
            'total_reviews' => $service->reviews_count,
            'breakdown' => $ratingBreakdown,
        ];

        // Add reviews
        $data['reviews'] = $service->reviews->map(function ($review) use ($locale) {
            return [
                'id' => $review->id,
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'avatar' => $review->user->avatar ? asset('storage/' . $review->user->avatar) : null,
                ],
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at->toIso8601String(),
            ];
        });

        // Add is_favorite
        $data['is_favorite'] = $isFavorite;

        // Add related services
        $data['related_services'] = $relatedServices;

        // Add statistics
        $data['statistics'] = [
            'views' => $service->views_count,
            'purchases' => $service->purchases_count,
            'favorites' => $service->favorites_count,
        ];

        return $data;
    }

    /**
     * Get rating breakdown
     */
    private function getRatingBreakdown($serviceId)
    {
        $breakdown = Rating::where('ratable_id', $serviceId)
            ->where('ratable_type', 'ai_service')
            ->where('is_approved', true)
            ->select('rating', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        return [
            '5' => $breakdown[5] ?? 0,
            '4' => $breakdown[4] ?? 0,
            '3' => $breakdown[3] ?? 0,
            '2' => $breakdown[2] ?? 0,
            '1' => $breakdown[1] ?? 0,
        ];
    }

    /**
     * Format order data
     */
    private function formatOrderData($order, $locale)
    {
        $service = $order->service;
        $mainImage = $service ? $service->images()->where('type', 'main')->first() : null;

        return [
            'id' => $order->id,
            'service' => $service ? [
                'id' => $service->id,
                'name' => $service->trans('name'),
                'name_en' => $service->name_en,
                'main_image' => $mainImage ? $mainImage->url : null,
                'category' => $service->category ? [
                    'id' => $service->category->id,
                    'name' => $service->category->trans('name'),
                    'name_en' => $service->category->name_en,
                    'slug' => $service->category->slug,
                ] : null,
            ] : null,
            'price' => (float) $order->price,
            'currency' => $order->currency,
            'status' => $order->status,
            'notes' => $order->notes,
            'contact_preference' => $order->contact_preference,
            'created_at' => $order->created_at->toIso8601String(),
            'updated_at' => $order->updated_at->toIso8601String(),
            'processed_at' => $order->processed_at ? $order->processed_at->toIso8601String() : null,
        ];
    }
}
