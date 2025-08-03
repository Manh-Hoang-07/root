<?php

namespace App\Http\Resources\Admin\Inventory;

use Illuminate\Http\Resources\Json\JsonResource;

class InventoryListResource extends JsonResource
{
    public function toArray($request)
    {

        
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product ? $this->product->name : null,
            'product_code' => $this->product ? $this->product->code : null,
            
            'variant_id' => $this->variant_id,
            'variant_sku' => $this->variant ? $this->variant->sku : null,
            'variant_name' => $this->variant ? $this->variant->name : null,

            'warehouse_id' => $this->warehouse_id,
            'warehouse_name' => $this->warehouse ? $this->warehouse->name : null,
            'quantity' => $this->quantity,
            'reserved_quantity' => $this->reserved_quantity,
            'available_quantity' => $this->available_quantity,
            'min_quantity' => $this->min_quantity,
            'max_quantity' => $this->max_quantity,
            'batch_no' => $this->batch_no,
            'lot_no' => $this->lot_no,
            'expiry_date' => $this->expiry_date,
            'cost_price' => $this->cost_price,
            'status' => $this->status,
            'created_at' => $this->created_at,

        ];
    }
} 