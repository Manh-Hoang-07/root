<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use App\Models\Product;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    /**
     * Display a listing of shipping zones.
     */
    public function index(Request $request)
    {
        $query = ShippingZone::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $zones = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $zones,
            'message' => 'Shipping zones retrieved successfully'
        ]);
    }

    /**
     * Display the specified shipping zone.
     */
    public function show(ShippingZone $shippingZone)
    {
        return response()->json([
            'success' => true,
            'data' => $shippingZone,
            'message' => 'Shipping zone retrieved successfully'
        ]);
    }

    /**
     * Calculate shipping fee
     */
    public function calculateShipping(Request $request)
    {
        $request->validate([
            'shipping_address.province' => 'required|string',
            'shipping_address.district' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $province = $request->input('shipping_address.province');
        $district = $request->input('shipping_address.district');
        $items = $request->input('items');

        // Find shipping zone
        $shippingZone = ShippingZone::where('status', 'active')
                                   ->where(function ($query) use ($province, $district) {
                                       $query->whereJsonContains('provinces', $province)
                                             ->orWhereJsonContains('districts', $district);
                                   })
                                   ->first();

        if (!$shippingZone) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping zone not found for this address'
            ], 404);
        }

        // Calculate total weight
        $totalWeight = 0;
        $productIds = collect($items)->pluck('product_id');
        $products = Product::whereIn('id', $productIds)->get();

        foreach ($items as $item) {
            $product = $products->find($item['product_id']);
            if ($product) {
                $totalWeight += $product->weight * $item['quantity'];
            }
        }

        // Convert to kg
        $totalWeightKg = $totalWeight / 1000;

        // Calculate shipping fee
        $shippingFee = $shippingZone->calculateShippingFee($totalWeightKg);

        return response()->json([
            'success' => true,
            'data' => [
                'shipping_fee' => $shippingFee,
                'total_weight' => $totalWeight,
                'total_weight_kg' => $totalWeightKg,
                'estimated_days' => $shippingZone->estimated_days,
                'shipping_zone' => [
                    'name' => $shippingZone->name,
                    'base_fee' => $shippingZone->base_fee,
                    'weight_fee' => $shippingZone->weight_fee,
                ],
                'shipping_address' => $request->input('shipping_address'),
            ],
            'message' => 'Shipping fee calculated successfully'
        ]);
    }

    /**
     * Get available shipping zones for address
     */
    public function availableZones(Request $request)
    {
        $request->validate([
            'province' => 'required|string',
            'district' => 'required|string',
        ]);

        $province = $request->input('province');
        $district = $request->input('district');

        $zones = ShippingZone::where('status', 'active')
                            ->where(function ($query) use ($province, $district) {
                                $query->whereJsonContains('provinces', $province)
                                      ->orWhereJsonContains('districts', $district);
                            })
                            ->get();

        return response()->json([
            'success' => true,
            'data' => $zones,
            'message' => 'Available shipping zones retrieved successfully'
        ]);
    }
}
