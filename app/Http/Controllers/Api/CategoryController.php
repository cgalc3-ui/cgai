<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        $category = Category::create($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.category_created_success'),
            'data' => $category,
        ], 201);
    }

    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'data' => $category->load('subCategories'),
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.category_updated_success'),
            'data' => $category->fresh(),
        ]);
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.category_deleted_success'),
        ]);
    }
}
