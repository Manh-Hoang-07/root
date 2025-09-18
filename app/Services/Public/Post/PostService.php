<?php

namespace App\Services\Public\Post;

use App\Services\BaseService;
use App\Repositories\Post\PostRepository;

class PostService extends BaseService
{
    /**
     * @var PostRepository
     */
    protected $repo;

    public function __construct(PostRepository $repo)
    {
        parent::__construct($repo);
    }
}
