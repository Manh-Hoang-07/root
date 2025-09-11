<?php

namespace App\Http\Resources\SystemConfig;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemConfigResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'config_key' => $this->config_key,
            'config_value' => $this->config_value,
            'value' => $this->value, // Computed value with type casting
            'config_type' => $this->config_type,
            'config_group' => $this->config_group,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'is_public' => $this->is_public,
            'is_required' => $this->is_required,
            'validation_rules' => $this->validation_rules,
            'default_value' => $this->default_value,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
