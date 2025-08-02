<?php

namespace App\Http\Resources\Admin\Category;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryListResource extends JsonResource
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
            'name' => $this->name,
            'parent_name' => optional($this->parent)->name,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
} 