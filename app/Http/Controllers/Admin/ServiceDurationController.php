<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceDurationRequest;
use App\Http\Requests\UpdateServiceDurationRequest;
use App\Models\Service;
use App\Models\ServiceDuration;
use Illuminate\Http\Request;

class ServiceDurationController extends Controller
{
    public function index()
    {
        $durations = ServiceDuration::with('service.subCategory.category')->latest()->paginate(15);
        return view('admin.service-durations.index', compact('durations'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)->with('subCategory.category')->orderBy('name')->get();
        return view('admin.service-durations.create', compact('services'));
    }

    public function store(StoreServiceDurationRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        ServiceDuration::create($data);

        return redirect()->route('admin.service-durations.index')
            ->with('success', 'تم إنشاء مدة الخدمة بنجاح');
    }

    public function edit(ServiceDuration $serviceDuration)
    {
        $services = Service::where('is_active', true)->with('subCategory.category')->orderBy('name')->get();
        return view('admin.service-durations.edit', compact('serviceDuration', 'services'));
    }

    public function update(UpdateServiceDurationRequest $request, ServiceDuration $serviceDuration)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        $serviceDuration->update($data);

        return redirect()->route('admin.service-durations.index')
            ->with('success', 'تم تحديث مدة الخدمة بنجاح');
    }

    public function destroy(ServiceDuration $serviceDuration)
    {
        $serviceDuration->delete();

        return redirect()->route('admin.service-durations.index')
            ->with('success', 'تم حذف مدة الخدمة بنجاح');
    }
}
