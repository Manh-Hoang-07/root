<?php

namespace App\Http\Resources\Admin\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        // Tối ưu: Chỉ transform những gì cần thiết
        $data = [
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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Lazy load brand name nếu có brand relation
        if ($this->relationLoaded('brand')) {
            $data['brand_name'] = $this->brand ? $this->brand->name : null;
        }

        // Lazy load category names nếu có categories relation
        if ($this->relationLoaded('categories')) {
            $data['category_names'] = $this->categories->pluck('name')->implode(', ');
        }

        // Lazy load images nếu có images relation
        if ($this->relationLoaded('images')) {
            $data['product_images'] = $this->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->url,
                    'alt' => $image->alt ?? null,
                ];
            });
        }

        // Lazy load variants nếu có variants relation - tối ưu transformation
        if ($this->relationLoaded('variants')) {
            $data['variants'] = $this->variants->map(function($variant) {
                $variantData = [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'barcode' => $variant->barcode,
                    'price' => $variant->price,
                    'sale_price' => $variant->sale_price,
                    'quantity' => $variant->quantity,
                    'image' => $variant->image,
                    'status' => $variant->status,
                ];

                // Chỉ load attribute values nếu cần
                if ($variant->relationLoaded('attributeValues')) {
                    $variantData['attribute_values'] = $variant->attributeValues->map(function($attrValue) {
                        return [
                            'id' => $attrValue->id,
                            'attribute_id' => $attrValue->attribute_id,
                            'value' => $attrValue->value,
                            'attribute_name' => $attrValue->relationLoaded('attribute') ? $attrValue->attribute->name : null,
                        ];
                    });
                }

                return $variantData;
            });
        }

        // Lazy load inventory nếu có inventory relation
        if ($this->relationLoaded('inventory')) {
            $data['inventory'] = $this->inventory;
            $data['total_quantity'] = $this->inventory->sum('quantity');
        }

        // Lazy load main SKU từ variants
        if ($this->relationLoaded('variants') && $this->variants->isNotEmpty()) {
            $data['sku'] = $this->variants->first()->sku;
        }

        // Lazy load has_variants
        if ($this->relationLoaded('variants')) {
            $data['has_variants'] = $this->variants->isNotEmpty();
        }

        // Lazy load attributes
        if ($this->attributes) {
            $data['attributes'] = $this->attributes;
        }

        return $data;
    }
} 