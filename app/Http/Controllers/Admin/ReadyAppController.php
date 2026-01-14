<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReadyApp;
use App\Models\ReadyAppCategory;
use App\Models\ReadyAppImage;
use App\Models\ReadyAppScreenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReadyAppController extends Controller
{
    public function index(Request $request)
    {
        $query = ReadyApp::with('category');

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

        $apps = $query->latest()->paginate(10)->withQueryString();

        // Get categories for filter dropdown
        $categories = ReadyAppCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.ready-apps.index', compact('apps', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = ReadyAppCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $view = view('admin.ready-apps.create-modal', compact('categories'));

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:ready_app_categories,id',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ready_apps,slug',
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
            'is_popular' => 'boolean',
            'is_new' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['is_popular'] = $request->has('is_popular') ? true : false;
        $data['is_new'] = $request->has('is_new') ? true : false;
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['currency'] = $data['currency'] ?? 'SAR';

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        DB::beginTransaction();
        try {
            $app = ReadyApp::create($data);

            // Handle main image upload
            if ($request->hasFile('main_image')) {
                $mainImagePath = $request->file('main_image')->store('ready-apps/' . $app->id, 'public');
                ReadyAppImage::create([
                    'ready_app_id' => $app->id,
                    'url' => Storage::url($mainImagePath),
                    'type' => 'main',
                    'order' => 0,
                ]);
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                $order = 1;
                foreach ($request->file('gallery_images') as $image) {
                    $galleryImagePath = $image->store('ready-apps/' . $app->id . '/gallery', 'public');
                    ReadyAppImage::create([
                        'ready_app_id' => $app->id,
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
                    $screenshotPath = $screenshot->store('ready-apps/' . $app->id . '/screenshots', 'public');
                    ReadyAppScreenshot::create([
                        'ready_app_id' => $app->id,
                        'url' => Storage::url($screenshotPath),
                        'order' => $order++,
                    ]);
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('messages.ready_app_created_success'),
                    'redirect' => route('admin.ready-apps.apps.index')
                ]);
            }

            return redirect()->route('admin.ready-apps.apps.index')
                ->with('success', __('messages.ready_app_created_success'));
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.error'),
                ], 500);
            }

            return redirect()->route('admin.ready-apps.apps.index')
                ->with('error', __('messages.error'));
        }
    }

    public function show(ReadyApp $app)
    {
        $app->load(['category', 'images', 'features', 'screenshots']);
        return view('admin.ready-apps.show', compact('app'));
    }

    public function edit(Request $request, ReadyApp $app)
    {
        $categories = ReadyAppCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $app->load(['images', 'features', 'screenshots']);
        $view = view('admin.ready-apps.edit-modal', compact('app', 'categories'));

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, ReadyApp $app)
    {
        $request->validate([
            'category_id' => 'required|exists:ready_app_categories,id',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ready_apps,slug,' . $app->id,
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
            'is_popular' => 'boolean',
            'is_new' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['is_popular'] = $request->has('is_popular') ? true : false;
        $data['is_new'] = $request->has('is_new') ? true : false;
        $data['is_featured'] = $request->has('is_featured') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        $app->update($data);

        // Handle deleted gallery images
        if ($request->filled('deleted_gallery_images')) {
            $deletedIds = $request->input('deleted_gallery_images');
            $imagesToDelete = $app->images()->where('type', 'gallery')->whereIn('id', $deletedIds)->get();
            foreach ($imagesToDelete as $image) {
                // Extract path from URL (remove /storage/ prefix)
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
            $screenshotsToDelete = $app->screenshots()->whereIn('id', $deletedIds)->get();
            foreach ($screenshotsToDelete as $screenshot) {
                // Extract path from URL
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
            $oldMainImage = $app->images()->where('type', 'main')->first();
            if ($oldMainImage) {
                // Extract path from URL (remove /storage/ prefix)
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
            $mainImagePath = $request->file('main_image')->store('ready-apps/' . $app->id, 'public');
            ReadyAppImage::create([
                'ready_app_id' => $app->id,
                'url' => Storage::url($mainImagePath),
                'type' => 'main',
                'order' => 0,
            ]);
        }

        // Handle gallery images upload (add new ones)
        if ($request->hasFile('gallery_images')) {
            $maxOrder = $app->images()->where('type', 'gallery')->max('order') ?? 0;
            $order = $maxOrder + 1;
            foreach ($request->file('gallery_images') as $image) {
                $galleryImagePath = $image->store('ready-apps/' . $app->id . '/gallery', 'public');
                ReadyAppImage::create([
                    'ready_app_id' => $app->id,
                    'url' => Storage::url($galleryImagePath),
                    'type' => 'gallery',
                    'order' => $order++,
                ]);
            }
        }

        // Handle screenshots upload (add new ones)
        if ($request->hasFile('screenshots')) {
            $maxOrder = $app->screenshots()->max('order') ?? 0;
            $order = $maxOrder + 1;
            foreach ($request->file('screenshots') as $screenshot) {
                $screenshotPath = $screenshot->store('ready-apps/' . $app->id . '/screenshots', 'public');
                ReadyAppScreenshot::create([
                    'ready_app_id' => $app->id,
                    'url' => Storage::url($screenshotPath),
                    'order' => $order++,
                ]);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.ready_app_updated_success'),
                'redirect' => route('admin.ready-apps.apps.index')
            ]);
        }

        return redirect()->route('admin.ready-apps.apps.index')
            ->with('success', __('messages.ready_app_updated_success'));
    }

    public function destroy(ReadyApp $app)
    {
        $app->delete();

        return redirect()->route('admin.ready-apps.apps.index')
            ->with('success', __('messages.ready_app_deleted_success'));
    }
}
