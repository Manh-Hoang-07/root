<?php

namespace App\Services\Public\PostTag;

use App\Services\BaseService;
use App\Repositories\PostTag\PostTagRepository;
use Illuminate\Database\Eloquent\Collection;

class PostTagService extends BaseService
{
    public function __construct(PostTagRepository $repo)
    {
        parent::__construct($repo);
    }
}
