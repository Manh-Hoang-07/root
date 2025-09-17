<?php

namespace App\Http\Controllers\Api\Admin\Post;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Post\PostService;
use App\Http\Requests\Admin\Post\PostRequest;

class PostController extends BaseController
{
    protected static $serviceClass = PostService::class;
    protected $indexRelations = ['categories:id,name,slug', 'tags:id,name,slug'];
    protected $showRelations = ['categories:id,name,slug', 'tags:id,name,slug', 'primaryCategory:id,name,slug'];
    protected $storeRequestClass = PostRequest::class;
    protected $updateRequestClass = PostRequest::class;
}


