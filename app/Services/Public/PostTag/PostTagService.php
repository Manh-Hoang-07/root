<?php

namespace App\Services\Public\PostTag;

use App\Services\BaseService;
use App\Repositories\PostTag\PostTagRepository;

class PostTagService extends BaseService
{
    /**
     * @var PostTagRepository
     */
    protected $repo;

    public function __construct(PostTagRepository $repo)
    {
        parent::__construct($repo);
    }
}
