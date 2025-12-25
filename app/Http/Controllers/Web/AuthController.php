<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Login user with email and password
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Check if user exists and is admin or staff
        $user = User::where('email', $request->email)
            ->whereIn('role', [User::ROLE_ADMIN, User::ROLE_STAFF])
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'البريد الإلكتروني غير مسجل أو ليس لديك صلاحية للوصول',
            ])->withInput($request->only('email'));
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'كلمة المرور غير صحيحة',
            ])->withInput($request->only('email'));
        }

        // Login user
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'مرحباً ' . $user->name);
        } elseif ($user->isStaff()) {
            return redirect()->intended(route('staff.dashboard'))
                ->with('success', 'مرحباً ' . $user->name);
        }

        return redirect()->intended('/');
    }

    /**
     * Logout user (for web)
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
