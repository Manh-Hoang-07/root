<?php

namespace App\Http\Resources\Admin\Image;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'alt_text' => $this->alt_text,
            'caption' => $this->caption,
            'order' => $this->order,
            'imageable_type' => $this->imageable_type,
            'imageable_id' => $this->imageable_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 