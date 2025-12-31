<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\SubscriptionRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CustomerController extends Controller
{
    /**
     * Get authenticated customer profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        return response()->json([
            'success' => true,
            'customer' => $user,
        ]);
    }

    /**
     * Update customer profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        if (!$user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $user->update($request->only(['name']));

        return response()->json([
            'success' => true,
            'message' => __('messages.profile_updated_success'),
            'customer' => $user->fresh(),
        ]);
    }

    /**
     * Get customer dashboard data
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        if (!$user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        // Get active subscription
        $activeSubscription = $user->getActiveSubscription();
        $subscriptionData = null;
        if ($activeSubscription) {
            $subscriptionData = [
                'id' => $activeSubscription->id,
                'subscription' => [
                    'id' => $activeSubscription->subscription->id,
                    'name' => $activeSubscription->subscription->name,
                    'description' => $activeSubscription->subscription->description,
                    'price' => $activeSubscription->subscription->price,
                    'duration_type' => $activeSubscription->subscription->duration_type,
                    'features' => $activeSubscription->subscription->features,
                ],
                'status' => $activeSubscription->status,
                'started_at' => $activeSubscription->started_at,
                'expires_at' => $activeSubscription->expires_at,
                'is_active' => $activeSubscription->isActive(),
            ];
        }

        // Get pending subscription request
        $pendingRequest = SubscriptionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('subscription')
            ->first();

        // Booking Statistics
        $totalBookings = Booking::where('customer_id', $user->id)->count();
        $pendingBookings = Booking::where('customer_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $confirmedBookings = Booking::where('customer_id', $user->id)
            ->where('status', 'confirmed')
            ->count();
        $inProgressBookings = Booking::where('customer_id', $user->id)
            ->where('status', 'in_progress')
            ->count();
        $completedBookings = Booking::where('customer_id', $user->id)
            ->where('status', 'completed')
            ->count();
        $cancelledBookings = Booking::where('customer_id', $user->id)
            ->where('status', 'cancelled')
            ->count();

        // Payment Statistics
        $totalSpent = Booking::where('customer_id', $user->id)
            ->where('payment_status', 'paid')
            ->sum('total_price');
        $paidBookings = Booking::where('customer_id', $user->id)
            ->where('payment_status', 'paid')
            ->count();
        $unpaidBookings = Booking::where('customer_id', $user->id)
            ->where('payment_status', 'unpaid')
            ->count();

        // Recent Bookings (last 5)
        $recentBookings = Booking::where('customer_id', $user->id)
            ->with([
                'service' => function($q) {
                    $q->with('subCategory.category');
                },
                'consultation' => function($q) {
                    $q->with('category');
                },
                'employee.user',
                'timeSlot'
            ])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'booking_type' => $booking->booking_type,
                    'service' => $booking->service ? [
                        'id' => $booking->service->id,
                        'name' => $booking->service->name,
                        'sub_category' => $booking->service->subCategory ? [
                            'id' => $booking->service->subCategory->id,
                            'name' => $booking->service->subCategory->name,
                            'category' => $booking->service->subCategory->category ? [
                                'id' => $booking->service->subCategory->category->id,
                                'name' => $booking->service->subCategory->category->name,
                            ] : null,
                        ] : null,
                    ] : null,
                    'consultation' => $booking->consultation ? [
                        'id' => $booking->consultation->id,
                        'name' => $booking->consultation->name,
                        'category' => $booking->consultation->category ? [
                            'id' => $booking->consultation->category->id,
                            'name' => $booking->consultation->category->name,
                        ] : null,
                    ] : null,
                    'employee' => $booking->employee ? [
                        'id' => $booking->employee->id,
                        'user' => $booking->employee->user ? [
                            'id' => $booking->employee->user->id,
                            'name' => $booking->employee->user->name,
                        ] : null,
                    ] : null,
                    'booking_date' => $booking->booking_date,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'total_price' => $booking->total_price,
                    'status' => $booking->status,
                    'actual_status' => $booking->actual_status,
                    'payment_status' => $booking->payment_status,
                    'created_at' => $booking->created_at,
                ];
            });

        // Ticket Statistics
        $totalTickets = Ticket::where('user_id', $user->id)->count();
        $openTickets = Ticket::where('user_id', $user->id)
            ->where('status', 'open')
            ->count();
        $inProgressTickets = Ticket::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->count();
        $resolvedTickets = Ticket::where('user_id', $user->id)
            ->where('status', 'resolved')
            ->count();

        // Recent Tickets (last 5)
        $recentTickets = Ticket::where('user_id', $user->id)
            ->with(['assignedUser', 'latestMessage'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($ticket) {
                return [
                    'id' => $ticket->id,
                    'subject' => $ticket->subject,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'assigned_to' => $ticket->assignedUser ? [
                        'id' => $ticket->assignedUser->id,
                        'name' => $ticket->assignedUser->name,
                    ] : null,
                    'latest_message' => $ticket->latestMessage ? [
                        'id' => $ticket->latestMessage->id,
                        'message' => $ticket->latestMessage->message,
                        'created_at' => $ticket->latestMessage->created_at,
                    ] : null,
                    'created_at' => $ticket->created_at,
                    'resolved_at' => $ticket->resolved_at,
                ];
            });

        // Unread Notifications Count
        $unreadNotificationsCount = $user->notifications()->unread()->count();

        // Today's Bookings
        $todayBookings = Booking::where('customer_id', $user->id)
            ->whereDate('booking_date', Carbon::today())
            ->count();

        // Upcoming Bookings (next 7 days)
        $upcomingBookings = Booking::where('customer_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->whereDate('booking_date', '>=', Carbon::today())
            ->whereDate('booking_date', '<=', Carbon::today()->addDays(7))
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'subscription' => $subscriptionData,
                'pending_subscription_request' => $pendingRequest ? [
                    'id' => $pendingRequest->id,
                    'subscription' => [
                        'id' => $pendingRequest->subscription->id,
                        'name' => $pendingRequest->subscription->name,
                    ],
                    'status' => $pendingRequest->status,
                    'created_at' => $pendingRequest->created_at,
                ] : null,
                'stats' => [
                    'bookings' => [
                        'total' => $totalBookings,
                        'pending' => $pendingBookings,
                        'confirmed' => $confirmedBookings,
                        'in_progress' => $inProgressBookings,
                        'completed' => $completedBookings,
                        'cancelled' => $cancelledBookings,
                        'today' => $todayBookings,
                        'upcoming' => $upcomingBookings,
                    ],
                    'payments' => [
                        'total_spent' => (float) $totalSpent,
                        'paid_bookings' => $paidBookings,
                        'unpaid_bookings' => $unpaidBookings,
                    ],
                    'tickets' => [
                        'total' => $totalTickets,
                        'open' => $openTickets,
                        'in_progress' => $inProgressTickets,
                        'resolved' => $resolvedTickets,
                    ],
                    'notifications' => [
                        'unread_count' => $unreadNotificationsCount,
                    ],
                ],
                'recent_bookings' => $recentBookings,
                'recent_tickets' => $recentTickets,
            ],
        ]);
    }
}
