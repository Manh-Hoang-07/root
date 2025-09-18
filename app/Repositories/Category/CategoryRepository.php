<?php
namespace App\Repositories\Category;

use App\Models\Category;
use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    public function model(): string
    {
        return Category::class;
    }

    /**
     * Override searchable fields for Category
     */
    protected function getSearchableFields(): array
    {
        return ['name', 'description'];
    }
} 