<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'product_name' => $this->product_name,
            'variant_sku' => $this->variant_sku,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'final_price' => $this->final_price,
            'total' => $this->total,
            'created_at' => $this->created_at,
        ];
    }
} 