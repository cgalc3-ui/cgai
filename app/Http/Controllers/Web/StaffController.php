<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:staff,admin');
    }

    /**
     * Show staff dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
        ];

        return view('staff.dashboard', compact('stats'));
    }

    /**
     * Show all customers
     */
    public function customers()
    {
        $customers = User::where('role', 'customer')->latest()->paginate(20);
        return view('staff.customers.index', compact('customers'));
    }

    /**
     * Show customer details
     */
    public function showCustomer(User $customer)
    {
        if (!$customer->isCustomer()) {
            return redirect()->route('staff.customers')
                ->with('error', 'المستخدم المطلوب ليس عميلاً');
        }

        return view('staff.customers.show', compact('customer'));
    }
}
