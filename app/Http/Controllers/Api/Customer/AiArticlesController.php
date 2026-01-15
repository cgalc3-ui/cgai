<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\AiArticle;
use Illuminate\Http\Request;

class AiArticlesController extends Controller
{
    /**
     * Get all active AI articles
     */
    public function index(Request $request)
    {
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $query = AiArticle::with(['author', 'category'])
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by featured
        if ($request->filled('featured') && $request->featured === 'true') {
            $query->where('is_featured', true);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $locale) {
                if ($locale === 'en') {
                    $q->where('title_en', 'like', "%{$search}%")
                        ->orWhere('excerpt_en', 'like', "%{$search}%");
                } else {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                }
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $articles = $query->latest('published_at')->latest()->paginate($perPage);

        $formattedArticles = $articles->getCollection()->map(function ($article) use ($locale) {
            return $this->formatArticle($article, $locale, true); // true = summary mode
        });

        return response()->json([
            'success' => true,
            'data' => $formattedArticles,
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
            ],
        ]);
    }

    /**
     * Get single article by slug
     */
    public function show(Request $request, $slug)
    {
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $article = AiArticle::with(['author', 'category'])
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views count
        $article->increment('views_count');

        return response()->json([
            'success' => true,
            'data' => $this->formatArticle($article, $locale, false), // false = full content
        ]);
    }

    /**
     * Format article data for API response
     */
    private function formatArticle($article, $locale, $summary = false)
    {
        $data = [
            'id' => $article->id,
            'title' => $locale === 'en' && $article->title_en 
                ? $article->title_en 
                : $article->title,
            'excerpt' => $locale === 'en' && $article->excerpt_en 
                ? $article->excerpt_en 
                : ($article->excerpt ?? ''),
            'image' => $article->image ? asset($article->image) : null,
            'views_count' => (int) $article->views_count,
            'category' => $article->category ? [
                'id' => $article->category->id,
                'name' => $locale === 'en' && $article->category->name_en 
                    ? $article->category->name_en 
                    : $article->category->name,
            ] : null,
            'author' => $article->author ? [
                'id' => $article->author->id,
                'name' => $article->author->name,
            ] : null,
            'is_featured' => (bool) $article->is_featured,
            'slug' => $article->slug,
            'published_at' => $article->published_at ? $article->published_at->format('Y-m-d H:i:s') : null,
            'created_at' => $article->created_at->format('Y-m-d H:i:s'),
        ];

        // Include full content only in detail view
        if (!$summary) {
            $data['content'] = $locale === 'en' && $article->content_en 
                ? $article->content_en 
                : ($article->content ?? '');
        }

        return $data;
    }
}

