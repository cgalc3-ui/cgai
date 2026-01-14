<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRatingRequest;
use App\Models\Booking;
use App\Models\Rating;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class RatingController extends Controller
{
    use ApiResponseTrait;
    /**
     * Create a new rating for a completed booking
     */
    public function store(StoreRatingRequest $request)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $customer = $request->user();

        // Check if user is a customer
        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        $ratableId = $request->ratable_id;
        $ratableType = $request->ratable_type;
        $bookingId = $request->booking_id;

        // If booking_id is provided, infer ratable
        if ($bookingId) {
            $booking = Booking::findOrFail($bookingId);

            // Check if booking belongs to the customer
            if ($booking->customer_id !== $customer->id) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.booking_not_belongs_to_customer'),
                ], 403);
            }

            // Check if booking is completed
            if ($booking->status !== 'completed' && $booking->actual_status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.rating_only_completed_bookings'),
                ], 422);
            }

            $ratableId = $booking->booking_type === 'consultation' ? $booking->consultation_id : $booking->service_id;
            $ratableType = $booking->booking_type === 'consultation' ? 'consultation' : 'service';
        }

        // Validate ratable
        if (!$ratableId || !$ratableType) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تحديد العنصر المراد تقييمه أو رقم الحجز',
            ], 422);
        }

        // Check if customer already rated this ratable
        $query = Rating::where('ratable_id', $ratableId)
            ->where('ratable_type', $ratableType)
            ->where('customer_id', $customer->id);

        if ($bookingId) {
            $query->where('booking_id', $bookingId);
        }

        $existingRating = $query->first();

        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => __('messages.rating_already_exists'),
            ], 422);
        }

        // Create rating
        $rating = Rating::create([
            'ratable_id' => $ratableId,
            'ratable_type' => $ratableType,
            'booking_id' => $bookingId,
            'customer_id' => $customer->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true, // Auto-approve for now, or match existing logic
        ]);

        $rating->load(['customer', 'ratable']);

        return response()->json([
            'success' => true,
            'message' => __('messages.rating_created_success'),
            'data' => $this->filterLocaleColumns($rating),
        ], 201);
    }

    /**
     * Get all ratings (public endpoint)
     */
    public function index(Request $request)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $query = Rating::with(['customer', 'ratable']);

        // Filter by rating if provided
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by type if provided
        if ($request->has('ratable_type')) {
            $query->where('ratable_type', $request->ratable_type);
        }

        // Paginate results
        $perPage = $request->get('per_page', 10);
        $ratings = $query->latest()->paginate($perPage);

        // Filter locale columns
        $ratings->getCollection()->transform(function ($rating) {
            return $this->filterLocaleColumns($rating);
        });

        return response()->json([
            'success' => true,
            'data' => $ratings,
        ]);
    }

    /**
     * Get customer's own ratings
     */
    public function myRatings(Request $request)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        $ratings = Rating::with(['ratable', 'booking'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(10);

        // Filter locale columns
        $ratings->getCollection()->transform(function ($rating) {
            return $this->filterLocaleColumns($rating);
        });

        return response()->json([
            'success' => true,
            'data' => $ratings,
        ]);
    }

    /**
     * Get rating statistics
     */
    public function statistics(Request $request)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $totalRatings = Rating::count();
        $averageRating = Rating::avg('rating');
        $ratingDistribution = Rating::selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->rating => $item->count];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'total_ratings' => $totalRatings,
                'average_rating' => round($averageRating, 2),
                'rating_distribution' => $ratingDistribution,
            ],
        ]);
    }
}
