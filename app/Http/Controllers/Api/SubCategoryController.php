<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class SubCategoryController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $query = SubCategory::with('category')->where('is_active', true);
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        $subCategories = $query->orderBy('name')->get();
        
        // Filter locale columns
        $filteredData = $subCategories->map(function ($subCategory) {
            return $this->filterLocaleColumns($subCategory);
        });
        
        return response()->json([
            'success' => true,
            'data' => $filteredData,
        ]);
    }

    public function store(StoreSubCategoryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        $subCategory = SubCategory::create($data);
        $subCategory->load('category');

        return response()->json([
            'success' => true,
            'message' => __('messages.subcategory_created_success'),
            'data' => $this->filterLocaleColumns($subCategory),
        ], 201);
    }

    public function show(Request $request, SubCategory $subCategory)
    {
        // Set locale from request
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $subCategory->load(['category', 'services']);

        return response()->json([
            'success' => true,
            'data' => $this->filterLocaleColumns($subCategory),
        ]);
    }

    public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        $subCategory->update($data);
        $subCategory->fresh()->load('category');

        return response()->json([
            'success' => true,
            'message' => __('messages.subcategory_updated_success'),
            'data' => $this->filterLocaleColumns($subCategory->fresh()),
        ]);
    }

    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.subcategory_deleted_success'),
        ]);
    }
}
