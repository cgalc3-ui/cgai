<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiService;
use App\Models\AiServiceCategory;
use Illuminate\Http\Request;

class AiNewsController extends Controller
{
    public function index(Request $request)
    {
        // Get latest technologies (selected by admin)
        $latestQuery = AiService::with(['category', 'images'])
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('latest_category_id')) {
            $latestQuery->where('category_id', $request->latest_category_id);
        }

        // Search
        if ($request->filled('latest_search')) {
            $search = $request->latest_search;
            $latestQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $latestTechnologies = $latestQuery->where('is_latest', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'latest_page')
            ->withQueryString();

        // Get best technologies of the month (selected by admin)
        $bestQuery = AiService::with(['category', 'images'])
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('best_category_id')) {
            $bestQuery->where('category_id', $request->best_category_id);
        }

        // Search
        if ($request->filled('best_search')) {
            $search = $request->best_search;
            $bestQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $bestTechnologies = $bestQuery->where('is_best_of_month', true)
            ->orderBy('purchases_count', 'desc')
            ->orderBy('rating', 'desc')
            ->paginate(10, ['*'], 'best_page')
            ->withQueryString();

        // Get all active services for selection
        $allServices = AiService::where('is_active', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        // Get categories for filter dropdown
        $categories = AiServiceCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.ai-news.index', compact('latestTechnologies', 'bestTechnologies', 'allServices', 'categories'));
    }

    /**
     * Remove service from latest technologies section
     */
    public function removeFromLatest(AiService $service)
    {
        $service->update(['is_latest' => false]);

        return redirect()->route('admin.ai-news.index')
            ->with('success', __('messages.removed_from_latest_success') ?? 'تم إزالة الخدمة من قسم أحدث التقنيات بنجاح');
    }

    /**
     * Remove service from best technologies of the month section
     */
    public function removeFromBest(AiService $service)
    {
        $service->update(['is_best_of_month' => false]);

        return redirect()->route('admin.ai-news.index')
            ->with('success', __('messages.removed_from_best_success') ?? 'تم إزالة الخدمة من قسم أفضل التقنيات خلال الشهر بنجاح');
    }
}
