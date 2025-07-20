<?php

namespace App\Http\Resources\Admin\Brand;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'image' => $this->image, // Đổi từ logo sang image
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 