<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class ConsultationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all consultations
     */
    public function index(Request $request)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $query = Consultation::with('category')
            ->where('is_active', true);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $consultations = $query->with('pointsPricing')->get();

        // Filter locale columns and add points pricing
        $filteredData = $consultations->map(function ($consultation) {
            $data = $this->filterLocaleColumns($consultation);
            $data['points_price'] = $consultation->pointsPricing && $consultation->pointsPricing->is_active 
                ? (float) $consultation->pointsPricing->points_price 
                : null;
            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => $filteredData,
        ]);
    }

    /**
     * Get single consultation
     */
    public function show(Request $request, $id)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $consultation = Consultation::with(['category', 'pointsPricing'])->findOrFail($id);
        
        $data = $this->filterLocaleColumns($consultation);
        $data['points_price'] = $consultation->pointsPricing && $consultation->pointsPricing->is_active 
            ? (float) $consultation->pointsPricing->points_price 
            : null;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get consultations by category
     */
    public function byCategory(Request $request, $categoryId)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $consultations = Consultation::with(['category', 'pointsPricing'])
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->get();

        // Filter locale columns and add points pricing
        $filteredData = $consultations->map(function ($consultation) {
            $data = $this->filterLocaleColumns($consultation);
            $data['points_price'] = $consultation->pointsPricing && $consultation->pointsPricing->is_active 
                ? (float) $consultation->pointsPricing->points_price 
                : null;
            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => $filteredData,
        ]);
    }
}
