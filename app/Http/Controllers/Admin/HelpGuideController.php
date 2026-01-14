<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpGuide;
use Illuminate\Http\Request;

class HelpGuideController extends Controller
{
    /**
     * Display a listing of help guides
     */
    public function index()
    {
        $helpGuides = HelpGuide::ordered()->paginate(10);
        return view('admin.help-guides.index', compact('helpGuides'));
    }

    /**
     * Show the form for creating a new help guide
     */
    public function create(Request $request)
    {
        $view = view('admin.help-guides.create-modal');
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    /**
     * Store a newly created help guide
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,staff,customer',
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_en' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['sort_order'] = $request->sort_order ?? 0;

        HelpGuide::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.help_guide_created_success'),
                'redirect' => route('admin.help-guides.index')
            ]);
        }

        return redirect()->route('admin.help-guides.index')
            ->with('success', __('messages.help_guide_created_success'));
    }

    /**
     * Show the form for editing the specified help guide
     */
    public function edit(Request $request, HelpGuide $helpGuide)
    {
        $view = view('admin.help-guides.edit-modal', compact('helpGuide'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    /**
     * Update the specified help guide
     */
    public function update(Request $request, HelpGuide $helpGuide)
    {
        $request->validate([
            'role' => 'required|in:admin,staff,customer',
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_en' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;
        $data['sort_order'] = $request->sort_order ?? 0;

        $helpGuide->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.help_guide_updated_success'),
                'redirect' => route('admin.help-guides.index')
            ]);
        }

        return redirect()->route('admin.help-guides.index')
            ->with('success', __('messages.help_guide_updated_success'));
    }

    /**
     * Remove the specified help guide
     */
    public function destroy(HelpGuide $helpGuide)
    {
        $helpGuide->delete();

        return redirect()->route('admin.help-guides.index')
            ->with('success', __('messages.help_guide_deleted_success'));
    }
}
