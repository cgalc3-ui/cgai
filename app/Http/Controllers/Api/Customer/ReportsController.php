<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\Controller;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\SubscriptionRequest;
use App\Models\UserSubscription;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Traits\ApiResponseTrait;

class ReportsController extends Controller
{
    use ApiResponseTrait;
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Get comprehensive reports and statistics for customer
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول',
            ], 401);
        }

        if (!$user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        // Date filters (optional)
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;

        // Customer Bookings Statistics
        $bookingsQuery = Booking::where('customer_id', $user->id);
        if ($startDate && $endDate) {
            $bookingsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $bookingsStats = [
            'total' => (clone $bookingsQuery)->count(),
            'pending' => (clone $bookingsQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $bookingsQuery)->where('status', 'confirmed')->count(),
            'in_progress' => (clone $bookingsQuery)->where('status', 'in_progress')->count(),
            'completed' => (clone $bookingsQuery)->where('status', 'completed')->count(),
            'cancelled' => (clone $bookingsQuery)->where('status', 'cancelled')->count(),
            'today' => Booking::where('customer_id', $user->id)
                ->whereDate('booking_date', Carbon::today())
                ->count(),
            'this_week' => Booking::where('customer_id', $user->id)
                ->whereBetween('booking_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count(),
            'this_month' => Booking::where('customer_id', $user->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'this_year' => Booking::where('customer_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'upcoming' => Booking::where('customer_id', $user->id)
                ->where('booking_date', '>=', Carbon::today())
                ->where('status', '!=', 'cancelled')
                ->count(),
        ];

        // Payment Statistics
        $paymentQuery = Booking::where('customer_id', $user->id);
        if ($startDate && $endDate) {
            $paymentQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $paymentStats = [
            'total_spent' => (float) (clone $paymentQuery)->where('payment_status', 'paid')->sum('total_price'),
            'paid_bookings' => (clone $paymentQuery)->where('payment_status', 'paid')->count(),
            'unpaid_bookings' => (clone $paymentQuery)->where('payment_status', 'unpaid')->count(),
            'pending_payment' => (float) (clone $paymentQuery)->where('payment_status', 'unpaid')->sum('total_price'),
            'this_month_spent' => (float) Booking::where('customer_id', $user->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('payment_status', 'paid')
                ->sum('total_price'),
            'this_year_spent' => (float) Booking::where('customer_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('payment_status', 'paid')
                ->sum('total_price'),
        ];

        // Tickets Statistics
        $ticketsQuery = Ticket::where('user_id', $user->id);
        if ($startDate && $endDate) {
            $ticketsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $ticketsStats = [
            'total' => (clone $ticketsQuery)->count(),
            'open' => (clone $ticketsQuery)->where('status', 'open')->count(),
            'in_progress' => (clone $ticketsQuery)->where('status', 'in_progress')->count(),
            'resolved' => (clone $ticketsQuery)->where('status', 'resolved')->count(),
            'closed' => (clone $ticketsQuery)->where('status', 'closed')->count(),
        ];

        // Subscription Statistics
        $activeSubscription = $user->getActiveSubscription();
        $subscriptionStats = null;
        if ($activeSubscription) {
            $subscriptionStats = [
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

        // Pending Subscription Request
        $pendingRequest = SubscriptionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with(['subscription' => function($query) {
                $query->select('id', 'name', 'name_en', 'price', 'duration_type');
            }])
            ->first();

        // Monthly Bookings Chart (Last 12 months)
        $monthlyBookings = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Booking::where('customer_id', $user->id)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
            
            $monthlyBookings[] = [
                'month' => $month->format('Y-m'),
                'month_name' => $month->translatedFormat('F Y'),
                'count' => $count,
            ];
        }

        // Monthly Spending Chart (Last 12 months)
        $monthlySpending = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $spent = Booking::where('customer_id', $user->id)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->where('payment_status', 'paid')
                ->sum('total_price');
            
            $monthlySpending[] = [
                'month' => $month->format('Y-m'),
                'month_name' => $month->translatedFormat('F Y'),
                'amount' => (float) $spent,
            ];
        }

        // Bookings by Status (for charts)
        $bookingsByStatus = [
            'pending' => $bookingsStats['pending'],
            'confirmed' => $bookingsStats['confirmed'],
            'in_progress' => $bookingsStats['in_progress'],
            'completed' => $bookingsStats['completed'],
            'cancelled' => $bookingsStats['cancelled'],
        ];

        // Most Used Services (Top 5)
        $mostUsedServicesQuery = DB::table('bookings')
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->where('bookings.customer_id', $user->id)
            ->whereNotNull('bookings.service_id');
        
        if (Schema::hasColumn('bookings', 'booking_type')) {
            $mostUsedServicesQuery->where('bookings.booking_type', 'service');
        }
        
        $mostUsedServices = $mostUsedServicesQuery
            ->select('services.id', 'services.name', DB::raw('count(*) as bookings_count'))
            ->groupBy('services.id', 'services.name')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'name_en' => $item->name_en ?? null,
                    'bookings_count' => $item->bookings_count,
                ];
            });

        // Most Used Consultations (Top 5)
        $mostUsedConsultations = collect([]);
        if (Schema::hasTable('consultations') && Schema::hasColumn('bookings', 'consultation_id')) {
            $mostUsedConsultationsQuery = DB::table('bookings')
                ->join('consultations', 'bookings.consultation_id', '=', 'consultations.id')
                ->where('bookings.customer_id', $user->id)
                ->whereNotNull('bookings.consultation_id');
            
            if (Schema::hasColumn('bookings', 'booking_type')) {
                $mostUsedConsultationsQuery->where('bookings.booking_type', 'consultation');
            }
            
            $mostUsedConsultations = $mostUsedConsultationsQuery
                ->select('consultations.id', 'consultations.name', DB::raw('count(*) as bookings_count'))
                ->groupBy('consultations.id', 'consultations.name')
                ->orderBy('bookings_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'bookings_count' => $item->bookings_count,
                    ];
                });
        }

        // Recent Bookings (last 10)
        $recentBookings = Booking::where('customer_id', $user->id)
            ->with([
                'service.subCategory.category',
                'consultation.category',
                'employee.user'
            ])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'service' => $booking->service ? [
                        'id' => $booking->service->id,
                        'name' => $booking->service->name,
                        'name_en' => $booking->service->name_en,
                        'sub_category' => $booking->service->subCategory ? [
                            'id' => $booking->service->subCategory->id,
                            'name' => $booking->service->subCategory->name,
                            'name_en' => $booking->service->subCategory->name_en,
                            'category' => $booking->service->subCategory->category ? [
                                'id' => $booking->service->subCategory->category->id,
                                'name' => $booking->service->subCategory->category->name,
                                'name_en' => $booking->service->subCategory->category->name_en,
                            ] : null,
                        ] : null,
                    ] : null,
                    'consultation' => $booking->consultation ? [
                        'id' => $booking->consultation->id,
                        'name' => $booking->consultation->name,
                        'name_en' => $booking->consultation->name_en,
                        'category' => $booking->consultation->category ? [
                            'id' => $booking->consultation->category->id,
                            'name' => $booking->consultation->category->name,
                            'name_en' => $booking->consultation->category->name_en,
                        ] : null,
                    ] : null,
                    'employee' => $booking->employee ? [
                        'id' => $booking->employee->id,
                        'user' => $booking->employee->user ? [
                            'id' => $booking->employee->user->id,
                            'name' => $booking->employee->user->name,
                        ] : null,
                    ] : null,
                    'booking_type' => Schema::hasColumn('bookings', 'booking_type') 
                        ? $booking->booking_type 
                        : ($booking->service_id ? 'service' : ($booking->consultation_id ? 'consultation' : 'service')),
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

        // Upcoming Bookings (next 7 days)
        $upcomingBookings = Booking::where('customer_id', $user->id)
            ->where('booking_date', '>=', Carbon::today())
            ->where('booking_date', '<=', Carbon::today()->addDays(7))
            ->where('status', '!=', 'cancelled')
            ->with(['service', 'consultation', 'employee.user'])
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'service' => $booking->service ? [
                        'id' => $booking->service->id,
                        'name' => $booking->service->name,
                        'name_en' => $booking->service->name_en,
                    ] : null,
                    'consultation' => $booking->consultation ? [
                        'id' => $booking->consultation->id,
                        'name' => $booking->consultation->name,
                        'name_en' => $booking->consultation->name_en,
                    ] : null,
                    'employee' => $booking->employee ? [
                        'id' => $booking->employee->id,
                        'user' => $booking->employee->user ? [
                            'id' => $booking->employee->user->id,
                            'name' => $booking->employee->user->name,
                        ] : null,
                    ] : null,
                    'booking_type' => Schema::hasColumn('bookings', 'booking_type') 
                        ? $booking->booking_type 
                        : ($booking->service_id ? 'service' : ($booking->consultation_id ? 'consultation' : 'service')),
                    'booking_date' => $booking->booking_date,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'status' => $booking->status,
                    'actual_status' => $booking->actual_status,
                ];
            });

        // Filter locale columns for all nested data
        $filteredRecentBookings = $recentBookings->map(function ($booking) {
            return $this->filterLocaleColumns($booking);
        });

        $filteredUpcomingBookings = $upcomingBookings->map(function ($booking) {
            return $this->filterLocaleColumns($booking);
        });

        $filteredMostUsedServices = $mostUsedServices->map(function ($service) {
            return $this->filterLocaleColumns($service);
        });

        $filteredMostUsedConsultations = $mostUsedConsultations->map(function ($consultation) {
            return $this->filterLocaleColumns($consultation);
        });

        $filteredPendingRequest = $pendingRequest ? $this->filterLocaleColumns($pendingRequest) : null;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'bookings' => $bookingsStats,
                'payments' => $paymentStats,
                'tickets' => $ticketsStats,
                'subscription' => $subscriptionStats,
                'pending_subscription_request' => $filteredPendingRequest,
                'charts' => [
                    'monthly_bookings' => $monthlyBookings,
                    'monthly_spending' => $monthlySpending,
                    'bookings_by_status' => $bookingsByStatus,
                ],
                'most_used_services' => $filteredMostUsedServices,
                'most_used_consultations' => $filteredMostUsedConsultations,
                'recent_bookings' => $filteredRecentBookings,
                'upcoming_bookings' => $filteredUpcomingBookings,
            ],
        ]);
    }

    /**
     * Get activity log for customer
     */
    public function activityLog(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بالوصول',
            ], 401);
        }

        if (!$user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        $perPage = (int) $request->get('per_page', 20);
        $type = $request->get('type'); // bookings, tickets, subscriptions, payments, all
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;

        $activities = collect();

        // Bookings Activities
        if (!$type || $type === 'all' || $type === 'bookings') {
            $bookingsQuery = Booking::where('customer_id', $user->id)
                ->with([
                    'service.subCategory.category',
                    'consultation.category',
                    'employee.user'
                ])
                ->orderBy('created_at', 'desc');

            if ($startDate && $endDate) {
                $bookingsQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $bookings = $bookingsQuery->get();

            foreach ($bookings as $booking) {
                $activities->push([
                    'id' => 'booking_' . $booking->id,
                    'type' => 'booking',
                    'type_label' => __('messages.booking'),
                    'title' => $booking->service 
                        ? $booking->service->name 
                        : ($booking->consultation ? $booking->consultation->name : __('messages.booking')),
                    'description' => $this->getBookingDescription($booking),
                    'status' => $booking->status,
                    'status_label' => $this->getBookingStatusLabel($booking->status),
                    'payment_status' => $booking->payment_status,
                    'payment_status_label' => $booking->payment_status === 'paid' ? __('messages.paid') : __('messages.unpaid'),
                    'amount' => $booking->total_price,
                    'date' => $booking->booking_date,
                    'time' => $booking->start_time,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                    'data' => [
                        'booking_id' => $booking->id,
                        'service' => $booking->service ? [
                            'id' => $booking->service->id,
                            'name' => $booking->service->name,
                        ] : null,
                        'consultation' => $booking->consultation ? [
                            'id' => $booking->consultation->id,
                            'name' => $booking->consultation->name,
                        ] : null,
                        'employee' => $booking->employee ? [
                            'id' => $booking->employee->id,
                            'name' => $booking->employee->user ? $booking->employee->user->name : null,
                        ] : null,
                        'booking_type' => Schema::hasColumn('bookings', 'booking_type') 
                            ? $booking->booking_type 
                            : ($booking->service_id ? 'service' : ($booking->consultation_id ? 'consultation' : 'service')),
                    ],
                ]);
            }
        }

        // Tickets Activities
        if (!$type || $type === 'all' || $type === 'tickets') {
            $ticketsQuery = Ticket::where('user_id', $user->id)
                ->with(['messages' => function($query) {
                    $query->latest()->limit(1);
                }])
                ->orderBy('created_at', 'desc');

            if ($startDate && $endDate) {
                $ticketsQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $tickets = $ticketsQuery->get();

            foreach ($tickets as $ticket) {
                $lastMessage = $ticket->messages->first();
                $activities->push([
                    'id' => 'ticket_' . $ticket->id,
                    'type' => 'ticket',
                    'type_label' => __('messages.ticket'),
                    'title' => $ticket->subject,
                    'description' => $this->getTicketDescription($ticket, $lastMessage),
                    'status' => $ticket->status,
                    'status_label' => $this->getTicketStatusLabel($ticket->status),
                    'priority' => $ticket->priority,
                    'amount' => null,
                    'date' => null,
                    'time' => null,
                    'created_at' => $ticket->created_at,
                    'updated_at' => $ticket->updated_at,
                    'data' => [
                        'ticket_id' => $ticket->id,
                        'subject' => $ticket->subject,
                        'description' => $ticket->description,
                        'last_message' => $lastMessage ? [
                            'id' => $lastMessage->id,
                            'message' => $lastMessage->message,
                            'created_at' => $lastMessage->created_at,
                        ] : null,
                    ],
                ]);
            }
        }

        // Subscription Requests Activities
        if (!$type || $type === 'all' || $type === 'subscriptions') {
            $subscriptionRequestsQuery = SubscriptionRequest::where('user_id', $user->id)
                ->orderBy('created_at', 'desc');

            if ($startDate && $endDate) {
                $subscriptionRequestsQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $subscriptionRequests = $subscriptionRequestsQuery
                ->with(['subscription' => function($query) {
                    $query->select('id', 'name', 'name_en', 'price', 'duration_type');
                }])
                ->get();

            foreach ($subscriptionRequests as $subscriptionRequest) {
                $activities->push([
                    'id' => 'subscription_request_' . $subscriptionRequest->id,
                    'type' => 'subscription_request',
                    'type_label' => __('messages.subscription_request'),
                    'title' => $subscriptionRequest->subscription->name ?? __('messages.subscription_request'),
                    'description' => $this->getSubscriptionRequestDescription($subscriptionRequest),
                    'status' => $subscriptionRequest->status,
                    'status_label' => $this->getSubscriptionRequestStatusLabel($subscriptionRequest->status),
                    'amount' => $subscriptionRequest->subscription->price ?? null,
                    'date' => null,
                    'time' => null,
                    'created_at' => $subscriptionRequest->created_at,
                    'updated_at' => $subscriptionRequest->updated_at,
                    'data' => [
                        'request_id' => $subscriptionRequest->id,
                        'subscription' => [
                            'id' => $subscriptionRequest->subscription->id ?? null,
                            'name' => $subscriptionRequest->subscription->name ?? null,
                        ],
                    ],
                ]);
            }
        }

        // User Subscriptions Activities
        if (!$type || $type === 'all' || $type === 'subscriptions') {
            $userSubscriptionsQuery = UserSubscription::where('user_id', $user->id)
                ->select([
                    'user_subscriptions.id',
                    'user_subscriptions.user_id',
                    'user_subscriptions.subscription_id',
                    'user_subscriptions.subscription_request_id',
                    'user_subscriptions.status',
                    'user_subscriptions.started_at',
                    'user_subscriptions.expires_at',
                    'user_subscriptions.created_at',
                    'user_subscriptions.updated_at'
                ])
                ->with(['subscription' => function($query) {
                    $query->select('id', 'name', 'name_en', 'price', 'duration_type');
                }])
                ->orderBy('created_at', 'desc');

            if ($startDate && $endDate) {
                $userSubscriptionsQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $userSubscriptions = $userSubscriptionsQuery->get();

            foreach ($userSubscriptions as $subscription) {
                $activities->push([
                    'id' => 'user_subscription_' . $subscription->id,
                    'type' => 'subscription',
                    'type_label' => __('messages.subscription'),
                    'title' => $subscription->subscription->name ?? __('messages.subscription'),
                    'description' => $this->getUserSubscriptionDescription($subscription),
                    'status' => $subscription->status,
                    'status_label' => $this->getUserSubscriptionStatusLabel($subscription->status),
                    'amount' => $subscription->subscription->price ?? null,
                    'date' => $subscription->started_at ? $subscription->started_at->format('Y-m-d') : null,
                    'time' => null,
                    'created_at' => $subscription->created_at,
                    'updated_at' => $subscription->updated_at,
                    'data' => [
                        'subscription_id' => $subscription->id,
                        'subscription' => [
                            'id' => $subscription->subscription->id ?? null,
                            'name' => $subscription->subscription->name ?? null,
                        ],
                        'started_at' => $subscription->started_at,
                        'expires_at' => $subscription->expires_at,
                    ],
                ]);
            }
        }

        // Payment Activities (from paid bookings)
        if (!$type || $type === 'all' || $type === 'payments') {
            $paymentsQuery = Booking::where('customer_id', $user->id)
                ->where('payment_status', 'paid')
                ->whereNotNull('paid_at')
                ->with(['service', 'consultation'])
                ->orderBy('paid_at', 'desc');

            if ($startDate && $endDate) {
                $paymentsQuery->whereBetween('paid_at', [$startDate, $endDate]);
            }

            $payments = $paymentsQuery->get();

            foreach ($payments as $payment) {
                $activities->push([
                    'id' => 'payment_' . $payment->id,
                    'type' => 'payment',
                    'type_label' => __('messages.payment'),
                    'title' => __('messages.payment_booking') . ': ' . ($payment->service 
                        ? $payment->service->name 
                        : ($payment->consultation ? $payment->consultation->name : __('messages.booking'))),
                    'description' => __('messages.payment_amount_paid') . ' ' . number_format($payment->total_price, 2) . ' ' . __('messages.sar'),
                    'status' => 'paid',
                    'status_label' => __('messages.paid'),
                    'payment_status' => 'paid',
                    'payment_status_label' => __('messages.paid'),
                    'amount' => $payment->total_price,
                    'date' => $payment->paid_at ? $payment->paid_at->format('Y-m-d') : null,
                    'time' => $payment->paid_at ? $payment->paid_at->format('H:i') : null,
                    'created_at' => $payment->paid_at ?? $payment->created_at,
                    'updated_at' => $payment->updated_at,
                    'data' => [
                        'booking_id' => $payment->id,
                        'service' => $payment->service ? [
                            'id' => $payment->service->id,
                            'name' => $payment->service->name,
                        ] : null,
                        'consultation' => $payment->consultation ? [
                            'id' => $payment->consultation->id,
                            'name' => $payment->consultation->name,
                        ] : null,
                    ],
                ]);
            }
        }

        // Sort all activities by created_at (newest first)
        $activities = $activities->sortByDesc('created_at')->values();

        // Paginate
        $pageParam = $request->input('page', 1);
        $currentPage = is_numeric($pageParam) ? (int) $pageParam : 1;
        
        // Ensure perPage is an integer (it's defined at line 392)
        $perPageValue = is_int($perPage) ? $perPage : (is_numeric($perPage) ? (int) $perPage : 20);
        
        $items = $activities->slice(($currentPage - 1) * $perPageValue, $perPageValue)->values();
        $total = $activities->count();
        $lastPage = (int) ceil($total / $perPageValue);

        return response()->json([
            'success' => true,
            'data' => [
                'activities' => $items,
                'pagination' => [
                    'current_page' => (int) $currentPage,
                    'per_page' => (int) $perPageValue,
                    'total' => $total,
                    'last_page' => $lastPage,
                    'from' => $total > 0 ? (($currentPage - 1) * $perPageValue) + 1 : 0,
                    'to' => min($currentPage * $perPageValue, $total),
                ],
                'filters' => [
                    'type' => $type ?? 'all',
                    'start_date' => $startDate ? $startDate->format('Y-m-d') : null,
                    'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
                ],
            ],
        ]);
    }

    /**
     * Get booking description for activity log
     */
    private function getBookingDescription($booking)
    {
        $serviceName = $booking->service 
            ? $booking->service->name 
            : ($booking->consultation ? $booking->consultation->name : __('messages.booking'));
        
        $statusLabel = $this->getBookingStatusLabel($booking->status);
        
        return __('messages.booking') . " {$serviceName} - " . __('messages.status') . ": {$statusLabel}";
    }

    /**
     * Get booking status label
     */
    private function getBookingStatusLabel($status)
    {
        $labels = [
            'pending' => __('messages.pending'),
            'confirmed' => __('messages.confirmed'),
            'in_progress' => __('messages.in_progress'),
            'completed' => __('messages.completed'),
            'cancelled' => __('messages.cancelled'),
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get ticket description for activity log
     */
    private function getTicketDescription($ticket, $lastMessage)
    {
        $statusLabel = $this->getTicketStatusLabel($ticket->status);
        
        if ($lastMessage) {
            return __('messages.ticket') . ": {$ticket->subject} - " . __('messages.status') . ": {$statusLabel} - " . __('messages.last_message') . ": " . substr($lastMessage->message, 0, 50);
        }
        
        return __('messages.ticket') . ": {$ticket->subject} - " . __('messages.status') . ": {$statusLabel}";
    }

    /**
     * Get ticket status label
     */
    private function getTicketStatusLabel($status)
    {
        $labels = [
            'open' => __('messages.open'),
            'in_progress' => __('messages.in_progress'),
            'resolved' => __('messages.resolved'),
            'closed' => __('messages.closed'),
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get subscription request description
     */
    private function getSubscriptionRequestDescription($request)
    {
        $statusLabel = $this->getSubscriptionRequestStatusLabel($request->status);
        
        return __('messages.subscription_request_package') . ": {$request->subscription->name} - " . __('messages.status') . ": {$statusLabel}";
    }

    /**
     * Get subscription request status label
     */
    private function getSubscriptionRequestStatusLabel($status)
    {
        $labels = [
            'pending' => __('messages.pending'),
            'approved' => __('messages.approved'),
            'rejected' => __('messages.rejected'),
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get user subscription description
     */
    private function getUserSubscriptionDescription($subscription)
    {
        $statusLabel = $this->getUserSubscriptionStatusLabel($subscription->status);
        
        $expiresText = $subscription->expires_at 
            ? ' - ' . __('messages.expires_at') . ': ' . $subscription->expires_at->format('Y-m-d')
            : '';
        
        return __('messages.subscription_package') . ": {$subscription->subscription->name} - " . __('messages.status') . ": {$statusLabel}{$expiresText}";
    }

    /**
     * Get user subscription status label
     */
    private function getUserSubscriptionStatusLabel($status)
    {
        $labels = [
            'active' => __('messages.active'),
            'expired' => __('messages.expired'),
            'cancelled' => __('messages.cancelled'),
        ];

        return $labels[$status] ?? $status;
    }
}

