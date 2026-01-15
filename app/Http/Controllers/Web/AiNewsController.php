<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AiNews;
use App\Models\AiServiceCategory;
use Illuminate\Http\Request;

class AiNewsController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        
        $query = AiNews::with(['author', 'category'])
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
                    ->orWhere('content', 'like', "%{$search}%");
                
                if ($locale === 'en') {
                    $q->orWhere('title_en', 'like', "%{$search}%")
                        ->orWhere('content_en', 'like', "%{$search}%");
                }
            });
        }

        $news = $query->latest('published_at')->latest()->paginate(12)->withQueryString();

        // Get categories
        $categories = AiServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get featured news
        $featuredNews = AiNews::published()
            ->where('is_featured', true)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('ai-news.index', compact('news', 'categories', 'featuredNews'));
    }

    public function show($slug)
    {
        $locale = app()->getLocale();
        
        $news = AiNews::where('slug', $slug)
            ->published()
            ->with(['author', 'category'])
            ->firstOrFail();

        // Increment views
        $news->increment('views_count');

        // Get related news
        $relatedNews = AiNews::published()
            ->where('id', '!=', $news->id)
            ->where(function($q) use ($news) {
                if ($news->category_id) {
                    $q->where('category_id', $news->category_id);
                }
            })
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('ai-news.show', compact('news', 'relatedNews'));
    }
}
