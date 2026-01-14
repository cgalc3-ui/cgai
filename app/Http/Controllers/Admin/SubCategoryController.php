<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubCategoryRequest;
use App\Http\Requests\UpdateSubCategoryRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SubCategory::with('category');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $subCategories = $query->latest()->paginate(10)->withQueryString();

        // Get categories for filter dropdown
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.sub-categories.index', compact('subCategories', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $view = view('admin.sub-categories.create-modal', compact('categories'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(StoreSubCategoryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        if (empty($data['slug'])) {
            unset($data['slug']);
        }
        
        SubCategory::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.subcategory_created_success'),
                'redirect' => route('admin.sub-categories.index')
            ]);
        }

        return redirect()->route('admin.sub-categories.index')
            ->with('success', 'تم إنشاء الفئة الفرعية بنجاح');
    }

    public function edit(Request $request, SubCategory $subCategory)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $view = view('admin.sub-categories.edit-modal', compact('subCategory', 'categories'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(UpdateSubCategoryRequest $request, SubCategory $subCategory)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        if (empty($data['slug'])) {
            unset($data['slug']);
        }
        
        $subCategory->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.subcategory_updated_success'),
                'redirect' => route('admin.sub-categories.index')
            ]);
        }

        return redirect()->route('admin.sub-categories.index')
            ->with('success', 'تم تحديث الفئة الفرعية بنجاح');
    }

    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();

        return redirect()->route('admin.sub-categories.index')
            ->with('success', 'تم حذف الفئة الفرعية بنجاح');
    }
}
