<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionsSection;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionsSectionController extends Controller
{
    public function index()
    {
        $section = SubscriptionsSection::first();
        $subscriptions = Subscription::where('is_active', true)->orderBy('price', 'asc')->get();

        return view('admin.subscriptions-section.index', compact('section', 'subscriptions'));
    }

    public function create()
    {
        $section = null;
        $view = view('admin.subscriptions-section.section-modal', compact('section'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'background_color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
        ]);

        // Delete old section if exists
        $oldSection = SubscriptionsSection::first();
        if ($oldSection) {
            $oldSection->delete();
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['background_color'] = $request->background_color ?? '#02c0ce';

        SubscriptionsSection::create($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.subscriptions_section_created') ?? 'تم إنشاء قسم الباقات بنجاح',
            'redirect' => route('admin.customer-facing.subscriptions-section.index')
        ]);
    }

    public function edit(SubscriptionsSection $subscriptionsSection)
    {
        $section = $subscriptionsSection;
        $view = view('admin.subscriptions-section.section-modal', compact('section'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, SubscriptionsSection $subscriptionsSection)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_en' => 'nullable|string',
            'background_color' => 'nullable|string|max:7',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['background_color'] = $request->background_color ?? '#02c0ce';

        $subscriptionsSection->update($data);

        return response()->json([
            'success' => true,
            'message' => __('messages.subscriptions_section_updated') ?? 'تم تحديث قسم الباقات بنجاح',
            'redirect' => route('admin.customer-facing.subscriptions-section.index')
        ]);
    }

    public function destroy(SubscriptionsSection $subscriptionsSection)
    {
        $subscriptionsSection->delete();

        return redirect()->route('admin.customer-facing.subscriptions-section.index')
            ->with('success', __('messages.subscriptions_section_deleted') ?? 'تم حذف قسم الباقات بنجاح');
    }
}
