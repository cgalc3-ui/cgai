<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReadyAppCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReadyAppCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ReadyAppCategory::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.ready-apps.categories.index', compact('categories'));
    }

    public function create(Request $request)
    {
        $view = view('admin.ready-apps.categories.create-modal');

        if ($request->ajax()) {
            return response()->json([
                'html' => $view->render()
            ]);
        }

        return $view;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ready_app_categories,slug',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['sort_order'] = $request->sort_order ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = Storage::url($request->file('image')->store('ready-apps/categories', 'public'));
        }

        ReadyAppCategory::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.category_created_success'),
                'redirect' => route('admin.ready-apps.categories.index')
            ]);
        }

        return redirect()->route('admin.ready-apps.categories.index')
            ->with('success', __('messages.category_created_success'));
    }

    public function edit(Request $request, ReadyAppCategory $category)
    {
        $view = view('admin.ready-apps.categories.edit-modal', compact('category'));

        if ($request->ajax()) {
            return response()->json([
                'html' => $view->render()
            ]);
        }

        return $view;
    }

    public function update(Request $request, ReadyAppCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ready_app_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['sort_order'] = $request->sort_order ?? 0;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                $oldPath = str_replace(Storage::url(''), '', $category->image);
                if (empty($oldPath)) {
                    $oldPath = str_replace('/storage/', '', $category->image);
                }
                Storage::disk('public')->delete($oldPath);
            }
            $data['image'] = Storage::url($request->file('image')->store('ready-apps/categories', 'public'));
        }

        $category->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.category_updated_success'),
                'redirect' => route('admin.ready-apps.categories.index')
            ]);
        }

        return redirect()->route('admin.ready-apps.categories.index')
            ->with('success', __('messages.category_updated_success'));
    }

    public function destroy(ReadyAppCategory $category)
    {
        // Check if category has apps
        if ($category->apps()->count() > 0) {
            return redirect()->route('admin.ready-apps.categories.index')
                ->with('error', __('messages.category_cannot_delete_has_apps'));
        }

        $category->delete();

        return redirect()->route('admin.ready-apps.categories.index')
            ->with('success', __('messages.category_deleted_success'));
    }
}
