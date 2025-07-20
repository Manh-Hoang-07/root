<?php

namespace App\Http\Resources\Admin\Order;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'shipping_fee' => $this->shipping_fee,
            'shipping_discount' => $this->shipping_discount,
            'promotion_discount' => $this->promotion_discount,
            'final_price' => $this->final_price,
            'shipping_address_id' => $this->shipping_address_id,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cancelled_at' => $this->cancelled_at,
            'completed_at' => $this->completed_at,
            // Có thể bổ sung thêm các trường khác nếu cần
        ];
    }
} 