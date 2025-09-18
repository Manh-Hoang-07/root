<?php

namespace App\Services\Public\PostCategory;

use App\Services\BaseService;
use App\Repositories\PostCategory\PostCategoryRepository;

class PostCategoryService extends BaseService
{
    /**
     * @var PostCategoryRepository
     */
    protected $repo;
    public function __construct(PostCategoryRepository $repo)
    {
        parent::__construct($repo);
    }
}
