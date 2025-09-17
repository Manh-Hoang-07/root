<?php

namespace App\Services\Admin\PostCategory;

use App\Services\BaseService;
use App\Repositories\PostCategory\PostCategoryRepository;

class PostCategoryService extends BaseService
{
    protected static $repositoryClass = PostCategoryRepository::class;

    public function findBySlug(string $slug, $relations = [], $fields = ['*']): ?array
    {
        return $this->repo->findBySlug($slug, $relations, $fields);
    }

    public function create($data): array
    {
        $data = $this->ensureSlug($data);
        return parent::create($data);
    }

    public function update($id, $data): ?array
    {
        $data = $this->ensureSlug($data);
        return parent::update($id, $data);
    }
}


