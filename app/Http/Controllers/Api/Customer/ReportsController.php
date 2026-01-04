<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Api\Controller;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\SubscriptionRequest;
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
}

