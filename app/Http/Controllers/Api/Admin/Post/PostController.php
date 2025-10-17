<?php

namespace App\Http\Controllers\Api\Admin\Post;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\Post\PostRequest;
use App\Services\Admin\Post\PostService;

class PostController extends CrudController
{
    protected $storeRequestClass = PostRequest::class;
    protected $updateRequestClass = PostRequest::class;
    protected $indexRelations = ['categories:id,name,slug', 'tags:id,name,slug'];
    protected $showRelations = ['categories:id,name,slug', 'tags:id,name,slug', 'primaryCategory:id,name,slug'];

    /**
     * @var PostService
     */
    protected $service;

    public function __construct(PostService $service)
    {
        parent::__construct($service);
    }
}


