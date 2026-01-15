<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AiService;
use App\Models\AiServiceCategory;
use App\Models\AiServiceTag;
use Illuminate\Http\Request;

class AiTechnologiesController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        
        $query = AiService::with(['category', 'images'])
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag (technology)
        if ($request->filled('tag')) {
            $query->where('tags', 'like', "%\"{$request->tag}\"%");
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        // Filter by latest
        if ($request->filled('latest')) {
            $query->where('is_new', true);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $locale) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
                
                if ($locale === 'en') {
                    $q->orWhere('name_en', 'like', "%{$search}%")
                        ->orWhere('description_en', 'like', "%{$search}%");
                }
            });
        }

        // Order by
        $orderBy = $request->get('order_by', 'created_at');
        $orderDir = $request->get('order_dir', 'desc');
        $query->orderBy($orderBy, $orderDir);

        $technologies = $query->paginate(12)->withQueryString();

        // Get categories
        $categories = AiServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get popular tags (technologies)
        $tags = AiServiceTag::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Get latest technologies (selected by admin)
        $latestTechnologies = AiService::where('is_active', true)
            ->where('is_latest', true)
            ->with(['category', 'images'])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        // Get best technologies of the month (selected by admin)
        $bestTechnologies = AiService::where('is_active', true)
            ->where('is_best_of_month', true)
            ->with(['category', 'images'])
            ->orderBy('purchases_count', 'desc')
            ->orderBy('rating', 'desc')
            ->limit(3)
            ->get();

        return view('ai-technologies.index', compact('technologies', 'categories', 'tags', 'latestTechnologies', 'bestTechnologies'));
    }

    public function show($slug)
    {
        $locale = app()->getLocale();
        
        $technology = AiService::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'images', 'features', 'screenshots'])
            ->firstOrFail();

        // Increment views
        $technology->increment('views_count');

        // Get related technologies
        $relatedTechnologies = AiService::where('is_active', true)
            ->where('id', '!=', $technology->id)
            ->where(function($q) use ($technology) {
                if ($technology->category_id) {
                    $q->where('category_id', $technology->category_id);
                }
            })
            ->with(['category', 'images'])
            ->latest()
            ->limit(4)
            ->get();

        return view('ai-technologies.show', compact('technology', 'relatedTechnologies'));
    }
}
