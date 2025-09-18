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

    // Deprecated: use BaseRepository::findOneBy(['slug' => $slug], ...)
}


