<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Rating;
use App\Models\ReadyAppOrder;
use App\Models\PointsTransaction;
use App\Models\PointsSetting;
use App\Models\UserSubscription;
use App\Models\SubscriptionRequest;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
            'customer' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'phone_verified_at' => $user->phone_verified_at,
                'role' => $user->role,
                'date_of_birth' => $user->date_of_birth,
                'gender' => $user->gender,
                'avatar' => $user->avatar_url, // Return avatar_url instead of path
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }
    /**
     * Update customer avatar (profile picture)
     */
    public function updateAvatar(Request $request)
    {
        $user = $request->user();

        if (!$user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        // Validate the file
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'avatar.required' => __('messages.avatar_required'),
            'avatar.image' => __('messages.avatar_must_be_image'),
            'avatar.mimes' => __('messages.avatar_invalid_format'),
            'avatar.max' => __('messages.avatar_max_size'),
        ]);

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');

        // Update user avatar
        $user->update(['avatar' => $avatarPath]);
        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => __('messages.avatar_updated_success'),
            'customer' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' =>  $user->avatar_url
            ],
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

        /*
        |--------------------------------------------------------------------------
        | Fix PUT + multipart/form-data
        |--------------------------------------------------------------------------
        | Laravel doesn't hydrate files on PUT requests properly
        | So we manually force Symfony to parse it by changing method to POST
        */
        if ($request->isMethod('put')) {
            $request->setMethod('POST');
        }

        // Validate all fields including avatar
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'gender' => 'sometimes|nullable|in:male,female',
            'date_of_birth' => 'sometimes|nullable|date',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Separate avatar from other data
        $updateData = collect($validated)->except('avatar')->toArray();

        // Handle avatar upload separately
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $updateData['avatar'] = $avatarPath;
        }

        // Update user with all data including avatar
        if (!empty($updateData)) {
            $user->update($updateData);
        }

        // Refresh to get latest data including avatar_url
        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => __('messages.profile_updated_success'),
            'customer' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'avatar_url' => $user->avatar_url,
                'gender' => $user->gender,
                'date_of_birth' => $user->date_of_birth,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Get customer dashboard data
     */
    public function dashboard(Request $request)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $user = $request->user();

        if (!$user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        // Eager load relationships for better performance
        $bookingsQuery = \App\Models\Booking::where('customer_id', $user->id)
            ->with(['service.subCategory.category', 'consultation', 'employee.user', 'rating']);

        // Get all paid bookings (all bookings are paid)
        $paidBookings = (clone $bookingsQuery)->where('payment_status', 'paid')->get();
        
        // Bookings Stats
        $bookingsStats = [
            'total' => $paidBookings->count(),
            'upcoming' => $paidBookings->filter(function ($booking) {
                return $booking->booking_date && $booking->start_time && 
                       $booking->actual_status === 'pending' && 
                       \Carbon\Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time)->isFuture();
            })->count(),
            'pending' => $paidBookings->where('status', 'pending')->count(),
            'confirmed' => $paidBookings->where('status', 'confirmed')->count(),
            'in_progress' => $paidBookings->where('status', 'in_progress')->count(),
            'completed' => $paidBookings->where('status', 'completed')->count(),
            'cancelled' => $paidBookings->where('status', 'cancelled')->count(),
            'today' => $paidBookings->filter(function ($booking) {
                return $booking->booking_date && $booking->booking_date->isToday();
            })->count(),
        ];

        // Payments Stats
        $totalSpent = $paidBookings->sum('total_price');
        $totalInvoices = $paidBookings->count();
        $paidInvoices = $totalInvoices; // All bookings are paid
        $thisMonthSpent = $paidBookings->filter(function ($booking) {
            return $booking->created_at && $booking->created_at->isCurrentMonth();
        })->sum('total_price');
        $averagePerBooking = $totalInvoices > 0 ? $totalSpent / $totalInvoices : 0;

        $paymentsStats = [
            'total_spent' => (float) $totalSpent,
            'total_invoices' => $totalInvoices,
            'paid_invoices' => $paidInvoices,
            'this_month_spent' => (float) $thisMonthSpent,
            'average_per_booking' => (float) round($averagePerBooking, 2),
        ];

        // Wallet Stats
        $wallet = $user->getOrCreateWallet();
        $pointsSettings = \App\Models\PointsSetting::getActive();
        $pointsPerRiyal = $pointsSettings ? (float) $pointsSettings->points_per_riyal : 10.0;
        
        $pointsUsedThisMonth = \App\Models\PointsTransaction::where('user_id', $user->id)
            ->where('type', 'usage')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('points');

        $walletStats = [
            'current_balance' => (float) $wallet->balance,
            'points_per_riyal' => $pointsPerRiyal,
            'points_used_this_month' => (int) $pointsUsedThisMonth,
        ];

        // Ratings Stats
        $ratings = \App\Models\Rating::where('customer_id', $user->id)->get();
        $ratingsAverage = $ratings->count() > 0 ? $ratings->avg('rating') : 0;
        $ratingsDistribution = [
            '5' => $ratings->where('rating', 5)->count(),
            '4' => $ratings->where('rating', 4)->count(),
            '3' => $ratings->where('rating', 3)->count(),
            '2' => $ratings->where('rating', 2)->count(),
            '1' => $ratings->where('rating', 1)->count(),
        ];

        $ratingsStats = [
            'average' => (float) round($ratingsAverage, 2),
            'total' => $ratings->count(),
            'distribution' => $ratingsDistribution,
        ];

        // Ready Apps Stats
        $readyAppOrders = \App\Models\ReadyAppOrder::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();
        
        $readyAppsStats = [
            'purchased_count' => $readyAppOrders->count(),
            'total_spent' => (float) $readyAppOrders->sum('price'),
        ];

        // Services Stats
        $serviceBookings = $paidBookings->where('booking_type', 'service');
        $totalBooked = $serviceBookings->count();
        
        // Most popular services
        $mostPopularServices = $serviceBookings->groupBy('service_id')
            ->map(function ($bookings) {
                $service = $bookings->first()->service;
                return [
                    'name' => $service ? $service->trans('name') : null,
                    'name_en' => $service ? $service->name_en : null,
                    'count' => $bookings->count(),
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values()
            ->toArray();

        // Services by category
        $servicesByCategory = $serviceBookings->groupBy(function ($booking) {
            return $booking->service && $booking->service->subCategory && $booking->service->subCategory->category
                ? $booking->service->subCategory->category->trans('name')
                : 'غير محدد';
        })
            ->map(function ($bookings, $category) {
                return [
                    'category' => $category,
                    'count' => $bookings->count(),
                ];
            })
            ->values()
            ->toArray();

        $servicesStats = [
            'total_booked' => $totalBooked,
            'most_popular' => $mostPopularServices,
            'by_category' => $servicesByCategory,
        ];

        // Tickets Stats
        $tickets = \App\Models\Ticket::where('user_id', $user->id)->get();
        $ticketsStats = [
            'total' => $tickets->count(),
            'open' => $tickets->where('status', 'open')->count(),
            'in_progress' => $tickets->where('status', 'in_progress')->count(),
            'resolved' => $tickets->where('status', 'resolved')->count(),
        ];

        // Notifications Stats
        $notificationsStats = [
            'unread_count' => \App\Models\Notification::where('user_id', $user->id)
                ->where('read_at', null)
                ->count(),
        ];

        // Subscription
        $subscription = \App\Models\UserSubscription::where('user_id', $user->id)
            ->active()
            ->with('subscription')
            ->first();
        
        $subscriptionData = null;
        if ($subscription) {
            $subscriptionData = [
                'id' => $subscription->id,
                'subscription' => [
                    'id' => $subscription->subscription->id,
                    'name' => $subscription->subscription->trans('name'),
                    'description' => $subscription->subscription->trans('description'),
                    'price' => (string) $subscription->subscription->price,
                    'duration_type' => $subscription->subscription->duration_type,
                    'features' => $this->formatSubscriptionFeatures($subscription->subscription, $locale),
                ],
                'status' => $subscription->status,
                'started_at' => $subscription->started_at ? $subscription->started_at->toISOString() : null,
                'expires_at' => $subscription->expires_at ? $subscription->expires_at->toISOString() : null,
                'is_active' => $subscription->isActive(),
            ];
        }

        // Pending Subscription Request
        $pendingSubscriptionRequest = \App\Models\SubscriptionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('subscription')
            ->first();
        
        $pendingRequestData = null;
        if ($pendingSubscriptionRequest) {
            $pendingRequestData = [
                'id' => $pendingSubscriptionRequest->id,
                'subscription' => [
                    'id' => $pendingSubscriptionRequest->subscription->id,
                    'name' => $pendingSubscriptionRequest->subscription->trans('name'),
                ],
                'status' => $pendingSubscriptionRequest->status,
                    'created_at' => $pendingSubscriptionRequest->created_at ? $pendingSubscriptionRequest->created_at->toISOString() : null,
            ];
        }

        // Recent Bookings (last 5)
        $recentBookings = (clone $bookingsQuery)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_type' => $booking->booking_type,
                    'service' => $booking->service ? [
                        'id' => $booking->service->id,
                        'name' => $booking->service->trans('name'),
                        'sub_category' => $booking->service->subCategory ? [
                            'id' => $booking->service->subCategory->id,
                            'name' => $booking->service->subCategory->trans('name'),
                            'category' => $booking->service->subCategory->category ? [
                                'id' => $booking->service->subCategory->category->id,
                                'name' => $booking->service->subCategory->category->trans('name'),
                            ] : null,
                        ] : null,
                    ] : null,
                    'consultation' => $booking->consultation ? [
                        'id' => $booking->consultation->id,
                        'name' => $booking->consultation->trans('name'),
                    ] : null,
                    'employee' => $booking->employee && $booking->employee->user ? [
                        'id' => $booking->employee->id,
                        'user' => [
                            'id' => $booking->employee->user->id,
                            'name' => $booking->employee->user->name,
                        ],
                    ] : null,
                    'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'total_price' => (string) $booking->total_price,
                    'status' => $booking->status,
                    'actual_status' => $booking->actual_status,
                    'payment_status' => $booking->payment_status,
                    'created_at' => $booking->created_at ? $booking->created_at->toISOString() : null,
                ];
            });

        // Recent Tickets (last 5)
        $recentTickets = \App\Models\Ticket::where('user_id', $user->id)
            ->with(['assignedUser', 'latestMessage'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'subject' => $ticket->trans('subject'),
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'assigned_to' => $ticket->assignedUser ? [
                        'id' => $ticket->assignedUser->id,
                        'name' => $ticket->assignedUser->name,
                    ] : null,
                    'latest_message' => $ticket->latestMessage ? [
                        'id' => $ticket->latestMessage->id,
                        'message' => $ticket->latestMessage->message,
                        'created_at' => $ticket->latestMessage && $ticket->latestMessage->created_at ? $ticket->latestMessage->created_at->toISOString() : null,
                    ] : null,
                    'created_at' => $ticket->created_at ? $ticket->created_at->toISOString() : null,
                    'resolved_at' => $ticket->resolved_at ? $ticket->resolved_at->toISOString() : null,
                ];
            });

        // Recent Invoices (last 5 paid bookings)
        $recentInvoices = $paidBookings
            ->sortByDesc('created_at')
            ->take(5)
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'invoice_number' => 'INV-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                    'booking_id' => $booking->id,
                    'service' => $booking->booking_type === 'consultation' 
                        ? ($booking->consultation ? [
                            'id' => $booking->consultation->id,
                            'name' => $booking->consultation->trans('name'),
                            'name_en' => $booking->consultation->name_en,
                            'type' => 'consultation',
                        ] : null)
                        : ($booking->service ? [
                            'id' => $booking->service->id,
                            'name' => $booking->service->trans('name'),
                            'name_en' => $booking->service->name_en,
                            'type' => 'service',
                        ] : null),
                    'employee' => $booking->employee && $booking->employee->user ? [
                        'id' => $booking->employee->id,
                        'name' => $booking->employee->user->name,
                    ] : null,
                    'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'total_price' => (string) $booking->total_price,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'paid_at' => $booking->paid_at ? $booking->paid_at->toISOString() : null,
                    'payment_id' => $booking->payment_id,
                    'created_at' => $booking->created_at ? $booking->created_at->toISOString() : null,
                ];
            })
            ->values();

        // Recent Ratings (last 5)
        $recentRatings = \App\Models\Rating::where('customer_id', $user->id)
            ->with(['booking.service'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'booking_id' => $rating->booking_id,
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'created_at' => $rating->created_at ? $rating->created_at->toISOString() : null,
                    'booking' => $rating->booking ? [
                        'id' => $rating->booking->id,
                        'service' => $rating->booking->service ? [
                            'id' => $rating->booking->service->id,
                            'name' => $rating->booking->service->trans('name'),
                        ] : null,
                    ] : null,
                ];
            });

        // Recent Activity (last 10)
        $recentActivity = collect();
        
        // Add booking activities
        $recentBookings->each(function ($booking) use ($recentActivity) {
            $recentActivity->push([
                'id' => $booking['id'],
                'action' => 'booking_created',
                'description' => __('messages.booking_created'),
                'created_at' => $booking['created_at'],
            ]);
        });

        // Add payment activities
        $paidBookings->filter(function ($booking) {
            return $booking->paid_at && $booking->paid_at->isAfter(now()->subDays(30));
        })->each(function ($booking) use ($recentActivity) {
            $recentActivity->push([
                'id' => $booking->id,
                'action' => 'payment_completed',
                'description' => __('messages.payment_completed'),
                'created_at' => $booking->paid_at ? $booking->paid_at->toISOString() : null,
            ]);
        });

        $recentActivity = $recentActivity->sortByDesc('created_at')->take(10)->values();

        // Upcoming Bookings (next 24 hours)
        $upcomingBookings = (clone $bookingsQuery)
            ->where('booking_date', '>=', now()->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(10)
            ->get()
            ->filter(function ($booking) {
                if (!$booking->booking_date || !$booking->start_time) {
                    return false;
                }
                $bookingDateTime = \Carbon\Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time);
                return $bookingDateTime->isAfter(now()) && $bookingDateTime->isBefore(now()->addDay());
            })
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_type' => $booking->booking_type,
                    'service' => $booking->service ? [
                        'id' => $booking->service->id,
                        'name' => $booking->service->trans('name'),
                    ] : null,
                    'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'status' => $booking->status,
                ];
            })
            ->values();

        // Subscription Alerts
        $subscriptionAlerts = [
            'expires_soon' => false,
            'days_remaining' => null,
            'expires_at' => null,
        ];

        if ($subscription && $subscription->expires_at) {
            $daysRemaining = now()->diffInDays($subscription->expires_at, false);
            $subscriptionAlerts = [
                'expires_soon' => $daysRemaining <= 7 && $daysRemaining >= 0,
                'days_remaining' => $daysRemaining > 0 ? $daysRemaining : 0,
                'expires_at' => $subscription->expires_at->toISOString(),
            ];
        }

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
                'pending_subscription_request' => $pendingRequestData,
                'stats' => [
                    'bookings' => $bookingsStats,
                    'payments' => $paymentsStats,
                    'tickets' => $ticketsStats,
                    'notifications' => $notificationsStats,
                    'wallet' => $walletStats,
                    'ratings' => $ratingsStats,
                    'ready_apps' => $readyAppsStats,
                    'services' => $servicesStats,
                ],
                'recent_bookings' => $recentBookings,
                'recent_tickets' => $recentTickets,
                'recent_invoices' => $recentInvoices,
                'recent_ratings' => $recentRatings,
                'recent_activity' => $recentActivity,
                'upcoming_bookings' => $upcomingBookings,
                'subscription_alerts' => $subscriptionAlerts,
            ],
        ]);
    }

    /**
     * Format subscription features based on locale
     */
    private function formatSubscriptionFeatures($subscription, $locale)
    {
        // Get features based on locale
        if ($locale === 'en' && !empty($subscription->features_en)) {
            $features = $subscription->features_en;
        } else {
            $features = $subscription->features ?? [];
        }

        // If features is empty, try the other language as fallback
        if (empty($features)) {
            $features = $locale === 'en' 
                ? ($subscription->features ?? [])
                : ($subscription->features_en ?? []);
        }

        return collect($features)->map(function ($feature) {
            if (is_string($feature)) {
                return ['name' => $feature];
            }
            if (is_array($feature)) {
                return ['name' => $feature['name'] ?? $feature['name_en'] ?? ''];
            }
            return ['name' => ''];
        })->filter(function ($feature) {
            return !empty($feature['name']);
        })->values()->toArray();
    }
}
