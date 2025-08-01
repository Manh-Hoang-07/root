<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'status' => $this->status,
            
            // Chỉ load relationships cần thiết cho list
            'brand' => $this->whenLoaded('brand', function() {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                ];
            }),
        ];
    }
} 