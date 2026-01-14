<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

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

        $categories = $query->latest()->paginate(10)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function create(Request $request)
    {
        $view = view('admin.categories.create-modal');

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        if ($request->hasFile('image')) {
            $data['image'] = Storage::url($request->file('image')->store('categories', 'public'));
        }

        Category::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.category_created'),
                'redirect' => route('admin.categories.index')
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', __('messages.category_created'));
    }

    public function edit(Request $request, Category $category)
    {
        $view = view('admin.categories.edit-modal', compact('category'));

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                $oldPath = str_replace(Storage::url(''), '', $category->image);
                if (empty($oldPath)) {
                    $oldPath = str_replace('/storage/', '', $category->image);
                }
                Storage::disk('public')->delete($oldPath);
            }
            $data['image'] = Storage::url($request->file('image')->store('categories', 'public'));
        }

        $category->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.category_updated'),
                'redirect' => route('admin.categories.index')
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', __('messages.category_updated'));
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم حذف الفئة بنجاح');
    }
}
