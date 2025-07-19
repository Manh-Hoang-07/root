<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'warehouse_id' => $this->warehouse_id,
            'quantity' => $this->quantity,
            'updated_at' => $this->updated_at,
        ];
    }
} 