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

    /**
     * Override searchable fields for PostCategory to match schema
     */
    protected function getSearchableFields(): array
    {
        return [
            'name',
            'description',
            'slug',
            'meta_title',
            'meta_description',
            'canonical_url',
        ];
    }

    // Deprecated: use BaseRepository::findOneBy(['slug' => $slug], ...)
}


