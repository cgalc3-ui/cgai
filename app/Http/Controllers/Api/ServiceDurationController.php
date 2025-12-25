<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceDurationRequest;
use App\Http\Requests\UpdateServiceDurationRequest;
use App\Models\ServiceDuration;
use Illuminate\Http\Request;

class ServiceDurationController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceDuration::with('service.subCategory.category')
            ->where('is_active', true);
        
        if ($request->has('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        
        $durations = $query->orderBy('duration_value')->get();
        
        return response()->json([
            'success' => true,
            'data' => $durations,
        ]);
    }

    public function store(StoreServiceDurationRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        $duration = ServiceDuration::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء مدة الخدمة بنجاح',
            'data' => $duration->load('service.subCategory.category'),
        ], 201);
    }

    public function show(ServiceDuration $serviceDuration)
    {
        return response()->json([
            'success' => true,
            'data' => $serviceDuration->load('service.subCategory.category'),
        ]);
    }

    public function update(UpdateServiceDurationRequest $request, ServiceDuration $serviceDuration)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        $serviceDuration->update($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث مدة الخدمة بنجاح',
            'data' => $serviceDuration->fresh()->load('service.subCategory.category'),
        ]);
    }

    public function destroy(ServiceDuration $serviceDuration)
    {
        $serviceDuration->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف مدة الخدمة بنجاح',
        ]);
    }
}
