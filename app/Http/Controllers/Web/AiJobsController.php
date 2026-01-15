<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AiJob;
use Illuminate\Http\Request;

class AiJobsController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        
        $query = AiJob::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });

        // Filter by job type
        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
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
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
                
                if ($locale === 'en') {
                    $q->orWhere('title_en', 'like', "%{$search}%")
                        ->orWhere('description_en', 'like', "%{$search}%")
                        ->orWhere('company_en', 'like', "%{$search}%")
                        ->orWhere('location_en', 'like', "%{$search}%");
                }
            });
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();

        return view('ai-jobs.index', compact('jobs'));
    }

    public function show($slug)
    {
        $locale = app()->getLocale();
        
        $job = AiJob::where('slug', $slug)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        // Get related jobs
        $relatedJobs = AiJob::where('is_active', true)
            ->where('id', '!=', $job->id)
            ->where(function($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->where('job_type', $job->job_type)
            ->latest()
            ->limit(3)
            ->get();

        return view('ai-jobs.show', compact('job', 'relatedJobs'));
    }
}
