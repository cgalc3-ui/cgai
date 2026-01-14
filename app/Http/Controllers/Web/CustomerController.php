<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display customer dashboard
     */
    public function dashboard()
    {
        $customer = auth()->user();
        
        // Get customer statistics
        $totalBookings = Booking::where('customer_id', $customer->id)->count();
        $pendingBookings = Booking::where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->count();
        $completedBookings = Booking::where('customer_id', $customer->id)
            ->where('status', 'completed')
            ->count();
        
        // Get recent bookings
        $recentBookings = Booking::where('customer_id', $customer->id)
            ->with(['service', 'consultation', 'employee.user'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('customer.dashboard', compact(
            'totalBookings',
            'pendingBookings',
            'completedBookings',
            'recentBookings'
        ));
    }

    /**
     * Display customer bookings
     */
    public function bookings()
    {
        $customer = auth()->user();
        
        $bookings = Booking::where('customer_id', $customer->id)
            ->with(['service', 'consultation', 'employee.user'])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10);
        
        return view('customer.bookings', compact('bookings'));
    }

    /**
     * Show booking details
     */
    public function showBooking(Booking $booking)
    {
        $customer = auth()->user();
        
        // Check if booking belongs to customer
        if ($booking->customer_id !== $customer->id) {
            abort(403, 'Unauthorized');
        }
        
        $booking->load(['service', 'consultation', 'employee.user', 'timeSlots', 'rating']);
        
        return view('customer.booking-show', compact('booking'));
    }
}
