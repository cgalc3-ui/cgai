<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AiVideo;
use App\Models\AiServiceCategory;
use Illuminate\Http\Request;

class AiVideosController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        
        $query = AiVideo::with('category')
            ->where('is_active', true);

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
                    ->orWhere('description', 'like', "%{$search}%");
                
                if ($locale === 'en') {
                    $q->orWhere('title_en', 'like', "%{$search}%")
                        ->orWhere('description_en', 'like', "%{$search}%");
                }
            });
        }

        $videos = $query->latest()->paginate(12)->withQueryString();

        // Get categories
        $categories = AiServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('ai-videos.index', compact('videos', 'categories'));
    }

    public function show($slug)
    {
        $locale = app()->getLocale();
        
        $video = AiVideo::where('slug', $slug)
            ->where('is_active', true)
            ->with('category')
            ->firstOrFail();

        // Increment views
        $video->increment('views_count');

        // Get related videos
        $relatedVideos = AiVideo::where('is_active', true)
            ->where('id', '!=', $video->id)
            ->where(function($q) use ($video) {
                if ($video->category_id) {
                    $q->where('category_id', $video->category_id);
                }
            })
            ->latest()
            ->limit(4)
            ->get();

        return view('ai-videos.show', compact('video', 'relatedVideos'));
    }
}
