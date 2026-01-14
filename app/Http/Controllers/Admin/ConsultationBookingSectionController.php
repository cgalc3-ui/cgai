<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsultationBookingSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConsultationBookingSectionController extends Controller
{
    public function index()
    {
        $section = ConsultationBookingSection::first();

        return view('admin.consultation-booking-section.index', compact('section'));
    }

    public function create()
    {
        $section = null;
        $view = view('admin.consultation-booking-section.section-modal', compact('section'));
        
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
        $oldSection = ConsultationBookingSection::first();
        if ($oldSection) {
            if ($oldSection->background_image) {
                Storage::disk('public')->delete($oldSection->background_image);
            }
            $oldSection->delete();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('background_image')) {
            $data['background_image'] = $request->file('background_image')->store('consultation-booking', 'public');
        }

        ConsultationBookingSection::create($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.consultation_booking_section_created') ?? 'تم إنشاء قسم حجز الاستشارة بنجاح',
            'redirect' => route('admin.customer-facing.consultation-booking-section.index')
        ]);
    }

    public function edit(ConsultationBookingSection $consultationBookingSection)
    {
        $section = $consultationBookingSection;
        $view = view('admin.consultation-booking-section.section-modal', compact('section'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, ConsultationBookingSection $consultationBookingSection)
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
            if ($consultationBookingSection->background_image) {
                Storage::disk('public')->delete($consultationBookingSection->background_image);
            }
            $data['background_image'] = $request->file('background_image')->store('consultation-booking', 'public');
        } else {
            // Keep existing image
            unset($data['background_image']);
        }

        $consultationBookingSection->update($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.consultation_booking_section_updated') ?? 'تم تحديث قسم حجز الاستشارة بنجاح',
            'redirect' => route('admin.customer-facing.consultation-booking-section.index')
        ]);
    }

    public function destroy(ConsultationBookingSection $consultationBookingSection)
    {
        if ($consultationBookingSection->background_image) {
            Storage::disk('public')->delete($consultationBookingSection->background_image);
        }

        $consultationBookingSection->delete();

        return redirect()->route('admin.customer-facing.consultation-booking-section.index')
            ->with('success', __('messages.consultation_booking_section_deleted') ?? 'تم حذف قسم حجز الاستشارة بنجاح');
    }
}

