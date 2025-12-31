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
        $query = Service::with(['subCategory.category'])
            ->where('is_active', true);

        if ($request->has('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // Filter by category_id (instead of specialization_id)
        if ($request->has('category_id')) {
            $query->whereHas('subCategory', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Backward compatibility: support specialization_id as category_id
        if ($request->has('specialization_id')) {
            $query->whereHas('subCategory', function ($q) use ($request) {
                $q->where('category_id', $request->specialization_id);
            });
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
            'data' => $service->load(['subCategory.category']),
        ], 201);
    }

    public function show(Service $service)
    {
        return response()->json([
            'success' => true,
            'data' => $service->load(['subCategory.category']),
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
            'data' => $service->fresh()->load(['subCategory.category']),
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
