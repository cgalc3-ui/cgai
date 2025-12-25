<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\VerificationCode;
use App\Services\Sms\FourJawalySmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $smsService;

    public function __construct(FourJawalySmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send verification code to phone number
     */
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'type' => 'nullable|string|in:registration,login,password_reset',
        ]);

        $phone = $request->phone;
        $type = $request->type ?? 'registration';

        // Check if user exists for login type
        if ($type === 'login') {
            $user = User::where('phone', $phone)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم الهاتف غير مسجل',
                ], 404);
            }
        }

        // Check if user already exists for registration type
        if ($type === 'registration') {
            $user = User::where('phone', $phone)->first();
            if ($user) {
                return response()->json([
                    'success' => false,
                    'message' => 'رقم الهاتف مسجل بالفعل',
                ], 422);
            }
        }

        // Generate verification code
        $verificationCode = VerificationCode::generate($phone, $type);

        // Send SMS
        $codeLength = config('sms.verification.code_length', 6);
        $message = "كود التحقق الخاص بك هو: {$verificationCode->code}";
        
        $result = $this->smsService->sendSMS($phone, $message, [
            'event_type' => 'verification_code',
            'entity_type' => 'verification_code',
            'entity_id' => $verificationCode->id,
        ]);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'تم إرسال كود التحقق بنجاح',
                'expires_in' => config('sms.verification.expires_in_minutes', 10),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'فشل إرسال كود التحقق: ' . $result['message'],
        ], 500);
    }

    /**
     * Verify code for registration (Step 1)
     */
    public function verifyRegistrationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        // Verify code
        $verified = VerificationCode::verify($request->phone, $request->code, 'registration');
        
        if (!$verified) {
            return response()->json([
                'success' => false,
                'message' => 'كود التحقق غير صحيح أو منتهي الصلاحية',
            ], 422);
        }

        // Check if user already exists
        $existingUser = User::where('phone', $request->phone)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الهاتف مسجل بالفعل',
            ], 422);
        }

        // Generate temporary token for completing registration
        $tempToken = bin2hex(random_bytes(32));
        
        // Store verification data temporarily (you can use cache or database)
        \Illuminate\Support\Facades\Cache::put(
            'registration_temp_' . $tempToken,
            [
                'phone' => $request->phone,
                'verified_at' => now(),
            ],
            now()->addMinutes(30) // Expires in 30 minutes
        );

        return response()->json([
            'success' => true,
            'message' => 'تم التحقق من الكود بنجاح',
            'temp_token' => $tempToken,
            'next_step' => 'complete_registration',
        ]);
    }

    /**
     * Complete registration with user data (Step 2)
     */
    public function completeRegistration(Request $request)
    {
        $request->validate([
            'temp_token' => 'required|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
        ]);

        // Get verification data from cache
        $verificationData = \Illuminate\Support\Facades\Cache::get('registration_temp_' . $request->temp_token);
        
        if (!$verificationData) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق المؤقت غير صحيح أو منتهي الصلاحية',
            ], 422);
        }

        $phone = $verificationData['phone'];

        // Check if user already exists (double check)
        $existingUser = User::where('phone', $phone)->first();
        if ($existingUser) {
            // Clear temp token
            \Illuminate\Support\Facades\Cache::forget('registration_temp_' . $request->temp_token);
            
            return response()->json([
                'success' => false,
                'message' => 'رقم الهاتف مسجل بالفعل',
            ], 422);
        }

        // Generate name if not provided
        $name = $request->name ?? 'مستخدم ' . substr($phone, -4);
        
        // Generate email if not provided
        $email = $request->email ?? $phone . '@customer.local';

        // Check email uniqueness if provided
        if ($request->filled('email')) {
            $emailExists = User::where('email', $request->email)->exists();
            if ($emailExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'البريد الإلكتروني مسجل بالفعل',
                ], 422);
            }
        }

        // Create user (always customer for API registration)
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'phone_verified_at' => $verificationData['verified_at'],
            'role' => User::ROLE_CUSTOMER, // API registration is always customer
            'password' => Hash::make(uniqid()), // Random password since we're using phone auth
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
        ]);

        // Clear temp token
        \Illuminate\Support\Facades\Cache::forget('registration_temp_' . $request->temp_token);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم التسجيل بنجاح',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login user with phone and verification code
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        // Verify code
        $verified = VerificationCode::verify($request->phone, $request->code, 'login');
        
        if (!$verified) {
            return response()->json([
                'success' => false,
                'message' => 'كود التحقق غير صحيح أو منتهي الصلاحية',
            ], 422);
        }

        // Find user
        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الهاتف غير مسجل',
            ], 404);
        }

        // Update phone verified at
        $user->update(['phone_verified_at' => now()]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }
}

