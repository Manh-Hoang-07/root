<?php

namespace App\Repositories\PostCategory;

use App\Models\PostCategory;
use App\Repositories\BaseRepository;

class PostCategoryRepository extends BaseRepository
{
    public function model()
    {
        return PostCategory::class;
    }

    public function findBySlug(string $slug, $relations = [], $fields = ['*'])
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


