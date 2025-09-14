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

    public function findBySlug(string $slug, array $relations = [], array $fields = ['*'])
    {
        $query = $this->getModel()->newQuery();

        if (!empty($relations)) {
            $query->with($this->optimizeRelations($relations));
        }
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($this->optimizeFields($fields));
        }

        return $query->where('slug', $slug)->first();
    }
}


