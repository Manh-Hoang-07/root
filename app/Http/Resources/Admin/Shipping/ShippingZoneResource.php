<?php

namespace App\Http\Resources\Admin\Shipping;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingZoneResource extends JsonResource
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
            'name' => $this->name,
            'provinces' => $this->provinces,
            'districts' => $this->districts,
            'base_fee' => $this->base_fee,
            'weight_fee' => $this->weight_fee,
            'estimated_days' => $this->estimated_days,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
