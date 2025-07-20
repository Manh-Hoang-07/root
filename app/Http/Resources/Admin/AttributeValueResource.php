<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeValueResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'attribute_id' => $this->attribute_id,
            'attribute_name' => $this->attribute->name ?? null,
            'value' => $this->value,
            'name' => $this->name,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 