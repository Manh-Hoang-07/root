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
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];

        // Lazy load parent_name nếu có parent relation
        if ($this->relationLoaded('parent')) {
            $data['parent_name'] = $this->parent ? $this->parent->name : null;
        } elseif (isset($this->parent_id)) {
            // Nếu không có parent relation nhưng có parent_id, có thể load sau
            $data['parent_id'] = $this->parent_id;
        }

        return $data;
    }
} 