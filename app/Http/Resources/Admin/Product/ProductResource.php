<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'image' => $this->image,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'brand_id' => $this->brand_id,
            'brand_name' => $this->brand_name,
            'status' => $this->status,
            'sku' => $this->main_sku,
            'category_names' => $this->category_names,
            'total_quantity' => $this->total_quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'brand' => $this->whenLoaded('brand'),
            'categories' => $this->whenLoaded('categories'),
            'variants' => $this->whenLoaded('variants'),
            'inventory' => $this->whenLoaded('inventory'),
            'images' => $this->whenLoaded('images'),
        ];
    }
} 