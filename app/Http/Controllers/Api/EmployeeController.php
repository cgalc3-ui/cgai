<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    /**
     * Get employee dashboard statistics
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        if (!$user->isEmployee() && !$user->isStaff()) {
            // Fallback if isEmployee not explicitly defined, check relationship
            if (!$user->employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'ليس لديك صلاحية للوصول',
                ], 403);
            }
        }

        $employee = $user->employee;

        // Statistics
        $todayBookings = $employee->bookings()
            ->where('booking_date', Carbon::today()->format('Y-m-d'))
            ->count();

        $upcomingBookings = $employee->bookings()
            ->where('booking_date', '>=', Carbon::today()->format('Y-m-d'))
            ->where('status', 'confirmed')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'today_bookings_count' => $todayBookings,
                'upcoming_bookings_count' => $upcomingBookings,
                'employee' => $employee->load('user'),
            ],
        ]);
    }

    /**
     * Get employee bookings
     */
    public function bookings(Request $request)
    {
        $user = $request->user();

        if (!$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'ملف الموظف غير موجود',
            ], 404);
        }

        $employee = $user->employee;

        $query = $employee->bookings()
            ->with(['customer', 'service', 'timeSlots']);

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date if provided
        if ($request->has('date')) {
            $query->where('booking_date', $request->date);
        }

        // Default: Order by date descending (newest first)
        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(15);

        // Add computed attributes for each booking
        $bookings->getCollection()->transform(function ($booking) {
            $booking->actual_status = $booking->actual_status;
            $booking->time_display = $booking->time_display;
            $booking->time_until_start = $booking->time_until_start;
            $booking->elapsed_time = $booking->elapsed_time;
            $booking->time_until_end = $booking->time_until_end;
            return $booking;
        });

        return response()->json([
            'success' => true,
            'data' => $bookings,
        ]);
    }
}
