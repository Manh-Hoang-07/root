<?php

namespace App\Repositories\PostTag;

use App\Models\PostTag;
use App\Repositories\BaseRepository;

class PostTagRepository extends BaseRepository
{
    public function model(): string
    {
        return PostTag::class;
    }

    public function findBySlug(string $slug, array $relations = [], array $fields = ['*']): ?array
    {
        $query = $this->getModel()->newQuery();
        if (!empty($relations)) {
            $query->with($relations);
        }
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($fields);
        }
        $tag = $query->where('slug', $slug)->first();
        return $tag ? $tag->toArray() : null;
    }
}


