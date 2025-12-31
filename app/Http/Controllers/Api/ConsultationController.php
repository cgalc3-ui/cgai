<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Category;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Get all consultations
     */
    public function index(Request $request)
    {
        $query = Consultation::with('category')
            ->where('is_active', true);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $consultations = $query->get();

        return response()->json([
            'success' => true,
            'data' => $consultations->map(function ($consultation) {
                return [
                    'id' => $consultation->id,
                    'name' => $consultation->name,
                    'slug' => $consultation->slug,
                    'description' => $consultation->description,
                    'fixed_price' => $consultation->fixed_price,
                    'price' => $consultation->price,
                    'category' => [
                        'id' => $consultation->category->id,
                        'name' => $consultation->category->name,
                    ],
                ];
            }),
        ]);
    }

    /**
     * Get single consultation
     */
    public function show($id)
    {
        $consultation = Consultation::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $consultation->id,
                'name' => $consultation->name,
                'slug' => $consultation->slug,
                'description' => $consultation->description,
                'fixed_price' => $consultation->fixed_price,
                'price' => $consultation->price,
                'category' => [
                    'id' => $consultation->category->id,
                    'name' => $consultation->category->name,
                ],
            ],
        ]);
    }

    /**
     * Get consultations by category
     */
    public function byCategory($categoryId)
    {
        $consultations = Consultation::with('category')
            ->where('category_id', $categoryId)
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $consultations,
        ]);
    }
}
