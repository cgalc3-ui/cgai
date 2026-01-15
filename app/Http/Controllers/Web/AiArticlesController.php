<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AiArticle;
use App\Models\AiServiceCategory;
use Illuminate\Http\Request;

class AiArticlesController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        
        $query = AiArticle::with(['author', 'category'])
            ->published();

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $locale) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
                
                if ($locale === 'en') {
                    $q->orWhere('title_en', 'like', "%{$search}%")
                        ->orWhere('content_en', 'like', "%{$search}%")
                        ->orWhere('excerpt_en', 'like', "%{$search}%");
                }
            });
        }

        $articles = $query->latest('published_at')->latest()->paginate(12)->withQueryString();

        // Get categories
        $categories = AiServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('ai-articles.index', compact('articles', 'categories'));
    }

    public function show($slug)
    {
        $locale = app()->getLocale();
        
        $article = AiArticle::where('slug', $slug)
            ->published()
            ->with(['author', 'category'])
            ->firstOrFail();

        // Increment views
        $article->increment('views_count');

        // Get related articles
        $relatedArticles = AiArticle::published()
            ->where('id', '!=', $article->id)
            ->where(function($q) use ($article) {
                if ($article->category_id) {
                    $q->where('category_id', $article->category_id);
                }
            })
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('ai-articles.show', compact('article', 'relatedArticles'));
    }
}
