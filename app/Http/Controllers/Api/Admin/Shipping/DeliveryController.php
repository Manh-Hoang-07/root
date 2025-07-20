<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\ShippingDeliverySetting;
use App\Http\Requests\Admin\Shipping\ShippingDeliverySettingRequest;
use App\Http\Resources\Admin\Shipping\ShippingDeliverySettingResource;

class DeliveryController extends Controller
{
    public function index()
    {
        $settings = ShippingDeliverySetting::query()->latest()->paginate(20);
        return ShippingDeliverySettingResource::collection($settings);
    }

    public function store(ShippingDeliverySettingRequest $request)
    {
        $setting = ShippingDeliverySetting::create($request->validated());
        return new ShippingDeliverySettingResource($setting);
    }

    public function show($id)
    {
        $setting = ShippingDeliverySetting::findOrFail($id);
        return new ShippingDeliverySettingResource($setting);
    }

    public function update(ShippingDeliverySettingRequest $request, $id)
    {
        $setting = ShippingDeliverySetting::findOrFail($id);
        $setting->update($request->validated());
        return new ShippingDeliverySettingResource($setting);
    }

    public function destroy($id)
    {
        $setting = ShippingDeliverySetting::findOrFail($id);
        $setting->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
} 