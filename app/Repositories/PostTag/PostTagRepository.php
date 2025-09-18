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

    // Deprecated: use BaseRepository::findOneBy(['slug' => $slug], ...)
}


