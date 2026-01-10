<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HelpGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Traits\ApiResponseTrait;

class HelpGuideController extends Controller
{
    use ApiResponseTrait;
    /**
     * Get help guides for the authenticated user based on their role
     */
    public function index(Request $request)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $user = $request->user();

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

        // Filter locale columns
        $filteredGuides = $formattedGuides->map(function ($guide) {
            return $this->filterLocaleColumns($guide);
        });

        return response()->json([
            'success' => true,
            'data' => $filteredGuides,
            'role' => $role,
        ]);
    }

    /**
     * Get a specific help guide by ID
     */
    public function show(Request $request, $id)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $user = $request->user();

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

        // Format and filter locale columns
        $formattedGuide = [
            'id' => $helpGuide->id,
            'title' => $helpGuide->title,
            'title_en' => $helpGuide->title_en,
            'content' => $helpGuide->content,
            'content_en' => $helpGuide->content_en,
            'icon' => $helpGuide->icon,
            'sort_order' => $helpGuide->sort_order,
        ];

        return response()->json([
            'success' => true,
            'data' => $this->filterLocaleColumns($formattedGuide),
        ]);
    }
}
