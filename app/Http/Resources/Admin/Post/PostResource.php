<?php

namespace App\Http\Resources\Admin\Post;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'image' => $this->image,
            'cover_image' => $this->cover_image,
            'status' => $this->status,
            'is_featured' => (bool) $this->is_featured,
            'is_pinned' => (bool) $this->is_pinned,
            'published_at' => $this->published_at,
            'view_count' => (int) $this->view_count,
            'primary_postcategory_id' => $this->primary_postcategory_id,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'canonical_url' => $this->canonical_url,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'og_image' => $this->og_image,
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($c) {
                    return [ 'id' => $c->id, 'name' => $c->name, 'slug' => $c->slug ];
                });
            }),
            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(function ($t) {
                    return [ 'id' => $t->id, 'name' => $t->name, 'slug' => $t->slug ];
                });
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


