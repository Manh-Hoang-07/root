<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\ShippingAdvancedSetting;
use App\Http\Requests\Admin\Shipping\ShippingAdvancedSettingRequest;
use App\Http\Resources\Admin\Shipping\ShippingAdvancedSettingResource;

class AdvancedController extends Controller
{
    public function index()
    {
        $settings = ShippingAdvancedSetting::query()->latest()->paginate(20);
        return ShippingAdvancedSettingResource::collection($settings);
    }

    public function store(ShippingAdvancedSettingRequest $request)
    {
        $setting = ShippingAdvancedSetting::create($request->validated());
        return new ShippingAdvancedSettingResource($setting);
    }

    public function show($id)
    {
        $setting = ShippingAdvancedSetting::findOrFail($id);
        return new ShippingAdvancedSettingResource($setting);
    }

    public function update(ShippingAdvancedSettingRequest $request, $id)
    {
        $setting = ShippingAdvancedSetting::findOrFail($id);
        $setting->update($request->validated());
        return new ShippingAdvancedSettingResource($setting);
    }

    public function destroy($id)
    {
        $setting = ShippingAdvancedSetting::findOrFail($id);
        $setting->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
} 