<?php

namespace App\Repositories\PostCategory;

use App\Models\PostCategory;
use App\Repositories\BaseRepository;

class PostCategoryRepository extends BaseRepository
{
    public function model(): string
    {
        return PostCategory::class;
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
        $category = $query->where('slug', $slug)->first();
        return $category ? $category->toArray() : null;
    }
}


