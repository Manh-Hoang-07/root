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
            'product_images' => $this->whenLoaded('images', function() {
                return $this->images->map(function($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->url,
                        'alt' => $image->alt ?? null,
                    ];
                });
            }),
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
            'attributes' => $this->attributes,
            'has_variants' => $this->hasVariants(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'brand' => $this->whenLoaded('brand'),
            'categories' => $this->whenLoaded('categories'),
            'variants' => $this->whenLoaded('variants', function() {
                return $this->variants->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'barcode' => $variant->barcode,
                        'price' => $variant->price,
                        'sale_price' => $variant->sale_price,
                        'quantity' => $variant->quantity,
                        'image' => $variant->image,
                        'status' => $variant->status,
                        'attribute_values' => $variant->whenLoaded('attributeValues', function() use ($variant) {
                            return $variant->attributeValues->map(function($attrValue) {
                                return [
                                    'id' => $attrValue->id,
                                    'attribute_id' => $attrValue->attribute_id,
                                    'attribute_name' => $attrValue->attribute->name ?? null,
                                    'value' => $attrValue->value,
                                ];
                            });
                        }, []),
                    ];
                });
            }),
            'inventory' => $this->whenLoaded('inventory'),
        ];
    }
} 