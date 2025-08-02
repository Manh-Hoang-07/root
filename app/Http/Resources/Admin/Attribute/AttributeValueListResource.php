<?php

namespace App\Http\Resources\Admin\Attribute;

use Illuminate\Http\Resources\Json\JsonResource;

class AttributeValueListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'attribute_id' => $this->attribute_id,
            'attribute_name' => $this->whenLoaded('attribute', function() {
                return $this->attribute->name ?? null;
            }),
            'value' => $this->value,
            'name' => $this->name,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
} 