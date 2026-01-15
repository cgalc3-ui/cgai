<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiJob;
use Illuminate\Http\Request;

class AiJobsController extends Controller
{
    public function index(Request $request)
    {
        $query = AiJob::query();

        // Filter by job type
        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
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
                    ->orWhere('title_en', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $jobs = $query->latest()->paginate(10)->withQueryString();

        return view('admin.ai-jobs.index', compact('jobs'));
    }

    public function create(Request $request)
    {
        $view = view('admin.ai-jobs.create-modal');

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_jobs,slug',
            'description' => 'required|string',
            'description_en' => 'nullable|string',
            'company' => 'required|string|max:255',
            'company_en' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'job_type' => 'required|in:full_time,part_time,contract,freelance,internship',
            'requirements' => 'nullable|string',
            'requirements_en' => 'nullable|string',
            'benefits' => 'nullable|string',
            'benefits_en' => 'nullable|string',
            'application_email' => 'nullable|email',
            'application_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        $job = AiJob::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.ai_job_created_success') ?? 'تم إنشاء الوظيفة بنجاح',
                'redirect' => route('admin.ai-jobs.index')
            ]);
        }

        return redirect()->route('admin.ai-jobs.index')
            ->with('success', __('messages.ai_job_created_success') ?? 'تم إنشاء الوظيفة بنجاح');
    }

    public function show(AiJob $aiJob)
    {
        return view('admin.ai-jobs.show', compact('aiJob'));
    }

    public function edit(Request $request, AiJob $aiJob)
    {
        $view = view('admin.ai-jobs.edit-modal', compact('aiJob'));

        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, AiJob $aiJob)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_jobs,slug,' . $aiJob->id,
            'description' => 'required|string',
            'description_en' => 'nullable|string',
            'company' => 'required|string|max:255',
            'company_en' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:255',
            'job_type' => 'required|in:full_time,part_time,contract,freelance,internship',
            'requirements' => 'nullable|string',
            'requirements_en' => 'nullable|string',
            'benefits' => 'nullable|string',
            'benefits_en' => 'nullable|string',
            'application_email' => 'nullable|email',
            'application_url' => 'nullable|url',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'expires_at' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        $aiJob->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.ai_job_updated_success') ?? 'تم تحديث الوظيفة بنجاح',
                'redirect' => route('admin.ai-jobs.index')
            ]);
        }

        return redirect()->route('admin.ai-jobs.index')
            ->with('success', __('messages.ai_job_updated_success') ?? 'تم تحديث الوظيفة بنجاح');
    }

    public function destroy(AiJob $aiJob)
    {
        $aiJob->delete();

        return redirect()->route('admin.ai-jobs.index')
            ->with('success', __('messages.ai_job_deleted_success') ?? 'تم حذف الوظيفة بنجاح');
    }
}
