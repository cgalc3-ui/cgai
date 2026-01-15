<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\AiService;
use Illuminate\Http\Request;

class AiTechnologiesController extends Controller
{
    /**
     * Get latest technologies and best technologies of the month
     */
    public function index(Request $request)
    {
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        // Latest Technologies
        $latestTechnologies = AiService::with(['category', 'images'])
            ->where('is_active', true)
            ->where('is_latest', true)
            ->latest()
            ->get()
            ->map(function ($service) use ($locale) {
                return $this->formatService($service, $locale);
            });

        // Best Technologies of the Month
        $bestTechnologies = AiService::with(['category', 'images'])
            ->where('is_active', true)
            ->where('is_best_of_month', true)
            ->orderBy('purchases_count', 'desc')
            ->orderBy('rating', 'desc')
            ->get()
            ->map(function ($service) use ($locale) {
                return $this->formatService($service, $locale);
            });

        return response()->json([
            'success' => true,
            'data' => [
                'latest_technologies' => $latestTechnologies,
                'best_technologies_of_month' => $bestTechnologies,
            ],
        ]);
    }

    /**
     * Format service data for API response
     */
    private function formatService($service, $locale)
    {
        return [
            'id' => $service->id,
            'name' => $locale === 'en' && $service->name_en 
                ? $service->name_en 
                : $service->name,
            'description' => $locale === 'en' && $service->description_en 
                ? $service->description_en 
                : ($service->description ?? ''),
            'short_description' => $locale === 'en' && $service->short_description_en 
                ? $service->short_description_en 
                : ($service->short_description ?? ''),
            'price' => (float) $service->price,
            'original_price' => $service->original_price ? (float) $service->original_price : null,
            'image' => $service->image ? asset($service->image) : null,
            'images' => $service->images->map(function ($img) {
                return asset($img->image_path);
            }),
            'category' => $service->category ? [
                'id' => $service->category->id,
                'name' => $locale === 'en' && $service->category->name_en 
                    ? $service->category->name_en 
                    : $service->category->name,
            ] : null,
            'rating' => (float) ($service->rating ?? 0),
            'reviews_count' => (int) ($service->reviews_count ?? 0),
            'purchases_count' => (int) ($service->purchases_count ?? 0),
            'is_featured' => (bool) $service->is_featured,
            'is_latest' => (bool) $service->is_latest,
            'is_best_of_month' => (bool) $service->is_best_of_month,
            'slug' => $service->slug,
        ];
    }
}

