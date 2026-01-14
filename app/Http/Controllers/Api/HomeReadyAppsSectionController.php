<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HomeReadyAppsSection;
use App\Models\ReadyAppCategory;
use Illuminate\Http\Request;

class HomeReadyAppsSectionController extends Controller
{
    /**
     * Get ready apps section
     * Public endpoint for frontend
     */
    public function index(Request $request)
    {
        // Get locale from query parameter, header, or default
        $locale = $request->get('locale') 
            ?? $request->header('locale') 
            ?? $request->header('X-Locale')
            ?? app()->getLocale();
        
        // Clean and normalize locale
        $locale = strtolower(trim($locale));
        
        // Normalize locale (handle 'en-US' -> 'en', 'ar-SA' -> 'ar')
        if ($locale && strpos($locale, '-') !== false) {
            $locale = substr($locale, 0, 2);
        }
        
        // Validate and set locale
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = app()->getLocale();
        }
        
        app()->setLocale($locale);

        $section = HomeReadyAppsSection::active()->first();

        // Always show all active categories, regardless of category_ids
        // This ensures newly added categories appear automatically
        $categories = ReadyAppCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if (!$section) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => null,
                    'heading' => null,
                    'description' => null,
                    'categories' => $categories->map(function($category) use ($locale) {
                        return [
                            'id' => $category->id,
                            'name' => $category->trans('name'),
                            'slug' => $category->slug,
                            'description' => $category->trans('description'),
                            'image' => $category->image ? (strpos($category->image, '/storage/') === 0 ? asset($category->image) : asset('storage/' . $category->image)) : null,
                        ];
                    }),
                ]
            ]);
        }

        $data = [
            'id' => $section->id,
            'heading' => $section->trans('heading'),
            'description' => $section->trans('description'),
            'categories' => $categories->map(function($category) use ($locale) {
                return [
                    'id' => $category->id,
                    'name' => $category->trans('name'),
                    'slug' => $category->slug,
                    'description' => $category->trans('description'),
                    'image' => $category->image ? (strpos($category->image, '/storage/') === 0 ? asset($category->image) : asset('storage/' . $category->image)) : null,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}

