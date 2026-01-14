<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConsultationBookingSection;
use Illuminate\Http\Request;

class ConsultationBookingSectionController extends Controller
{
    /**
     * Get consultation booking section
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

        $section = ConsultationBookingSection::active()->first();

        if (!$section) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }

        $data = [
            'id' => $section->id,
            'heading' => $section->trans('heading'),
            'description' => $section->trans('description'),
            'background_image' => $section->background_image ? asset('storage/' . $section->background_image) : null,
            'buttons' => $section->buttons ? array_map(function($button) use ($locale) {
                return [
                    'title' => $locale === 'ar' ? ($button['title'] ?? '') : ($button['title_en'] ?? $button['title'] ?? ''),
                    'link' => $button['link'] ?? '#',
                    'target' => $button['target'] ?? '_self',
                    'style' => $button['style'] ?? 'primary',
                ];
            }, $section->buttons) : [],
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}

