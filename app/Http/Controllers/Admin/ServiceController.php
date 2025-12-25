<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\SubCategory;
use App\Models\Specialization;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with(['subCategory.category', 'specialization'])->latest()->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $subCategories = SubCategory::where('is_active', true)->with('category')->orderBy('name')->get();
        $specializations = Specialization::orderBy('name')->get();
        return view('admin.services.create', compact('subCategories', 'specializations'));
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        if (empty($data['slug'])) {
            unset($data['slug']);
        }
        
        Service::create($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'تم إنشاء الخدمة بنجاح');
    }

    public function edit(Service $service)
    {
        $subCategories = SubCategory::where('is_active', true)->with('category')->orderBy('name')->get();
        $specializations = Specialization::orderBy('name')->get();
        return view('admin.services.edit', compact('service', 'subCategories', 'specializations'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        if (empty($data['slug'])) {
            unset($data['slug']);
        }
        
        $service->update($data);

        return redirect()->route('admin.services.index')
            ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'تم حذف الخدمة بنجاح');
    }
}
