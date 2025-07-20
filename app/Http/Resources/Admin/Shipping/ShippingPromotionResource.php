<?php

namespace App\Http\Resources\Admin\Shipping;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingPromotionResource extends JsonResource
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
            'discount' => $this->discount,
            'type' => $this->type,
            'service_id' => $this->service_id,
            'zone_id' => $this->zone_id,
            'valid_from' => $this->valid_from,
            'valid_until' => $this->valid_until,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
