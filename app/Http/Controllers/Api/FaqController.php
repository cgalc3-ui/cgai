<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class FaqController extends Controller
{
    use ApiResponseTrait;

    /**
     * Display a listing of active FAQs.
     */
    public function index(Request $request)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $faqs = Faq::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        // Filter locale columns for each group
        $filteredData = $faqs->map(function ($group) {
            return $group->map(function ($faq) {
                return $this->filterLocaleColumns($faq);
            })->values();
        });

        return response()->json([
            'success' => true,
            'data' => $filteredData
        ]);
    }

    /**
     * Display a listing of FAQs by category.
     */
    public function getByCategory(Request $request, $category)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $faqs = Faq::where('is_active', true)
            ->where('category', $category)
            ->orderBy('sort_order')
            ->get();

        // Filter locale columns
        $filteredData = $faqs->map(function ($faq) {
            return $this->filterLocaleColumns($faq);
        });

        return response()->json([
            'success' => true,
            'data' => $filteredData
        ]);
    }
}
