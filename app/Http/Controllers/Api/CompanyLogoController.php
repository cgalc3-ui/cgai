<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyLogo;
use Illuminate\Http\Request;

class CompanyLogoController extends Controller
{
    /**
     * Get company logos section
     * Public endpoint for frontend
     */
    public function index(Request $request)
    {
        // Get locale from query parameter, header, or default
        $locale = $request->get('locale') 
            ?? $request->header('locale') 
            ?? $request->header('X-Locale')
            ?? app()->getLocale();
        
        // Normalize locale (handle 'en-US' -> 'en', 'ar-SA' -> 'ar')
        if ($locale && strpos($locale, '-') !== false) {
            $locale = substr($locale, 0, 2);
        }
        
        // Validate and set locale
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = app()->getLocale();
        }
        
        app()->setLocale($locale);

        $companyLogo = CompanyLogo::active()->first();

        if (!$companyLogo) {
            return response()->json([
                'success' => true,
                'data' => null
            ]);
        }

        $data = [
            'id' => $companyLogo->id,
            'heading' => $companyLogo->trans('heading'),
            'logos' => $companyLogo->logos ? array_map(function($logo) {
                return [
                    'image' => isset($logo['image']) && $logo['image'] ? asset('storage/' . $logo['image']) : null,
                    'link' => $logo['link'] ?? null,
                    'name' => $logo['name'] ?? null,
                ];
            }, $companyLogo->logos) : [],
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
