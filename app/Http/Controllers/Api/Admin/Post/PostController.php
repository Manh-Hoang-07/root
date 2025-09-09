<?php

namespace App\Http\Controllers\Api\Admin\Post;

use App\Http\Controllers\Api\BaseController;
use App\Services\Post\PostService;
use App\Http\Resources\Admin\Post\PostResource;
use App\Http\Requests\Admin\Post\PostRequest;
use Illuminate\Http\Request;

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

    protected function getDefaultListFields(): array
    {
        return ['id','name','slug','image','status','published_at','created_at'];
    }
}


