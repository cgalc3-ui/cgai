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
        $customer = $request->user();

        // Check if user is a customer
        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        // Get the booking
        $booking = Booking::findOrFail($request->booking_id);

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

        // Check if customer already rated this booking
        $existingRating = Rating::where('booking_id', $booking->id)
            ->where('customer_id', $customer->id)
            ->first();

        if ($existingRating) {
            return response()->json([
                'success' => false,
                'message' => __('messages.rating_already_exists'),
            ], 422);
        }

        // Create rating
        $rating = Rating::create([
            'booking_id' => $booking->id,
            'customer_id' => $customer->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $rating->load(['customer', 'booking.service', 'booking.consultation']);

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
        $query = Rating::with(['customer', 'booking']);

        // Filter by rating if provided
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $ratings = $query->with(['booking.service', 'booking.consultation'])->latest()->paginate($perPage);

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
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        $ratings = Rating::with(['booking.service', 'booking.consultation'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->paginate(15);

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
    public function statistics()
    {
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
