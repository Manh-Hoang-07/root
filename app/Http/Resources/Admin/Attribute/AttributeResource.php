<?php

namespace App\Http\Resources\Admin\Attribute;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'is_required' => $this->is_required,
            'is_unique' => $this->is_unique,
            'is_filterable' => $this->is_filterable,
            'is_searchable' => $this->is_searchable,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'attribute_values' => $this->whenLoaded('attributeValues'),
        ];
    }
} 