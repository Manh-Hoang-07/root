<?php

namespace App\Http\Resources\Admin\Shipping;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingPricingRuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'zone_id' => $this->zone_id,
            'min_weight' => $this->min_weight,
            'max_weight' => $this->max_weight,
            'markup_percent' => $this->markup_percent,
            'markup_fixed' => $this->markup_fixed,
            'min_fee' => $this->min_fee,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
