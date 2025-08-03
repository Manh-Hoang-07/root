<?php

namespace App\Http\Resources\Admin\Warehouse;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseListResource extends JsonResource
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
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
} 