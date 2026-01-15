<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\AiJob;
use Illuminate\Http\Request;

class AiJobsController extends Controller
{
    /**
     * Get all active AI jobs
     */
    public function index(Request $request)
    {
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $query = AiJob::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });

        // Filter by job type
        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $locale) {
                if ($locale === 'en') {
                    $q->where('title_en', 'like', "%{$search}%")
                        ->orWhere('description_en', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                } else {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                }
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $jobs = $query->latest()->paginate($perPage);

        $formattedJobs = $jobs->getCollection()->map(function ($job) use ($locale) {
            return $this->formatJob($job, $locale);
        });

        return response()->json([
            'success' => true,
            'data' => $formattedJobs,
            'pagination' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
            ],
        ]);
    }

    /**
     * Get single job by slug
     */
    public function show(Request $request, $slug)
    {
        $locale = $request->get('locale', app()->getLocale());
        app()->setLocale($locale);

        $job = AiJob::where('is_active', true)
            ->where('slug', $slug)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatJob($job, $locale),
        ]);
    }

    /**
     * Format job data for API response
     */
    private function formatJob($job, $locale)
    {
        return [
            'id' => $job->id,
            'title' => $locale === 'en' && $job->title_en 
                ? $job->title_en 
                : $job->title,
            'description' => $locale === 'en' && $job->description_en 
                ? $job->description_en 
                : ($job->description ?? ''),
            'company' => $job->company,
            'location' => $job->location,
            'salary_range' => $job->salary_range,
            'job_type' => $job->job_type,
            'job_type_label' => $this->getJobTypeLabel($job->job_type, $locale),
            'requirements' => $this->formatRequirements($job, $locale),
            'expires_at' => $job->expires_at ? $job->expires_at->format('Y-m-d H:i:s') : null,
            'slug' => $job->slug,
            'created_at' => $job->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get job type label based on locale
     */
    private function getJobTypeLabel($jobType, $locale)
    {
        $labels = [
            'ar' => [
                'full_time' => 'دوام كامل',
                'part_time' => 'دوام جزئي',
                'contract' => 'عقد',
                'freelance' => 'عمل حر',
                'internship' => 'تدريب',
            ],
            'en' => [
                'full_time' => 'Full-time',
                'part_time' => 'Part-time',
                'contract' => 'Contract',
                'freelance' => 'Freelance',
                'internship' => 'Internship',
            ],
        ];

        return $labels[$locale][$jobType] ?? $jobType;
    }

    /**
     * Format requirements based on locale
     */
    private function formatRequirements($job, $locale)
    {
        $requirements = $locale === 'en' && !empty($job->requirements_en)
            ? $job->requirements_en
            : ($job->requirements ?? []);

        // Fallback to the other language if the preferred one is empty
        if (empty($requirements) && $locale === 'en' && !empty($job->requirements)) {
            $requirements = $job->requirements;
        } elseif (empty($requirements) && $locale === 'ar' && !empty($job->requirements_en)) {
            $requirements = $job->requirements_en;
        }

        return is_array($requirements) ? $requirements : [];
    }
}

