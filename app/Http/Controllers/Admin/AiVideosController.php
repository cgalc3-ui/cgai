<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiVideo;
use App\Models\AiServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AiVideosController extends Controller
{
    public function index(Request $request)
    {
        $query = AiVideo::with('category');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%");
            });
        }

        $videos = $query->latest()->paginate(10)->withQueryString();

        // Get categories for filter dropdown
        $categories = AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.ai-videos.index', compact('videos', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $view = view('admin.ai-videos.create-modal', compact('categories'));

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_videos,slug',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'video_url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'duration' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:ai_service_categories,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('ai-videos', 'public');
            $data['thumbnail'] = Storage::url($thumbnailPath);
        }

        $video = AiVideo::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.ai_video_created_success') ?? 'تم إنشاء الفيديو بنجاح',
                'redirect' => route('admin.ai-videos.index')
            ]);
        }

        return redirect()->route('admin.ai-videos.index')
            ->with('success', __('messages.ai_video_created_success') ?? 'تم إنشاء الفيديو بنجاح');
    }

    public function show(AiVideo $aiVideo)
    {
        $aiVideo->load('category');
        return view('admin.ai-videos.show', compact('aiVideo'));
    }

    public function edit(Request $request, AiVideo $aiVideo)
    {
        $categories = AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $view = view('admin.ai-videos.edit-modal', compact('aiVideo', 'categories'));

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, AiVideo $aiVideo)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_videos,slug,' . $aiVideo->id,
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'video_url' => 'required|url',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'duration' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:ai_service_categories,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($aiVideo->thumbnail) {
                $oldThumbnailPath = str_replace('/storage/', '', $aiVideo->thumbnail);
                Storage::disk('public')->delete($oldThumbnailPath);
            }

            $thumbnailPath = $request->file('thumbnail')->store('ai-videos', 'public');
            $data['thumbnail'] = Storage::url($thumbnailPath);
        }

        $aiVideo->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.ai_video_updated_success') ?? 'تم تحديث الفيديو بنجاح',
                'redirect' => route('admin.ai-videos.index')
            ]);
        }

        return redirect()->route('admin.ai-videos.index')
            ->with('success', __('messages.ai_video_updated_success') ?? 'تم تحديث الفيديو بنجاح');
    }

    public function destroy(AiVideo $aiVideo)
    {
        // Delete thumbnail
        if ($aiVideo->thumbnail) {
            $thumbnailPath = str_replace('/storage/', '', $aiVideo->thumbnail);
            Storage::disk('public')->delete($thumbnailPath);
        }

        $aiVideo->delete();

        return redirect()->route('admin.ai-videos.index')
            ->with('success', __('messages.ai_video_deleted_success') ?? 'تم حذف الفيديو بنجاح');
    }
}
