<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $query = Faq::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by question or answer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('question_en', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%")
                  ->orWhere('answer_en', 'like', "%{$search}%");
            });
        }

        $faqs = $query->orderBy('sort_order')->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        // Get unique categories for filter dropdown
        $categories = Faq::select('category')->distinct()->orderBy('category')->pluck('category');
        
        return view('admin.faqs.index', compact('faqs', 'categories'));
    }

    public function create(Request $request)
    {
        $view = view('admin.faqs.create-modal');
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|string',
            'role' => 'required|string|in:customer,staff,admin',
            'sort_order' => 'integer',
        ]);

        Faq::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.faq_added_success'),
                'redirect' => route('admin.faqs.index')
            ]);
        }

        return redirect()->route('admin.faqs.index')->with('success', __('messages.faq_added_success'));
    }

    public function edit(Request $request, Faq $faq)
    {
        $view = view('admin.faqs.edit-modal', compact('faq'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|string',
            'role' => 'required|string|in:customer,staff,admin',
            'sort_order' => 'integer',
        ]);

        $faq->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.faq_updated_success'),
                'redirect' => route('admin.faqs.index')
            ]);
        }

        return redirect()->route('admin.faqs.index')->with('success', __('messages.faq_updated_success'));
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', __('messages.faq_deleted_success'));
    }
}
