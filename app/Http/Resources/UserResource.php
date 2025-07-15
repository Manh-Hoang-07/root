<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->profile ? $this->profile->name : null,
            'email' => $this->email,
            'status' => $this->status,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'last_login_at' => $this->last_login_at ? $this->last_login_at->toDateTimeString() : null,
        ];
    }
}
