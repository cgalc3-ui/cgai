<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\ReadyApp;
use App\Models\ReadyAppCategory;
use App\Models\ReadyAppOrder;
use App\Models\ReadyAppFavorite;
use App\Models\Rating;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;

class ReadyAppsController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get list of ready apps
     */
    public function index(Request $request)
    {
        // Locale is set by SetApiLocale middleware from header
        $locale = app()->getLocale();

        $query = ReadyApp::with(['category', 'images', 'features'])
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

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $perPage = $request->get('per_page', 10);
        $apps = $query->paginate($perPage);

        // Transform data
        $apps->getCollection()->transform(function ($app) use ($locale) {
            return $this->formatAppData($app, $locale);
        });

        // Get categories with counts
        $categories = ReadyAppCategory::where('is_active', true)
            ->withCount(['activeApps'])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($category) use ($locale) {
                $data = $this->filterLocaleColumns($category);
                // Remove icon field
                unset($data['icon']);
                return $data;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'apps' => $apps->items(),
                'pagination' => [
                    'current_page' => $apps->currentPage(),
                    'per_page' => $apps->perPage(),
                    'total' => $apps->total(),
                    'last_page' => $apps->lastPage(),
                    'from' => $apps->firstItem(),
                    'to' => $apps->lastItem(),
                ],
                'categories' => $categories,
            ],
            'message' => __('messages.ready_apps_loaded_success'),
        ]);
    }

    /**
     * Get app details
     */
    public function show(Request $request, $id)
    {
        // Locale is set by SetApiLocale middleware from header
        $locale = app()->getLocale();
        $user = $request->user(); // Can be null for public access

        $app = ReadyApp::with([
            'category',
            'images',
            'features',
            'screenshots',
            'reviews.user'
        ])->findOrFail($id);

        // Increment views count
        $app->increment('views_count');

        // Check if favorited by user
        $isFavorite = false;
        if ($user) {
            $isFavorite = ReadyAppFavorite::where('user_id', $user->id)
                ->where('ready_app_id', $app->id)
                ->exists();
        }

        // Get related apps (same category, excluding current)
        $relatedApps = ReadyApp::where('category_id', $app->category_id)
            ->where('id', '!=', $app->id)
            ->where('is_active', true)
            ->with(['images'])
            ->limit(4)
            ->get()
            ->map(function ($relatedApp) use ($locale) {
                return $this->formatAppListItem($relatedApp, $locale);
            });

        // Format rating breakdown
        $ratingBreakdown = $this->getRatingBreakdown($app->id);

        $data = $this->formatAppDetails($app, $locale, $isFavorite, $relatedApps, $ratingBreakdown);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => __('messages.ready_app_details_loaded_success'),
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

        $app = ReadyApp::findOrFail($id);

        if (!$app->is_active) {
            return response()->json([
                'success' => false,
                'message' => __('messages.ready_app_not_available'),
            ], 400);
        }

        DB::beginTransaction();
        try {
            $order = ReadyAppOrder::create([
                'user_id' => $user->id,
                'ready_app_id' => $app->id,
                'price' => $app->price,
                'currency' => $app->currency,
                'status' => 'pending',
                'notes' => $request->notes,
                'contact_preference' => $request->contact_preference,
                'pricing_plan_id' => $request->pricing_plan_id,
            ]);

            $app->increment('purchases_count');

            // Send notifications
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->readyAppOrderCreated($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $order->id,
                    'app_id' => $app->id,
                    'app_name' => $app->trans('name'),
                    'price' => (float) $order->price,
                    'status' => $order->status,
                    'created_at' => $order->created_at->toIso8601String(),
                ],
                'message' => __('messages.ready_app_order_created_success'),
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

        $app = ReadyApp::findOrFail($id);

        DB::beginTransaction();
        try {
            $ticket = Ticket::create([
                'user_id' => $user->id,
                'subject' => $request->subject . ' - ' . $app->trans('name'),
                'subject_en' => $request->subject . ' - ' . ($app->name_en ?: $app->name),
                'description' => $request->message . "\n\n" . __('messages.ready_app_inquiry_app_info', ['app_name' => $app->trans('name')]),
                'description_en' => $request->message . "\n\n" . __('messages.ready_app_inquiry_app_info', ['app_name' => $app->name_en ?: $app->name]),
                'priority' => 'medium',
                'status' => 'open',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'ticket_id' => $ticket->id,
                    'app_id' => $app->id,
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at->toIso8601String(),
                ],
                'message' => __('messages.ready_app_inquiry_created_success'),
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

        $app = ReadyApp::findOrFail($id);

        $favorite = ReadyAppFavorite::where('user_id', $user->id)
            ->where('ready_app_id', $app->id)
            ->first();

        DB::beginTransaction();
        try {
            if ($favorite) {
                $favorite->delete();
                $app->decrement('favorites_count');
                $isFavorite = false;
            } else {
                ReadyAppFavorite::create([
                    'user_id' => $user->id,
                    'ready_app_id' => $app->id,
                ]);
                $app->increment('favorites_count');
                $isFavorite = true;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'is_favorite' => $isFavorite,
                ],
                'message' => $isFavorite
                    ? __('messages.ready_app_added_to_favorites')
                    : __('messages.ready_app_removed_from_favorites'),
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
     * Get favorite apps
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

        $favorites = ReadyAppFavorite::where('user_id', $user->id)
            ->with(['app.images'])
            ->orderBy('created_at', 'desc')
            ->get();

        $apps = $favorites->map(function ($favorite) use ($locale) {
            $app = $favorite->app;
            $data = $this->formatAppListItem($app, $locale);
            $data['favorited_at'] = $favorite->created_at->toIso8601String();
            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'apps' => $apps,
            ],
            'message' => __('messages.ready_apps_favorites_loaded_success'),
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

        $query = ReadyAppOrder::where('user_id', $user->id)
            ->with(['app.category', 'app.images']);

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
            'message' => __('messages.ready_app_orders_loaded_success'),
        ]);
    }

    /**
     * Format app data for list
     */
    private function formatAppData($app, $locale)
    {
        $data = $this->filterLocaleColumns($app);

        // Remove icon from category if exists
        if (isset($data['category']) && is_array($data['category'])) {
            unset($data['category']['icon']);
        }

        // Get main image
        $mainImage = $app->images()->where('type', 'main')->first();
        $data['main_image'] = $mainImage ? $mainImage->url : null;

        // Get images array
        $data['images'] = $app->images->map(function ($image) use ($locale) {
            $imageData = $this->filterLocaleColumns($image);
            // Remove alt fields
            unset($imageData['alt'], $imageData['alt_en']);
            return $imageData;
        });

        // Get features
        $data['features'] = $app->features->map(function ($feature) use ($locale) {
            return $this->filterLocaleColumns($feature);
        });

        // Calculate discount percentage
        if ($app->original_price && $app->original_price > $app->price) {
            $data['discount_percentage'] = round((($app->original_price - $app->price) / $app->original_price) * 100, 2);
        } else {
            $data['discount_percentage'] = null;
        }

        // Format dates
        $data['created_at'] = $app->created_at->toIso8601String();
        $data['updated_at'] = $app->updated_at->toIso8601String();

        // Remove unwanted fields
        unset($data['video_thumbnail'], $data['specifications'], $data['tags']);

        return $data;
    }

    /**
     * Format app list item (simplified)
     */
    private function formatAppListItem($app, $locale)
    {
        $mainImage = $app->images()->where('type', 'main')->first();

        return [
            'id' => $app->id,
            'name' => $app->trans('name'),
            'main_image' => $mainImage ? $mainImage->url : null,
            'price' => (float) $app->price,
        ];
    }

    /**
     * Format app details
     */
    private function formatAppDetails($app, $locale, $isFavorite, $relatedApps, $ratingBreakdown)
    {
        $data = $this->formatAppData($app, $locale);

        // Add full description
        $data['full_description'] = $app->trans('full_description') ?: $app->trans('description');

        // Add screenshots
        $data['screenshots'] = $app->screenshots->map(function ($screenshot) use ($locale) {
            $screenshotData = $this->filterLocaleColumns($screenshot);
            // Remove alt fields
            unset($screenshotData['alt'], $screenshotData['alt_en']);
            return $screenshotData;
        });


        // Add rating details
        $data['rating'] = [
            'average' => (float) $app->rating,
            'total_reviews' => $app->reviews_count,
            'breakdown' => $ratingBreakdown,
        ];

        // Add reviews
        $data['reviews'] = $app->reviews->map(function ($review) use ($locale) {
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

        // Add related apps
        $data['related_apps'] = $relatedApps;

        // Add statistics
        $data['statistics'] = [
            'views' => $app->views_count,
            'purchases' => $app->purchases_count,
            'favorites' => $app->favorites_count,
        ];

        return $data;
    }

    /**
     * Get rating breakdown
     */
    private function getRatingBreakdown($appId)
    {
        $breakdown = Rating::where('ratable_id', $appId)
            ->where('ratable_type', 'ready_app')
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
        $app = $order->app;
        $mainImage = $app ? $app->images()->where('type', 'main')->first() : null;

        return [
            'id' => $order->id,
            'app' => $app ? [
                'id' => $app->id,
                'name' => $app->trans('name'),
                'name_en' => $app->name_en,
                'main_image' => $mainImage ? $mainImage->url : null,
                'category' => $app->category ? [
                    'id' => $app->category->id,
                    'name' => $app->category->trans('name'),
                    'name_en' => $app->category->name_en,
                    'slug' => $app->category->slug,
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
