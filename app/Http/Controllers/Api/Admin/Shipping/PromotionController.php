<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\ShippingPromotion;
use App\Http\Requests\Admin\Shipping\ShippingPromotionRequest;
use App\Http\Resources\Admin\Shipping\ShippingPromotionResource;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = ShippingPromotion::query()->latest()->paginate(20);
        return ShippingPromotionResource::collection($promotions);
    }

    public function store(ShippingPromotionRequest $request)
    {
        $promotion = ShippingPromotion::create($request->validated());
        return new ShippingPromotionResource($promotion);
    }

    public function show($id)
    {
        $promotion = ShippingPromotion::findOrFail($id);
        return new ShippingPromotionResource($promotion);
    }

    public function update(ShippingPromotionRequest $request, $id)
    {
        $promotion = ShippingPromotion::findOrFail($id);
        $promotion->update($request->validated());
        return new ShippingPromotionResource($promotion);
    }

    public function destroy($id)
    {
        $promotion = ShippingPromotion::findOrFail($id);
        $promotion->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
} 