<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['subCategory.category', 'specialization'])
            ->where('is_active', true);

        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        if ($request->has('specialization_id')) {
            $query->where('specialization_id', $request->specialization_id);
        }

        $services = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $service = Service::create($data);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الخدمة بنجاح',
            'data' => $service->load(['subCategory.category', 'specialization']),
        ], 201);
    }

    public function show(Service $service)
    {
        return response()->json([
            'success' => true,
            'data' => $service->load(['subCategory.category', 'specialization']),
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $service->update($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الخدمة بنجاح',
            'data' => $service->fresh()->load(['subCategory.category', 'specialization']),
        ]);
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الخدمة بنجاح',
        ]);
    }
}
