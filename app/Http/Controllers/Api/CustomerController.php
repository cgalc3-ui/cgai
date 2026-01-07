<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        $user = $request->user();

        if (!$user->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        // Get wallet balance
        $wallet = $user->getOrCreateWallet();
        $pointsSettings = \App\Models\PointsSetting::getActive();

        // Get booking statistics
        $totalBookings = \App\Models\Booking::where('customer_id', $user->id)->count();
        $pendingBookings = \App\Models\Booking::where('customer_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $completedBookings = \App\Models\Booking::where('customer_id', $user->id)
            ->where('status', 'completed')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar_url,
                ],
                'wallet' => [
                    'balance' => (float) $wallet->balance,
                ],
                'points_settings' => [
                    'points_per_riyal' => $pointsSettings ? (float) $pointsSettings->points_per_riyal : 10.0,
                    'is_active' => $pointsSettings ? $pointsSettings->is_active : false,
                ],
                'stats' => [
                    'total_bookings' => $totalBookings,
                    'pending_bookings' => $pendingBookings,
                    'completed_bookings' => $completedBookings,
                ],
            ],
        ]);
    }
}
