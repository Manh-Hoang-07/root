<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use App\Http\Requests\Admin\Shipping\ShippingZoneRequest;
use App\Http\Resources\Admin\Shipping\ShippingZoneResource;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::query()->latest()->paginate(20);
        return ShippingZoneResource::collection($zones);
    }

    public function store(ShippingZoneRequest $request)
    {
        $zone = ShippingZone::create($request->validated());
        return new ShippingZoneResource($zone);
    }

    public function show($id)
    {
        $zone = ShippingZone::findOrFail($id);
        return new ShippingZoneResource($zone);
    }

    public function update(ShippingZoneRequest $request, $id)
    {
        $zone = ShippingZone::findOrFail($id);
        $zone->update($request->validated());
        return new ShippingZoneResource($zone);
    }

    public function destroy($id)
    {
        $zone = ShippingZone::findOrFail($id);
        $zone->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
} 