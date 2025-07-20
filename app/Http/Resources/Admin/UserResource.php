<?php

namespace App\Http\Resources\Admin;

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
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->toDateTimeString() : null,
            'phone_verified_at' => $this->phone_verified_at ? $this->phone_verified_at->toDateTimeString() : null,
            'last_login_at' => $this->last_login_at ? $this->last_login_at->toDateTimeString() : null,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            // Profile fields
            'name' => $this->profile ? $this->profile->name : null,
            'gender' => $this->profile ? $this->profile->gender : null,
            'address' => $this->profile ? $this->profile->address : null,
            'avatar' => $this->profile ? $this->profile->avatar : null,
        ];
    }
}
