<?php

namespace App\Http\Resources\Admin\Permission;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'guard_name' => $this->guard_name,
            'parent_id' => $this->parent_id,
            'parent_name' => $this->whenLoaded('parent', function() {
                return $this->parent ? $this->parent->display_name : null;
            }),
            'status' => $this->status,
            'has_children' => $this->children_count > 0,
            'children_count' => $this->children_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 