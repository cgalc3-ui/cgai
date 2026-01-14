<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiService;
use App\Models\AiServiceCategory;
use App\Models\AiServiceImage;
use App\Models\AiServiceScreenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AiServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = AiService::with('category');

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

        $services = $query->latest()->paginate(10)->withQueryString();

        // Get categories for filter dropdown
        $categories = AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.ai-services.services.index', compact('services', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $view = view('admin.ai-services.services.create-modal', compact('categories'));

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:ai_service_categories,id',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_services,slug',
            'short_description' => 'nullable|string|max:500',
            'short_description_en' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'full_description' => 'nullable|string',
            'full_description_en' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'video_url' => 'nullable|url',
            'video_thumbnail' => 'nullable|url',
            'specifications' => 'nullable|array',
            'tags' => 'nullable|array',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'screenshots' => 'nullable|array',
            'screenshots.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_free' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['is_free'] = $request->has('is_free') ? true : false;
        $data['currency'] = $data['currency'] ?? 'SAR';

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        DB::beginTransaction();
        try {
            $service = AiService::create($data);

            // Handle main image upload
            if ($request->hasFile('main_image')) {
                $mainImagePath = $request->file('main_image')->store('ai-services/' . $service->id, 'public');
                AiServiceImage::create([
                    'ai_service_id' => $service->id,
                    'url' => Storage::url($mainImagePath),
                    'type' => 'main',
                    'order' => 0,
                ]);
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                $order = 1;
                foreach ($request->file('gallery_images') as $image) {
                    $galleryImagePath = $image->store('ai-services/' . $service->id . '/gallery', 'public');
                    AiServiceImage::create([
                        'ai_service_id' => $service->id,
                        'url' => Storage::url($galleryImagePath),
                        'type' => 'gallery',
                        'order' => $order++,
                    ]);
                }
            }

            // Handle screenshots upload
            if ($request->hasFile('screenshots')) {
                $order = 1;
                foreach ($request->file('screenshots') as $screenshot) {
                    $screenshotPath = $screenshot->store('ai-services/' . $service->id . '/screenshots', 'public');
                    AiServiceScreenshot::create([
                        'ai_service_id' => $service->id,
                        'url' => Storage::url($screenshotPath),
                        'order' => $order++,
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.ai_service_created_success'),
                    'redirect' => route('admin.ai-services.services.index')
                ]);
            }

            return redirect()->route('admin.ai-services.services.index')
                ->with('success', __('messages.ai_service_created_success'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.validation_error'),
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('AI Service Creation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: __('messages.error'),
                ], 500);
            }

            return redirect()->route('admin.ai-services.services.index')
                ->with('error', __('messages.error'));
        }
    }

    public function show(AiService $service)
    {
        $service->load(['category', 'images', 'galleryImages', 'features', 'screenshots']);
        return view('admin.ai-services.services.show', compact('service'));
    }

    public function edit(Request $request, AiService $service)
    {
        $categories = AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $service->load(['images', 'features', 'screenshots']);
        $view = view('admin.ai-services.services.edit-modal', compact('service', 'categories'));

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, AiService $service)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:ai_service_categories,id',
                'name' => 'required|string|max:255',
                'name_en' => 'nullable|string|max:255',
                'slug' => 'nullable|string|max:255|unique:ai_services,slug,' . $service->id,
                'short_description' => 'nullable|string|max:500',
                'short_description_en' => 'nullable|string|max:500',
                'description' => 'nullable|string',
                'description_en' => 'nullable|string',
                'full_description' => 'nullable|string',
                'full_description_en' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'original_price' => 'nullable|numeric|min:0',
                'currency' => 'nullable|string|max:3',
                'video_url' => 'nullable|url',
                'video_thumbnail' => 'nullable|url',
                'specifications' => 'nullable|array',
                'tags' => 'nullable|array',
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'gallery_images' => 'nullable|array',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
                'screenshots' => 'nullable|array',
                'screenshots.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
                'is_featured' => 'boolean',
                'is_active' => 'boolean',
                'is_free' => 'boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.validation_error'),
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['is_featured'] = $request->has('is_featured') ? true : false;
            $data['is_free'] = $request->has('is_free') ? true : false;

            if (empty($data['slug'])) {
                unset($data['slug']);
            }

            $service->update($data);

            // Handle deleted gallery images
            if ($request->filled('deleted_gallery_images')) {
                $deletedIds = $request->input('deleted_gallery_images');
                $imagesToDelete = $service->images()->where('type', 'gallery')->whereIn('id', $deletedIds)->get();
                foreach ($imagesToDelete as $image) {
                    $path = str_replace(Storage::url(''), '', $image->url);
                    if (empty($path) || $path === $image->url) {
                        $path = str_replace('/storage/', '', $image->url);
                    }
                    if (!empty($path)) {
                        Storage::disk('public')->delete($path);
                    }
                    $image->delete();
                }
            }

            // Handle deleted screenshots
            if ($request->filled('deleted_screenshots')) {
                $deletedIds = $request->input('deleted_screenshots');
                $screenshotsToDelete = $service->screenshots()->whereIn('id', $deletedIds)->get();
                foreach ($screenshotsToDelete as $screenshot) {
                    $path = str_replace(Storage::url(''), '', $screenshot->url);
                    if (empty($path) || $path === $screenshot->url) {
                        $path = str_replace('/storage/', '', $screenshot->url);
                    }
                    if (!empty($path)) {
                        Storage::disk('public')->delete($path);
                    }
                    $screenshot->delete();
                }
            }

            // Handle main image upload (if new image provided)
            if ($request->hasFile('main_image')) {
                // Delete old main image
                $oldMainImage = $service->images()->where('type', 'main')->first();
                if ($oldMainImage) {
                    $oldPath = str_replace(Storage::url(''), '', $oldMainImage->url);
                    if (empty($oldPath)) {
                        $oldPath = str_replace('/storage/', '', $oldMainImage->url);
                    }
                    if (!empty($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                    $oldMainImage->delete();
                }

                // Store new main image
                $mainImagePath = $request->file('main_image')->store('ai-services/' . $service->id, 'public');
                AiServiceImage::create([
                    'ai_service_id' => $service->id,
                    'url' => Storage::url($mainImagePath),
                    'type' => 'main',
                    'order' => 0,
                ]);
            }

            // Handle gallery images upload (add new ones)
            if ($request->hasFile('gallery_images')) {
                $maxOrder = $service->images()->where('type', 'gallery')->max('order') ?? 0;
                $order = $maxOrder + 1;
                foreach ($request->file('gallery_images') as $image) {
                    $galleryImagePath = $image->store('ai-services/' . $service->id . '/gallery', 'public');
                    AiServiceImage::create([
                        'ai_service_id' => $service->id,
                        'url' => Storage::url($galleryImagePath),
                        'type' => 'gallery',
                        'order' => $order++,
                    ]);
                }
            }

            // Handle screenshots upload (add new ones)
            if ($request->hasFile('screenshots')) {
                $maxOrder = $service->screenshots()->max('order') ?? 0;
                $order = $maxOrder + 1;
                foreach ($request->file('screenshots') as $screenshot) {
                    $screenshotPath = $screenshot->store('ai-services/' . $service->id . '/screenshots', 'public');
                    AiServiceScreenshot::create([
                        'ai_service_id' => $service->id,
                        'url' => Storage::url($screenshotPath),
                        'order' => $order++,
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.ai_service_updated_success'),
                    'redirect' => route('admin.ai-services.services.index')
                ]);
            }

            return redirect()->route('admin.ai-services.services.index')
                ->with('success', __('messages.ai_service_updated_success'));
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('AI Service Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: __('messages.error'),
                ], 500);
            }

            return redirect()->route('admin.ai-services.services.index')
                ->with('error', __('messages.error'));
        }
    }

    public function destroy(AiService $service)
    {
        $service->delete();

        return redirect()->route('admin.ai-services.services.index')
            ->with('success', __('messages.ai_service_deleted_success'));
    }
}
