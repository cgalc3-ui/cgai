<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeReadyAppsSection;
use App\Models\ReadyAppCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HomeReadyAppsSectionController extends Controller
{
    public function index()
    {
        $section = HomeReadyAppsSection::first();
        $allCategories = ReadyAppCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        
        // Always show all active categories, regardless of category_ids
        // This ensures newly added categories appear automatically
        $sectionCategories = $allCategories;

        return view('admin.ready-apps-section.index', compact('section', 'allCategories', 'sectionCategories'));
    }

    public function create()
    {
        $section = null;
        $view = view('admin.ready-apps-section.header-modal', compact('section'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'heading_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
        ]);

        try {
            // Delete old section if exists
            $oldSection = HomeReadyAppsSection::first();
            if ($oldSection) {
                $oldSection->delete();
            }

            // Get all active category IDs
            $allCategoryIds = ReadyAppCategory::where('is_active', true)->pluck('id')->toArray();
            
            // If no categories, use empty array
            if (empty($allCategoryIds)) {
                $allCategoryIds = [];
            }

            $data = $request->only(['heading', 'heading_en', 'description', 'description_en']);
            $data['is_active'] = $request->has('is_active');
            $data['category_ids'] = $allCategoryIds; // Include all active categories by default

            HomeReadyAppsSection::create($data);

            return response()->json([
                'success' => true,
                'message' => __('messages.ready_apps_section_created'),
                'redirect' => route('admin.customer-facing.ready-apps-section.index')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error') ?? 'خطأ في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('HomeReadyAppsSection Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred') ?? 'حدث خطأ أثناء الحفظ',
                'error' => config('app.debug') ? $e->getMessage() : 'حدث خطأ غير متوقع'
            ], 500);
        }
    }

    public function edit(HomeReadyAppsSection $homeReadyAppsSection)
    {
        $section = $homeReadyAppsSection;
        $view = view('admin.ready-apps-section.header-modal', compact('section'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, HomeReadyAppsSection $homeReadyAppsSection)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'heading_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'category_ids' => 'nullable',
        ]);

        try {
            $data = $request->only(['heading', 'heading_en', 'description', 'description_en']);
            $data['is_active'] = $request->has('is_active');
            
            // Keep existing category_ids if not provided
            if ($request->has('category_ids')) {
                if (is_string($request->category_ids)) {
                    $data['category_ids'] = json_decode($request->category_ids, true);
                } else {
                    $data['category_ids'] = $request->category_ids;
                }
            } else {
                $data['category_ids'] = $homeReadyAppsSection->category_ids;
            }

            $homeReadyAppsSection->update($data);

            return response()->json([
                'success' => true,
                'message' => __('messages.ready_apps_section_updated'),
                'redirect' => route('admin.customer-facing.ready-apps-section.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred') ?? 'حدث خطأ أثناء الحفظ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(HomeReadyAppsSection $homeReadyAppsSection)
    {
        $homeReadyAppsSection->delete();

        return redirect()->route('admin.customer-facing.ready-apps-section.index')
            ->with('success', __('messages.ready_apps_section_deleted'));
    }

    /**
     * Edit a single category
     */
    public function editCategory(ReadyAppCategory $category)
    {
        $view = view('admin.ready-apps-section.category-modal', compact('category'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    /**
     * Update a single category
     */
    public function updateCategory(Request $request, ReadyAppCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp|max:2048',
        ]);

        try {
            $data = $request->only(['name', 'name_en', 'description', 'description_en']);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image) {
                    // Handle both formats: with /storage/ prefix and without
                    $oldPath = $category->image;
                    if (strpos($oldPath, '/storage/') === 0) {
                        $oldPath = str_replace('/storage/', '', $oldPath);
                    } else if (strpos($oldPath, 'storage/') === 0) {
                        $oldPath = str_replace('storage/', '', $oldPath);
                    }
                    Storage::disk('public')->delete($oldPath);
                }
                // Store new image using Storage::url() to match ReadyAppCategoryController format
                $data['image'] = Storage::url($request->file('image')->store('ready-apps/categories', 'public'));
            }

            $category->update($data);

            return response()->json([
                'success' => true,
                'message' => __('messages.ready_app_category_updated') ?? 'تم تحديث الفئة بنجاح',
                'redirect' => route('admin.customer-facing.ready-apps-section.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred') ?? 'حدث خطأ أثناء الحفظ',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

