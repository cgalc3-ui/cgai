<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanyLogo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyLogoController extends Controller
{
    public function index()
    {
        $companyLogo = CompanyLogo::first();

        return view('admin.company-logo.index', compact('companyLogo'));
    }

    public function create()
    {
        $companyLogo = null;
        $view = view('admin.company-logo.company-logo-modal', compact('companyLogo'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        // Validate only non-file fields first
        $validated = $request->validate([
            'heading' => 'required|string|max:255',
            'heading_en' => 'nullable|string|max:255',
            'logos' => 'nullable|array',
            'logos.*.link' => 'nullable|string|max:255',
            'logos.*.name' => 'nullable|string|max:255',
        ], [
            'heading.required' => __('messages.heading_required'),
        ]);

        // Delete old company logo if exists
        $oldCompanyLogo = CompanyLogo::first();
        if ($oldCompanyLogo) {
            // Delete old logo images
            if ($oldCompanyLogo->logos) {
                foreach ($oldCompanyLogo->logos as $logo) {
                    if (isset($logo['image']) && $logo['image']) {
                        Storage::disk('public')->delete($logo['image']);
                    }
                }
            }
            $oldCompanyLogo->delete();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->get('sort_order', 0);

        // Handle logo images upload
        $logos = [];
        if ($request->has('logos') && is_array($request->logos)) {
            foreach ($request->logos as $index => $logo) {
                $logoData = [
                    'link' => $logo['link'] ?? null,
                    'name' => $logo['name'] ?? null,
                ];

                // Check if new image is uploaded
                if ($request->hasFile("logos.{$index}.image")) {
                    $logoData['image'] = $request->file("logos.{$index}.image")->store('company-logos', 'public');
                } elseif (isset($logo['image']) && is_string($logo['image'])) {
                    // Keep existing image
                    $logoData['image'] = $logo['image'];
                }

                $logos[] = $logoData;
            }
        }
        $data['logos'] = $logos;

        try {
            CompanyLogo::create($data);

            return response()->json([
                'success' => true,
                'message' => __('messages.company_logo_created'),
                'redirect' => route('admin.customer-facing.company-logo.index')
            ]);
        } catch (\Exception $e) {
            \Log::error('Company Logo Creation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => __('messages.error_occurred') ?? 'حدث خطأ أثناء الحفظ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(CompanyLogo $companyLogo)
    {
        $view = view('admin.company-logo.company-logo-modal', compact('companyLogo'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, CompanyLogo $companyLogo)
    {
        // Validate only non-file fields
        $request->validate([
            'heading' => 'required|string|max:255',
            'heading_en' => 'nullable|string|max:255',
            'logos' => 'nullable|array',
            'logos.*.link' => 'nullable|string|max:255',
            'logos.*.name' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->get('sort_order', $companyLogo->sort_order);

        // Handle logo images upload
        $logos = [];
        $oldLogos = $companyLogo->logos ?? [];
        
        if ($request->has('logos') && is_array($request->logos)) {
            foreach ($request->logos as $index => $logo) {
                $logoData = [
                    'link' => $logo['link'] ?? null,
                    'name' => $logo['name'] ?? null,
                ];

                // Check if new image is uploaded
                if ($request->hasFile("logos.{$index}.image")) {
                    // Delete old image if exists
                    if (isset($oldLogos[$index]['image']) && $oldLogos[$index]['image']) {
                        Storage::disk('public')->delete($oldLogos[$index]['image']);
                    }
                    $logoData['image'] = $request->file("logos.{$index}.image")->store('company-logos', 'public');
                } elseif ($request->hasFile("logos.{$index}.image_new")) {
                    // Handle image_new field (for editing existing logos)
                    // Delete old image if exists
                    if (isset($oldLogos[$index]['image']) && $oldLogos[$index]['image']) {
                        Storage::disk('public')->delete($oldLogos[$index]['image']);
                    }
                    $logoData['image'] = $request->file("logos.{$index}.image_new")->store('company-logos', 'public');
                } elseif (isset($logo['image']) && is_string($logo['image'])) {
                    // Keep existing image
                    $logoData['image'] = $logo['image'];
                } elseif (isset($oldLogos[$index]['image'])) {
                    // Keep old image
                    $logoData['image'] = $oldLogos[$index]['image'];
                }

                $logos[] = $logoData;
            }
        }
        $data['logos'] = $logos;

        $companyLogo->update($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.company_logo_updated'),
            'redirect' => route('admin.customer-facing.company-logo.index')
        ]);
    }

    public function destroy(CompanyLogo $companyLogo)
    {
        // Delete logo images
        if ($companyLogo->logos) {
            foreach ($companyLogo->logos as $logo) {
                if (isset($logo['image']) && $logo['image']) {
                    Storage::disk('public')->delete($logo['image']);
                }
            }
        }

        $companyLogo->delete();

        return redirect()->route('admin.customer-facing.company-logo.index')
            ->with('success', __('messages.company_logo_deleted'));
    }
}
