<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class ServiceController extends Controller
{
    use ApiResponseTrait;

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

        $services = $query->with('pointsPricing')->orderBy('name')->get();

        // Filter locale columns and add points pricing
        $filteredData = $services->map(function ($service) {
            $data = $this->filterLocaleColumns($service);
            $data['points_price'] = $service->pointsPricing && $service->pointsPricing->is_active 
                ? (float) $service->pointsPricing->points_price 
                : null;
            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => $filteredData,
        ]);
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $service = Service::create($data);
        $service->load(['subCategory.category']);

        return response()->json([
            'success' => true,
            'message' => __('messages.service_created_success'),
            'data' => $this->filterLocaleColumns($service),
        ], 201);
    }

    public function show(Service $service)
    {
        $service->load(['subCategory.category', 'pointsPricing']);
        
        $data = $this->filterLocaleColumns($service);
        $data['points_price'] = $service->pointsPricing && $service->pointsPricing->is_active 
            ? (float) $service->pointsPricing->points_price 
            : null;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $service->update($data);
        $service->fresh()->load(['subCategory.category']);

        return response()->json([
            'success' => true,
            'message' => __('messages.service_updated_success'),
            'data' => $this->filterLocaleColumns($service->fresh()),
        ]);
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.service_deleted_success'),
        ]);
    }
}
