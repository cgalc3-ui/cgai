<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeServicesSection;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeServicesSectionController extends Controller
{
    public function index()
    {
        $section = HomeServicesSection::first();
        $allCategories = Category::where('is_active', true)->orderBy('name')->get();
        
        // If section exists and has categories, use them, otherwise show all active categories
        if ($section && $section->category_ids && !empty($section->category_ids)) {
            $sectionCategories = $section->categories();
        } else {
            // Show all active categories if no section or no categories selected
            $sectionCategories = $allCategories;
        }

        return view('admin.services-section.index', compact('section', 'allCategories', 'sectionCategories'));
    }

    public function create()
    {
        $section = null;
        $view = view('admin.services-section.header-modal', compact('section'));
        
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
            $oldSection = HomeServicesSection::first();
            if ($oldSection) {
                $oldSection->delete();
            }

            // Get all active category IDs
            $allCategoryIds = Category::where('is_active', true)->pluck('id')->toArray();
            
            // If no categories, use empty array
            if (empty($allCategoryIds)) {
                $allCategoryIds = [];
            }

            $data = $request->only(['heading', 'heading_en', 'description', 'description_en']);
            $data['is_active'] = $request->has('is_active');
            $data['category_ids'] = $allCategoryIds; // Include all active categories by default

            $section = HomeServicesSection::create($data);

            return response()->json([
                'success' => true,
                'message' => __('messages.services_section_created'),
                'redirect' => route('admin.customer-facing.services-section.index')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_error') ?? 'خطأ في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('HomeServicesSection Store Error: ' . $e->getMessage(), [
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

    public function edit(HomeServicesSection $homeServicesSection)
    {
        $section = $homeServicesSection;
        $view = view('admin.services-section.header-modal', compact('section'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, HomeServicesSection $homeServicesSection)
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
                $data['category_ids'] = $homeServicesSection->category_ids;
            }

            $homeServicesSection->update($data);

            return response()->json([
                'success' => true,
                'message' => __('messages.services_section_updated'),
                'redirect' => route('admin.customer-facing.services-section.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred') ?? 'حدث خطأ أثناء الحفظ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(HomeServicesSection $homeServicesSection)
    {
        $homeServicesSection->delete();

        return redirect()->route('admin.customer-facing.services-section.index')
            ->with('success', __('messages.services_section_deleted'));
    }

    /**
     * Edit a single category
     */
    public function editCategory(Category $category)
    {
        $view = view('admin.services-section.category-modal', compact('category'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    /**
     * Update a single category
     */
    public function updateCategory(Request $request, Category $category)
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
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
                // Store new image using Storage::url() to match CategoryController format
                $data['image'] = \Illuminate\Support\Facades\Storage::url($request->file('image')->store('categories', 'public'));
            }

            $category->update($data);

            return response()->json([
                'success' => true,
                'message' => __('messages.service_updated') ?? 'تم تحديث الخدمة بنجاح',
                'redirect' => route('admin.customer-facing.services-section.index')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred') ?? 'حدث خطأ أثناء الحفظ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update categories data
     */
    private function updateCategories($categoryIds, $data)
    {
        // Only update if data is provided
        if (empty($data['category_names']) && empty($data['category_names_en']) && 
            empty($data['category_descriptions']) && empty($data['category_descriptions_en']) &&
            empty($data['category_images'])) {
            return;
        }

        foreach ($categoryIds as $categoryId) {
            $category = Category::find($categoryId);
            if (!$category) continue;

            $updateData = [];

            // Use category_id as key (not index)
            if (isset($data['category_names'][$categoryId]) && !empty($data['category_names'][$categoryId])) {
                $updateData['name'] = $data['category_names'][$categoryId];
            }
            if (isset($data['category_names_en'][$categoryId]) && !empty($data['category_names_en'][$categoryId])) {
                $updateData['name_en'] = $data['category_names_en'][$categoryId];
            }
            if (isset($data['category_descriptions'][$categoryId]) && !empty($data['category_descriptions'][$categoryId])) {
                $updateData['description'] = $data['category_descriptions'][$categoryId];
            }
            if (isset($data['category_descriptions_en'][$categoryId]) && !empty($data['category_descriptions_en'][$categoryId])) {
                $updateData['description_en'] = $data['category_descriptions_en'][$categoryId];
            }

            if (!empty($updateData)) {
                $category->update($updateData);
            }

            // Handle image upload
            if (isset($data['category_images'][$categoryId]) && $data['category_images'][$categoryId]) {
                $image = $data['category_images'][$categoryId];
                if ($image->isValid()) {
                    // Delete old image
                    if ($category->image) {
                        // Handle both formats: with /storage/ prefix and without
                        $oldPath = $category->image;
                        if (strpos($oldPath, '/storage/') === 0) {
                            $oldPath = str_replace('/storage/', '', $oldPath);
                        } else if (strpos($oldPath, 'storage/') === 0) {
                            $oldPath = str_replace('storage/', '', $oldPath);
                        }
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                    }
                    // Store new image using Storage::url() to match CategoryController format
                    $imagePath = \Illuminate\Support\Facades\Storage::url($image->store('categories', 'public'));
                    $category->update(['image' => $imagePath]);
                }
            }
        }
    }
}

