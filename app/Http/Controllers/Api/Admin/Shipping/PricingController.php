<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\ShippingPricingRule;
use App\Http\Requests\Admin\Shipping\ShippingPricingRuleRequest;
use App\Http\Resources\Admin\Shipping\ShippingPricingRuleResource;

class PricingController extends Controller
{
    public function index()
    {
        $rules = ShippingPricingRule::query()->latest()->paginate(20);
        return ShippingPricingRuleResource::collection($rules);
    }

    public function store(ShippingPricingRuleRequest $request)
    {
        $rule = ShippingPricingRule::create($request->validated());
        return new ShippingPricingRuleResource($rule);
    }

    public function show($id)
    {
        $rule = ShippingPricingRule::findOrFail($id);
        return new ShippingPricingRuleResource($rule);
    }

    public function update(ShippingPricingRuleRequest $request, $id)
    {
        $rule = ShippingPricingRule::findOrFail($id);
        $rule->update($request->validated());
        return new ShippingPricingRuleResource($rule);
    }

    public function destroy($id)
    {
        $rule = ShippingPricingRule::findOrFail($id);
        $rule->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
} 