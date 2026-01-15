<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\AiVideo;
use Illuminate\Http\Request;

class AiVideosController extends Controller
{
    /**
     * Get all active AI videos
     */
    public function index(Request $request)
    {
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $query = AiVideo::with('category')
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $locale) {
                if ($locale === 'en') {
                    $q->where('title_en', 'like', "%{$search}%")
                        ->orWhere('description_en', 'like', "%{$search}%");
                } else {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                }
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $videos = $query->latest()->paginate($perPage);

        $formattedVideos = $videos->getCollection()->map(function ($video) use ($locale) {
            return $this->formatVideo($video, $locale);
        });

        return response()->json([
            'success' => true,
            'data' => $formattedVideos,
            'pagination' => [
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
                'per_page' => $videos->perPage(),
                'total' => $videos->total(),
            ],
        ]);
    }

    /**
     * Get single video by slug
     */
    public function show(Request $request, $slug)
    {
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $video = AiVideo::with('category')
            ->where('is_active', true)
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views count
        $video->increment('views_count');

        return response()->json([
            'success' => true,
            'data' => $this->formatVideo($video, $locale),
        ]);
    }

    /**
     * Format video data for API response
     */
    private function formatVideo($video, $locale)
    {
        return [
            'id' => $video->id,
            'title' => $locale === 'en' && $video->title_en 
                ? $video->title_en 
                : $video->title,
            'description' => $locale === 'en' && $video->description_en 
                ? $video->description_en 
                : ($video->description ?? ''),
            'video_url' => $video->video_url,
            'thumbnail' => $video->thumbnail ? asset($video->thumbnail) : null,
            'duration' => $video->duration ? (int) $video->duration : null,
            'duration_formatted' => $video->duration ? $this->formatDuration($video->duration) : null,
            'views_count' => (int) $video->views_count,
            'category' => $video->category ? [
                'id' => $video->category->id,
                'name' => $locale === 'en' && $video->category->name_en 
                    ? $video->category->name_en 
                    : $video->category->name,
            ] : null,
            'is_featured' => (bool) $video->is_featured,
            'slug' => $video->slug,
            'created_at' => $video->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Format duration in seconds to readable format
     */
    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%d:%02d', $minutes, $secs);
    }
}

