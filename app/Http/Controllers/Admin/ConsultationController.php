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

        $consultations = $query->latest()->paginate(15)->withQueryString();

        // Get categories for filter dropdown
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.consultations.index', compact('consultations', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.consultations.create', compact('categories'));
    }

    public function store(StoreConsultationRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        $consultation = Consultation::create($data);
        return redirect()->route('admin.consultations.index')
            ->with('success', 'تم إنشاء الاستشارة بنجاح');
    }

    public function edit(Consultation $consultation)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.consultations.edit', compact('consultation', 'categories'));
    }

    public function update(UpdateConsultationRequest $request, Consultation $consultation)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        $consultation->update($data);
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
