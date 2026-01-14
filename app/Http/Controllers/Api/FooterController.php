<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    /**
     * Get footer section
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

        $footer = Footer::active()->first();

        if (!$footer) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }

        $data = [
            'id' => $footer->id,
            'logo' => $footer->trans('logo'),
            'description' => $footer->trans('description'),
            'email' => $footer->email,
            'phone' => $footer->phone,
            'working_hours' => $footer->trans('working_hours'),
            'quick_links' => $footer->quick_links ? array_map(function($link) use ($locale) {
                return [
                    'title' => $locale === 'ar' ? ($link['title'] ?? '') : ($link['title_en'] ?? $link['title'] ?? ''),
                    'link' => $link['link'] ?? '#',
                ];
            }, $footer->quick_links) : [],
            'content_links' => $footer->content_links ? array_map(function($link) use ($locale) {
                return [
                    'title' => $locale === 'ar' ? ($link['title'] ?? '') : ($link['title_en'] ?? $link['title'] ?? ''),
                    'link' => $link['link'] ?? '#',
                ];
            }, $footer->content_links) : [],
            'support_links' => $footer->support_links ? array_map(function($link) use ($locale) {
                return [
                    'title' => $locale === 'ar' ? ($link['title'] ?? '') : ($link['title_en'] ?? $link['title'] ?? ''),
                    'link' => $link['link'] ?? '#',
                ];
            }, $footer->support_links) : [],
            'social_media' => $footer->social_media ? array_map(function($social) {
                return [
                    'platform' => $social['platform'] ?? '',
                    'url' => $social['url'] ?? '#',
                    'icon' => $social['icon'] ?? null,
                ];
            }, $footer->social_media) : [],
            'copyright_text' => $footer->trans('copyright_text'),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}


