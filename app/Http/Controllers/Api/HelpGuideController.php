<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HelpGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HelpGuideController extends Controller
{
    /**
     * Get help guides for the authenticated user based on their role
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Set locale from request header or default to Arabic
        $locale = $request->header('Accept-Language', 'ar');
        if (in_array($locale, ['en', 'ar'])) {
            App::setLocale($locale);
        } else {
            App::setLocale('ar'); // Default to Arabic
        }

        // Determine user role
        $role = 'customer';
        if ($user->isAdmin()) {
            $role = 'admin';
        } elseif ($user->isStaff()) {
            $role = 'staff';
        }

        // Get help guides for the user's role
        $helpGuides = HelpGuide::forRole($role)
            ->active()
            ->ordered()
            ->get();

        // Format the response - return both Arabic and English
        $formattedGuides = $helpGuides->map(function ($guide) {
            return [
                'id' => $guide->id,
                'title' => $guide->title,
                'title_en' => $guide->title_en,
                'content' => $guide->content,
                'content_en' => $guide->content_en,
                'icon' => $guide->icon,
                'sort_order' => $guide->sort_order,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedGuides,
            'role' => $role,
        ]);
    }

    /**
     * Get a specific help guide by ID
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        // Set locale from request header or default to Arabic
        $locale = $request->header('Accept-Language', 'ar');
        if (in_array($locale, ['en', 'ar'])) {
            App::setLocale($locale);
        } else {
            App::setLocale('ar'); // Default to Arabic
        }

        // Determine user role
        $role = 'customer';
        if ($user->isAdmin()) {
            $role = 'admin';
        } elseif ($user->isStaff()) {
            $role = 'staff';
        }

        $helpGuide = HelpGuide::forRole($role)
            ->active()
            ->findOrFail($id);

        // Return both Arabic and English
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $helpGuide->id,
                'title' => $helpGuide->title,
                'title_en' => $helpGuide->title_en,
                'content' => $helpGuide->content,
                'content_en' => $helpGuide->content_en,
                'icon' => $helpGuide->icon,
                'sort_order' => $helpGuide->sort_order,
            ],
        ]);
    }
}
