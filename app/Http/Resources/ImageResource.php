<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'variant_id' => $this->variant_id,
            'url' => $this->url,
            'alt' => $this->alt,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
        ];
    }
} 