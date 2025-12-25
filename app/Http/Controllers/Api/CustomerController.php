<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

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
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'customer' => $user,
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
                'message' => 'ليس لديك صلاحية للوصول',
            ], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $user->update($request->only(['name']));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'customer' => $user->fresh(),
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

        // يمكنك إضافة بيانات Dashboard هنا
        return response()->json([
            'success' => true,
            'data' => [
                'customer' => $user,
                'stats' => [
                    // إضافة إحصائيات العميل هنا
                ],
            ],
        ]);
    }
}
