<?php

namespace App\Http\Resources\Admin\User;

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
            // Profile fields - chỉ trả về khi relationship được load
            'name' => $this->whenLoaded('profile', function() {
                return $this->profile ? $this->profile->name : null;
            }, null),
            'gender' => $this->whenLoaded('profile', function() {
                return $this->profile ? $this->profile->gender : null;
            }, null),
            'birthday' => $this->whenLoaded('profile', function() {
                return $this->profile && $this->profile->birthday ? $this->profile->birthday->format('Y-m-d') : null;
            }, null),
            'address' => $this->whenLoaded('profile', function() {
                return $this->profile ? $this->profile->address : null;
            }, null),
            'image' => $this->whenLoaded('profile', function() {
                return $this->profile ? $this->profile->image : null;
            }, null),
            'about' => $this->whenLoaded('profile', function() {
                return $this->profile ? $this->profile->about : null;
            }, null),
            
            // Roles relationship
            'roles' => $this->whenLoaded('roles', function() {
                return $this->roles->map(function($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name,
                    ];
                });
            }, []),
        ];
    }
}
