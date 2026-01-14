<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroSection;
use Illuminate\Http\Request;

class HeroController extends Controller
{
    /**
     * Get hero section
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

        $hero = HeroSection::active()->first();

        if (!$hero) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }

        $data = [
            'id' => $hero->id,
            'heading' => $hero->trans('heading'),
            'subheading' => $hero->trans('subheading'),
            'description' => $hero->trans('description'),
            'background_image' => $hero->background_image ? asset('storage/' . $hero->background_image) : null,
            'buttons' => $hero->buttons ? array_map(function($button) use ($locale) {
                return [
                    'title' => $locale === 'ar' ? ($button['title'] ?? '') : ($button['title_en'] ?? $button['title'] ?? ''),
                    'link' => $button['link'] ?? '#',
                    'target' => $button['target'] ?? '_self',
                    'style' => $button['style'] ?? 'primary',
                ];
            }, $hero->buttons) : [],
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}

