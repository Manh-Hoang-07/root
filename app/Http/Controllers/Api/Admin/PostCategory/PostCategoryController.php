<?php

namespace App\Http\Controllers\Api\Admin\PostCategory;

use App\Http\Controllers\Api\BaseController;
use App\Services\PostCategory\PostCategoryService;
use App\Http\Resources\Admin\PostCategory\PostCategoryResource;
use App\Http\Requests\Admin\PostCategory\PostCategoryRequest;

class PostCategoryController extends BaseController
{
    protected $indexRelations = [];
    protected $showRelations = [];

    public function __construct(PostCategoryService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = PostCategoryRequest::class;
        $this->updateRequestClass = PostCategoryRequest::class;
    }
}
