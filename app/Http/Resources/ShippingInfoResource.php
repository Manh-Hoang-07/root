<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShippingInfoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'shipping_method' => $this->shipping_method,
            'provider' => $this->provider,
            'service_code' => $this->service_code,
            'provider_order_code' => $this->provider_order_code,
            'shipping_fee' => $this->shipping_fee,
            'shipping_discount' => $this->shipping_discount,
            'raw_fee_response' => $this->raw_fee_response,
            'tracking_number' => $this->tracking_number,
            'carrier' => $this->carrier,
            'status' => $this->status,
            'shipped_at' => $this->shipped_at,
            'delivered_at' => $this->delivered_at,
            'expected_delivery_time' => $this->expected_delivery_time,
            'note' => $this->note,
            'created_at' => $this->created_at,
        ];
    }
} 