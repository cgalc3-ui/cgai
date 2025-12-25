<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SubCategory::with('category')->where('is_active', true);
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        $subCategories = $query->orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $subCategories,
        ]);
    }

    public function store(StoreSubCategoryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        $subCategory = SubCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الفئة الفرعية بنجاح',
            'data' => $subCategory->load('category'),
        ], 201);
    }

    public function show(SubCategory $subCategory)
    {
        return response()->json([
            'success' => true,
            'data' => $subCategory->load(['category', 'services']),
        ]);
    }

    public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        $subCategory->update($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الفئة الفرعية بنجاح',
            'data' => $subCategory->fresh()->load('category'),
        ]);
    }

    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الفئة الفرعية بنجاح',
        ]);
    }
}
