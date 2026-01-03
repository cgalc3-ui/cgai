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

        // Format the response
        $currentLocale = App::getLocale();
        $formattedGuides = $helpGuides->map(function ($guide) use ($currentLocale) {
            // Get translated content based on current locale
            if ($currentLocale === 'en') {
                $title = !empty($guide->title_en) ? $guide->title_en : $guide->title;
                $content = !empty($guide->content_en) ? $guide->content_en : $guide->content;
            } else {
                // Arabic (default)
                $title = $guide->title;
                $content = $guide->content;
            }
            
            return [
                'id' => $guide->id,
                'title' => $title,
                'content' => $content,
                'icon' => $guide->icon,
                'sort_order' => $guide->sort_order,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedGuides,
            'role' => $role,
            'locale' => App::getLocale(),
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

        // Get translated content based on current locale
        $currentLocale = App::getLocale();
        if ($currentLocale === 'en') {
            $title = !empty($helpGuide->title_en) ? $helpGuide->title_en : $helpGuide->title;
            $content = !empty($helpGuide->content_en) ? $helpGuide->content_en : $helpGuide->content;
        } else {
            // Arabic (default)
            $title = $helpGuide->title;
            $content = $helpGuide->content;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $helpGuide->id,
                'title' => $title,
                'content' => $content,
                'icon' => $helpGuide->icon,
                'sort_order' => $helpGuide->sort_order,
            ],
            'locale' => $currentLocale,
        ]);
    }
}
