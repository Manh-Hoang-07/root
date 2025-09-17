<?php

namespace App\Http\Controllers\Api\Admin\Post;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Post\PostService;
use App\Http\Requests\Admin\Post\PostRequest;

class PostController extends BaseController
{
    protected $indexRelations = ['categories:id,name,slug', 'tags:id,name,slug'];
    protected $showRelations = ['categories:id,name,slug', 'tags:id,name,slug', 'primaryCategory:id,name,slug'];

    public function __construct(PostService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = PostRequest::class;
        $this->updateRequestClass = PostRequest::class;
    }
}


