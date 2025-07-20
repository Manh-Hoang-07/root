<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\ShippingApiConfig;
use App\Http\Requests\Admin\Shipping\ShippingApiConfigRequest;
use App\Http\Resources\Admin\Shipping\ShippingApiConfigResource;

class ApiController extends Controller
{
    public function index()
    {
        $configs = ShippingApiConfig::query()->latest()->paginate(20);
        return ShippingApiConfigResource::collection($configs);
    }

    public function store(ShippingApiConfigRequest $request)
    {
        $config = ShippingApiConfig::create($request->validated());
        return new ShippingApiConfigResource($config);
    }

    public function show($id)
    {
        $config = ShippingApiConfig::findOrFail($id);
        return new ShippingApiConfigResource($config);
    }

    public function update(ShippingApiConfigRequest $request, $id)
    {
        $config = ShippingApiConfig::findOrFail($id);
        $config->update($request->validated());
        return new ShippingApiConfigResource($config);
    }

    public function destroy($id)
    {
        $config = ShippingApiConfig::findOrFail($id);
        $config->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
} 