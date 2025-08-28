<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->profile?->name ?? $this->username,
            'phone' => $this->phone,
            'status' => $this->status,
            // 'last_login_at' => $this->last_login_at,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
            'permissions' => $this->getAllPermissions()->pluck('name'),
            // 'roles' => $this->getRoleNames()
        ];
    }
} 