<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiServiceTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AiServiceTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = AiServiceTag::ordered()->get();
        return view('admin.ai-services.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tag = null;
        $services = \App\Models\AiService::where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.ai-services.tags.form', compact('tag', 'services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:ai_services,id',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_service_tags,slug',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp|max:2048',
            'is_active' => 'boolean',
        ]);

        // Get service to use its name if not provided
        $service = \App\Models\AiService::findOrFail($request->service_id);
        
        $data = $request->only(['name', 'name_en', 'slug']);
        
        // If name is not provided, use service name
        if (empty($data['name'])) {
            $data['name'] = $service->name;
        }
        if (empty($data['name_en']) && $service->name_en) {
            $data['name_en'] = $service->name_en;
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = Storage::url($request->file('image')->store('ai-service-tags', 'public'));
        }
        
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['sort_order'] = 0;

        AiServiceTag::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.ai_service_tag_created_success'),
                'redirect' => route('admin.ai-services.tags.index')
            ]);
        }

        return redirect()->route('admin.ai-services.tags.index')
            ->with('success', __('messages.ai_service_tag_created_success'));
    }

    /**
     * Display the specified resource.
     */
    public function show(AiServiceTag $tag)
    {
        return view('admin.ai-services.tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AiServiceTag $tag)
    {
        $services = \App\Models\AiService::where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('admin.ai-services.tags.form', compact('tag', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AiServiceTag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:ai_service_tags,slug,' . $tag->id,
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'name_en', 'slug']);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($tag->image) {
                $oldPath = $tag->image;
                if (strpos($oldPath, '/storage/') === 0) {
                    $oldPath = str_replace('/storage/', '', $oldPath);
                } else if (strpos($oldPath, 'storage/') === 0) {
                    $oldPath = str_replace('storage/', '', $oldPath);
                }
                Storage::disk('public')->delete($oldPath);
            }
            // Store new image
            $data['image'] = Storage::url($request->file('image')->store('ai-service-tags', 'public'));
        }
        
        $data['is_active'] = $request->has('is_active') ? true : false;

        $tag->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.ai_service_tag_updated_success'),
                'redirect' => route('admin.ai-services.tags.index')
            ]);
        }

        return redirect()->route('admin.ai-services.tags.index')
            ->with('success', __('messages.ai_service_tag_updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AiServiceTag $tag)
    {
        $tag->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.ai_service_tag_deleted_success')
            ]);
        }

        return redirect()->route('admin.ai-services.tags.index')
            ->with('success', __('messages.ai_service_tag_deleted_success'));
    }
}

