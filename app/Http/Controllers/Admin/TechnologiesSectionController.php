<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechnologiesSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TechnologiesSectionController extends Controller
{
    public function index()
    {
        $section = TechnologiesSection::first();

        return view('admin.technologies-section.index', compact('section'));
    }

    public function create()
    {
        $section = null;
        $view = view('admin.technologies-section.section-modal', compact('section'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'heading_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp',
            'buttons' => 'nullable|array',
            'buttons.*.title' => 'required_with:buttons|string|max:255',
            'buttons.*.title_en' => 'nullable|string|max:255',
            'buttons.*.link' => 'required_with:buttons|string|max:255',
            'buttons.*.target' => 'nullable|string|in:_self,_blank',
            'buttons.*.style' => 'nullable|string|in:primary,secondary',
        ]);

        // Delete old section if exists
        $oldSection = TechnologiesSection::first();
        if ($oldSection) {
            if ($oldSection->background_image) {
                Storage::disk('public')->delete($oldSection->background_image);
            }
            $oldSection->delete();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('background_image')) {
            $data['background_image'] = $request->file('background_image')->store('technologies-section', 'public');
        }

        TechnologiesSection::create($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.technologies_section_created') ?? 'تم إنشاء قسم التقنيات بنجاح',
            'redirect' => route('admin.customer-facing.technologies-section.index')
        ]);
    }

    public function edit(TechnologiesSection $technologiesSection)
    {
        $section = $technologiesSection;
        $view = view('admin.technologies-section.section-modal', compact('section'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, TechnologiesSection $technologiesSection)
    {
        $request->validate([
            'heading' => 'required|string|max:255',
            'heading_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'background_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp',
            'buttons' => 'nullable|array',
            'buttons.*.title' => 'required_with:buttons|string|max:255',
            'buttons.*.title_en' => 'nullable|string|max:255',
            'buttons.*.link' => 'required_with:buttons|string|max:255',
            'buttons.*.target' => 'nullable|string|in:_self,_blank',
            'buttons.*.style' => 'nullable|string|in:primary,secondary',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('background_image')) {
            // Delete old image
            if ($technologiesSection->background_image) {
                Storage::disk('public')->delete($technologiesSection->background_image);
            }
            $data['background_image'] = $request->file('background_image')->store('technologies-section', 'public');
        } else {
            // Keep existing image
            unset($data['background_image']);
        }

        $technologiesSection->update($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.technologies_section_updated') ?? 'تم تحديث قسم التقنيات بنجاح',
            'redirect' => route('admin.customer-facing.technologies-section.index')
        ]);
    }

    public function destroy(TechnologiesSection $technologiesSection)
    {
        if ($technologiesSection->background_image) {
            Storage::disk('public')->delete($technologiesSection->background_image);
        }

        $technologiesSection->delete();

        return redirect()->route('admin.customer-facing.technologies-section.index')
            ->with('success', __('messages.technologies_section_deleted') ?? 'تم حذف قسم التقنيات بنجاح');
    }
}

