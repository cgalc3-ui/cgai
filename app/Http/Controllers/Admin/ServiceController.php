<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with(['subCategory.category'])->latest()->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function create(Request $request)
    {
        $subCategories = SubCategory::where('is_active', true)->with('category')->orderBy('name')->get();
        $view = view('admin.services.create-modal', compact('subCategories'));
        
        if ($request->ajax()) {
            return response()->json([
                'html' => $view->render()
            ]);
        }
        
        return view('admin.services.create', compact('subCategories'));
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        $service = Service::create($data);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.service_created'),
                'redirect' => route('admin.services.index')
            ]);
        }
        
        return redirect()->route('admin.services.index')
            ->with('success', __('messages.service_created'));
    }

    public function edit(Request $request, Service $service)
    {
        $subCategories = SubCategory::where('is_active', true)->with('category')->orderBy('name')->get();
        $view = view('admin.services.edit-modal', compact('service', 'subCategories'));
        
        if ($request->ajax()) {
            return response()->json([
                'html' => $view->render()
            ]);
        }
        
        return view('admin.services.edit', compact('service', 'subCategories'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        if (empty($data['slug'])) {
            unset($data['slug']);
        }

        $service->update($data);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.service_updated'),
                'redirect' => route('admin.services.index')
            ]);
        }
        
        return redirect()->route('admin.services.index')
            ->with('success', __('messages.service_updated'));
    }


    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'تم حذف الخدمة بنجاح');
    }
}
