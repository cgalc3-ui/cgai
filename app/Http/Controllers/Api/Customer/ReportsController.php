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

class ReportsController extends Controller
{
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
            ->with('subscription')
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
                    ] : null,
                    'consultation' => $booking->consultation ? [
                        'id' => $booking->consultation->id,
                        'name' => $booking->consultation->name,
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
                'pending_subscription_request' => $pendingRequest ? [
                    'id' => $pendingRequest->id,
                    'subscription' => [
                        'id' => $pendingRequest->subscription->id,
                        'name' => $pendingRequest->subscription->name,
                    ],
                    'status' => $pendingRequest->status,
                    'created_at' => $pendingRequest->created_at,
                ] : null,
                'charts' => [
                    'monthly_bookings' => $monthlyBookings,
                    'monthly_spending' => $monthlySpending,
                    'bookings_by_status' => $bookingsByStatus,
                ],
                'most_used_services' => $mostUsedServices,
                'most_used_consultations' => $mostUsedConsultations,
                'recent_bookings' => $recentBookings,
                'upcoming_bookings' => $upcomingBookings,
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

        $perPage = $request->get('per_page', 20);
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
                    'type_label' => 'حجز',
                    'title' => $booking->service 
                        ? $booking->service->name 
                        : ($booking->consultation ? $booking->consultation->name : 'حجز'),
                    'description' => $this->getBookingDescription($booking),
                    'status' => $booking->status,
                    'status_label' => $this->getBookingStatusLabel($booking->status),
                    'payment_status' => $booking->payment_status,
                    'payment_status_label' => $booking->payment_status === 'paid' ? 'مدفوع' : 'غير مدفوع',
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
                    'type_label' => 'تذكرة',
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
                ->with('subscription')
                ->orderBy('created_at', 'desc');

            if ($startDate && $endDate) {
                $subscriptionRequestsQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $subscriptionRequests = $subscriptionRequestsQuery->get();

            foreach ($subscriptionRequests as $request) {
                $activities->push([
                    'id' => 'subscription_request_' . $request->id,
                    'type' => 'subscription_request',
                    'type_label' => 'طلب اشتراك',
                    'title' => $request->subscription->name ?? 'طلب اشتراك',
                    'description' => $this->getSubscriptionRequestDescription($request),
                    'status' => $request->status,
                    'status_label' => $this->getSubscriptionRequestStatusLabel($request->status),
                    'amount' => $request->subscription->price ?? null,
                    'date' => null,
                    'time' => null,
                    'created_at' => $request->created_at,
                    'updated_at' => $request->updated_at,
                    'data' => [
                        'request_id' => $request->id,
                        'subscription' => [
                            'id' => $request->subscription->id ?? null,
                            'name' => $request->subscription->name ?? null,
                        ],
                    ],
                ]);
            }
        }

        // User Subscriptions Activities
        if (!$type || $type === 'all' || $type === 'subscriptions') {
            $userSubscriptionsQuery = UserSubscription::where('user_id', $user->id)
                ->with('subscription')
                ->orderBy('created_at', 'desc');

            if ($startDate && $endDate) {
                $userSubscriptionsQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $userSubscriptions = $userSubscriptionsQuery->get();

            foreach ($userSubscriptions as $subscription) {
                $activities->push([
                    'id' => 'user_subscription_' . $subscription->id,
                    'type' => 'subscription',
                    'type_label' => 'اشتراك',
                    'title' => $subscription->subscription->name ?? 'اشتراك',
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
                    'type_label' => 'دفع',
                    'title' => 'دفع حجز: ' . ($payment->service 
                        ? $payment->service->name 
                        : ($payment->consultation ? $payment->consultation->name : 'حجز')),
                    'description' => 'تم دفع مبلغ ' . number_format($payment->total_price, 2) . ' ريال',
                    'status' => 'paid',
                    'status_label' => 'مدفوع',
                    'payment_status' => 'paid',
                    'payment_status_label' => 'مدفوع',
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
        $currentPage = $request->get('page', 1);
        $items = $activities->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $total = $activities->count();
        $lastPage = ceil($total / $perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'activities' => $items,
                'pagination' => [
                    'current_page' => (int) $currentPage,
                    'per_page' => (int) $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                    'from' => $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0,
                    'to' => min($currentPage * $perPage, $total),
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
            : ($booking->consultation ? $booking->consultation->name : 'حجز');
        
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        $statusLabel = $statusLabels[$booking->status] ?? $booking->status;
        
        return "حجز {$serviceName} - الحالة: {$statusLabel}";
    }

    /**
     * Get booking status label
     */
    private function getBookingStatusLabel($status)
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'confirmed' => 'مؤكد',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get ticket description for activity log
     */
    private function getTicketDescription($ticket, $lastMessage)
    {
        $statusLabels = [
            'open' => 'مفتوح',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'تم الحل',
            'closed' => 'مغلق',
        ];

        $statusLabel = $statusLabels[$ticket->status] ?? $ticket->status;
        
        if ($lastMessage) {
            return "تذكرة: {$ticket->subject} - الحالة: {$statusLabel} - آخر رسالة: " . substr($lastMessage->message, 0, 50);
        }
        
        return "تذكرة: {$ticket->subject} - الحالة: {$statusLabel}";
    }

    /**
     * Get ticket status label
     */
    private function getTicketStatusLabel($status)
    {
        $labels = [
            'open' => 'مفتوح',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'تم الحل',
            'closed' => 'مغلق',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get subscription request description
     */
    private function getSubscriptionRequestDescription($request)
    {
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
        ];

        $statusLabel = $statusLabels[$request->status] ?? $request->status;
        
        return "طلب اشتراك في باقة: {$request->subscription->name} - الحالة: {$statusLabel}";
    }

    /**
     * Get subscription request status label
     */
    private function getSubscriptionRequestStatusLabel($status)
    {
        $labels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get user subscription description
     */
    private function getUserSubscriptionDescription($subscription)
    {
        $statusLabels = [
            'active' => 'نشط',
            'expired' => 'منتهي',
            'cancelled' => 'ملغي',
        ];

        $statusLabel = $statusLabels[$subscription->status] ?? $subscription->status;
        
        $expiresText = $subscription->expires_at 
            ? ' - ينتهي في: ' . $subscription->expires_at->format('Y-m-d')
            : '';
        
        return "اشتراك في باقة: {$subscription->subscription->name} - الحالة: {$statusLabel}{$expiresText}";
    }

    /**
     * Get user subscription status label
     */
    private function getUserSubscriptionStatusLabel($status)
    {
        $labels = [
            'active' => 'نشط',
            'expired' => 'منتهي',
            'cancelled' => 'ملغي',
        ];

        return $labels[$status] ?? $status;
    }
}

