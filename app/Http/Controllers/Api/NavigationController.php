<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use Illuminate\Http\Request;

class NavigationController extends Controller
{
    /**
     * Get navigation items (logo, menu items, buttons)
     * Public endpoint for frontend
     */
    public function index(Request $request)
    {
        $locale = $request->header('locale', $request->get('locale', app()->getLocale()));
        app()->setLocale($locale);

        $logo = NavigationItem::byType('logo')->active()->first();
        $menuItems = NavigationItem::byType('menu_item')->active()->orderBy('id')->get();
        $buttons = NavigationItem::byType('button')->active()->orderBy('id')->get();

        $data = [
            'logo' => $logo ? $this->formatNavigationItem($logo) : null,
            'menu_items' => $menuItems->map(function ($item) {
                return $this->formatNavigationItem($item);
            }),
            'buttons' => $buttons->map(function ($item) {
                return $this->formatNavigationItem($item);
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Format navigation item for API response
     */
    private function formatNavigationItem($item)
    {
        $formatted = [
            'id' => $item->id,
            'item_type' => $item->item_type,
            'title' => $item->trans('title'),
            'link' => $item->link,
            'target' => $item->target,
        ];

        // Icon removed - no longer used

        // Add image URL if exists
        if ($item->image) {
            $formatted['image'] = asset('storage/' . $item->image);
        }

        return $formatted;
    }
}

