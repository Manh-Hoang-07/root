<?php

namespace App\Http\Resources\Admin\Inventory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'quantity' => $this->quantity,
            'batch_no' => $this->batch_no,
            'lot_no' => $this->lot_no,
            'expiry_date' => $this->expiry_date?->format('Y-m-d'),
            'reserved_quantity' => $this->reserved_quantity,
            'available_quantity' => $this->available_quantity,
            'cost_price' => $this->cost_price,
            'stock_status' => $this->stock_status,
            'stock_status_text' => $this->stock_status_text,
            'stock_status_class' => $this->stock_status_class,
            'expiry_status_class' => $this->expiry_status_class,
            'is_expired' => $this->is_expired,
            'is_expiring_soon' => $this->is_expiring_soon,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Relationships
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'name' => $this->product->name,
                    'code' => $this->product->code,
                    'image' => $this->product->image,
                    'brand' => ($this->product->relationLoaded('brand') && $this->product->brand) ? [
                        'id' => $this->product->brand->id,
                        'name' => $this->product->brand->name,
                    ] : null,
                ];
            }),
            
            'warehouse' => $this->whenLoaded('warehouse', function () {
                return [
                    'id' => $this->warehouse->id,
                    'name' => $this->warehouse->name,
                    'address' => $this->warehouse->address,
                ];
            }),
        ];
    }
} 