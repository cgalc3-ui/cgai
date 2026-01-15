<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiArticle;
use App\Models\AiServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AiArticlesController extends Controller
{
    public function index(Request $request)
    {
        $query = AiArticle::with(['author', 'category']);

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

        $articles = $query->latest('published_at')->latest()->paginate(10)->withQueryString();

        // Get categories for filter dropdown
        $categories = AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.ai-articles.index', compact('articles', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $view = view('admin.ai-articles.create-modal', compact('categories'));

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_articles,slug',
            'content' => 'required|string',
            'content_en' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'excerpt_en' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'category_id' => 'nullable|exists:ai_service_categories,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['author_id'] = auth()->id();
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ai-articles', 'public');
            $data['image'] = Storage::url($imagePath);
        }

        $article = AiArticle::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.ai_article_created_success') ?? 'تم إنشاء المقال بنجاح',
                'redirect' => route('admin.ai-articles.index')
            ]);
        }

        return redirect()->route('admin.ai-articles.index')
            ->with('success', __('messages.ai_article_created_success') ?? 'تم إنشاء المقال بنجاح');
    }

    public function show(AiArticle $aiArticle)
    {
        $aiArticle->load(['author', 'category']);
        return view('admin.ai-articles.show', compact('aiArticle'));
    }

    public function edit(Request $request, AiArticle $aiArticle)
    {
        $categories = AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $view = view('admin.ai-articles.edit-modal', compact('aiArticle', 'categories'));

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, AiArticle $aiArticle)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_articles,slug,' . $aiArticle->id,
            'content' => 'required|string',
            'content_en' => 'nullable|string',
            'excerpt' => 'nullable|string|max:500',
            'excerpt_en' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'category_id' => 'nullable|exists:ai_service_categories,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($aiArticle->image) {
                $oldImagePath = str_replace('/storage/', '', $aiArticle->image);
                Storage::disk('public')->delete($oldImagePath);
            }

            $imagePath = $request->file('image')->store('ai-articles', 'public');
            $data['image'] = Storage::url($imagePath);
        }

        $aiArticle->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.ai_article_updated_success') ?? 'تم تحديث المقال بنجاح',
                'redirect' => route('admin.ai-articles.index')
            ]);
        }

        return redirect()->route('admin.ai-articles.index')
            ->with('success', __('messages.ai_article_updated_success') ?? 'تم تحديث المقال بنجاح');
    }

    public function destroy(AiArticle $aiArticle)
    {
        // Delete image
        if ($aiArticle->image) {
            $imagePath = str_replace('/storage/', '', $aiArticle->image);
            Storage::disk('public')->delete($imagePath);
        }

        $aiArticle->delete();

        return redirect()->route('admin.ai-articles.index')
            ->with('success', __('messages.ai_article_deleted_success') ?? 'تم حذف المقال بنجاح');
    }
}
