<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiServiceCategory;
use Illuminate\Http\Request;

class AiServiceCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = AiServiceCategory::query();

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

        return view('admin.ai-services.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.ai-services.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_service_categories,slug',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['sort_order'] = $request->sort_order ?? 0;

        AiServiceCategory::create($data);

        return redirect()->route('admin.ai-services.categories.index')
            ->with('success', __('messages.ai_service_category_created_success'));
    }

    public function edit(AiServiceCategory $category)
    {
        return view('admin.ai-services.categories.edit', compact('category'));
    }

    public function update(Request $request, AiServiceCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_service_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['sort_order'] = $request->sort_order ?? 0;

        $category->update($data);

        return redirect()->route('admin.ai-services.categories.index')
            ->with('success', __('messages.ai_service_category_updated_success'));
    }

    public function destroy(AiServiceCategory $category)
    {
        // Check if category has services
        if ($category->services()->count() > 0) {
            return redirect()->route('admin.ai-services.categories.index')
                ->with('error', __('messages.ai_service_category_cannot_delete_has_services'));
        }

        // Check if category has requests
        if ($category->requests()->count() > 0) {
            return redirect()->route('admin.ai-services.categories.index')
                ->with('error', __('messages.ai_service_category_cannot_delete_has_requests'));
        }

        $category->delete();

        return redirect()->route('admin.ai-services.categories.index')
            ->with('success', __('messages.ai_service_category_deleted_success'));
    }
}
