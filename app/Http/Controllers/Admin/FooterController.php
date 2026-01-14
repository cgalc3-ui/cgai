<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FooterController extends Controller
{
    public function index()
    {
        $footer = Footer::first();

        return view('admin.footer.index', compact('footer'));
    }

    public function create()
    {
        $footer = null;
        $view = view('admin.footer.footer-modal', compact('footer'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp',
            'logo_en' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'working_hours' => 'nullable|string|max:255',
            'working_hours_en' => 'nullable|string|max:255',
            'quick_links' => 'nullable|array',
            'quick_links.*.title' => 'required_with:quick_links|string|max:255',
            'quick_links.*.title_en' => 'nullable|string|max:255',
            'quick_links.*.link' => 'required_with:quick_links|string|max:255',
            'content_links' => 'nullable|array',
            'content_links.*.title' => 'required_with:content_links|string|max:255',
            'content_links.*.title_en' => 'nullable|string|max:255',
            'content_links.*.link' => 'required_with:content_links|string|max:255',
            'support_links' => 'nullable|array',
            'support_links.*.title' => 'required_with:support_links|string|max:255',
            'support_links.*.title_en' => 'nullable|string|max:255',
            'support_links.*.link' => 'required_with:support_links|string|max:255',
            'social_media' => 'nullable|array',
            'social_media.*.platform' => 'required_with:social_media|string|max:255',
            'social_media.*.url' => 'required_with:social_media|string|max:255',
            'social_media.*.icon' => 'nullable|string|max:255',
            'copyright_text' => 'nullable|string|max:255',
            'copyright_text_en' => 'nullable|string|max:255',
        ]);

        // Delete old footer if exists
        $oldFooter = Footer::first();
        if ($oldFooter) {
            if ($oldFooter->logo) {
                Storage::disk('public')->delete($oldFooter->logo);
            }
            if ($oldFooter->logo_en) {
                Storage::disk('public')->delete($oldFooter->logo_en);
            }
            $oldFooter->delete();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('footer', 'public');
        }

        if ($request->hasFile('logo_en')) {
            $data['logo_en'] = $request->file('logo_en')->store('footer', 'public');
        }

        try {
            Footer::create($data);

            return response()->json([
                'success' => true,
                'message' => __('messages.footer_created') ?? 'تم إنشاء الفوتر بنجاح',
                'redirect' => route('admin.customer-facing.footer.index')
            ]);
        } catch (\Exception $e) {
            \Log::error('Footer Creation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred') ?? 'حدث خطأ أثناء الحفظ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Footer $footer)
    {
        $view = view('admin.footer.footer-modal', compact('footer'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, Footer $footer)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp',
            'logo_en' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg,bmp',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'working_hours' => 'nullable|string|max:255',
            'working_hours_en' => 'nullable|string|max:255',
            'quick_links' => 'nullable|array',
            'quick_links.*.title' => 'required_with:quick_links|string|max:255',
            'quick_links.*.title_en' => 'nullable|string|max:255',
            'quick_links.*.link' => 'required_with:quick_links|string|max:255',
            'content_links' => 'nullable|array',
            'content_links.*.title' => 'required_with:content_links|string|max:255',
            'content_links.*.title_en' => 'nullable|string|max:255',
            'content_links.*.link' => 'required_with:content_links|string|max:255',
            'support_links' => 'nullable|array',
            'support_links.*.title' => 'required_with:support_links|string|max:255',
            'support_links.*.title_en' => 'nullable|string|max:255',
            'support_links.*.link' => 'required_with:support_links|string|max:255',
            'social_media' => 'nullable|array',
            'social_media.*.platform' => 'required_with:social_media|string|max:255',
            'social_media.*.url' => 'required_with:social_media|string|max:255',
            'social_media.*.icon' => 'nullable|string|max:255',
            'copyright_text' => 'nullable|string|max:255',
            'copyright_text_en' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('logo')) {
            // Delete old image
            if ($footer->logo) {
                Storage::disk('public')->delete($footer->logo);
            }
            $data['logo'] = $request->file('logo')->store('footer', 'public');
        } else {
            // Keep existing image
            unset($data['logo']);
        }

        if ($request->hasFile('logo_en')) {
            // Delete old image
            if ($footer->logo_en) {
                Storage::disk('public')->delete($footer->logo_en);
            }
            $data['logo_en'] = $request->file('logo_en')->store('footer', 'public');
        } else {
            // Keep existing image
            unset($data['logo_en']);
        }

        $footer->update($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.footer_updated') ?? 'تم تحديث الفوتر بنجاح',
            'redirect' => route('admin.customer-facing.footer.index')
        ]);
    }

    public function destroy(Footer $footer)
    {
        if ($footer->logo) {
            Storage::disk('public')->delete($footer->logo);
        }
        if ($footer->logo_en) {
            Storage::disk('public')->delete($footer->logo_en);
        }

        $footer->delete();

        return redirect()->route('admin.customer-facing.footer.index')
            ->with('success', __('messages.footer_deleted') ?? 'تم حذف الفوتر بنجاح');
    }
}


