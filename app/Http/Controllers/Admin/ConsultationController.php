<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationRequest;
use App\Http\Requests\UpdateConsultationRequest;
use App\Models\Consultation;
use App\Models\Category;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $query = Consultation::with('category');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $consultations = $query->latest()->paginate(10)->withQueryString();

        // Get categories for filter dropdown
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.consultations.index', compact('consultations', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $view = view('admin.consultations.create-modal', compact('categories'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function store(StoreConsultationRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        $consultation = Consultation::create($data);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.consultation_created_success'),
                'redirect' => route('admin.consultations.index')
            ]);
        }
        
        return redirect()->route('admin.consultations.index')
            ->with('success', 'تم إنشاء الاستشارة بنجاح');
    }

    public function edit(Request $request, Consultation $consultation)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $view = view('admin.consultations.edit-modal', compact('consultation', 'categories'));
        
        return response()->json([
            'html' => $view->render()
        ]);
    }

    public function update(UpdateConsultationRequest $request, Consultation $consultation)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        $consultation->update($data);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.consultation_updated_success'),
                'redirect' => route('admin.consultations.index')
            ]);
        }
        
        return redirect()->route('admin.consultations.index')
            ->with('success', 'تم تحديث الاستشارة بنجاح');
    }

    public function destroy(Consultation $consultation)
    {
        $consultation->delete();

        return redirect()->route('admin.consultations.index')
            ->with('success', 'تم حذف الاستشارة بنجاح');
    }
}
