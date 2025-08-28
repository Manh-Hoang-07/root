<?php

namespace App\Services\PostCategory;

use App\Services\BaseService;
use App\Repositories\PostCategory\PostCategoryRepository;

class PostCategoryService extends BaseService
{
    public function __construct(PostCategoryRepository $repo)
    {
        parent::__construct($repo);
    }

    public function findBySlug(string $slug, $relations = [], $fields = ['*'])
    {
        return $this->repo->findBySlug($slug, $relations, $fields);
    }

    public function create($data)
    {
        $data = $this->ensureSlug($data);
        return parent::create($data);
    }

    public function update($id, $data)
    {
        $data = $this->ensureSlug($data);
        return parent::update($id, $data);
    }
}


