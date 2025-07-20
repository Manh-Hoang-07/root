<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\ShippingService;
use App\Http\Requests\Admin\Shipping\ShippingServiceRequest;
use App\Http\Resources\Admin\Shipping\ShippingServiceResource;

class ServiceController extends Controller
{
    public function index()
    {
        $services = ShippingService::query()->latest()->paginate(20);
        return ShippingServiceResource::collection($services);
    }

    public function store(ShippingServiceRequest $request)
    {
        $service = ShippingService::create($request->validated());
        return new ShippingServiceResource($service);
    }

    public function show($id)
    {
        $service = ShippingService::findOrFail($id);
        return new ShippingServiceResource($service);
    }

    public function update(ShippingServiceRequest $request, $id)
    {
        $service = ShippingService::findOrFail($id);
        $service->update($request->validated());
        return new ShippingServiceResource($service);
    }

    public function destroy($id)
    {
        $service = ShippingService::findOrFail($id);
        $service->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
} 