<?php

namespace App\Http\Controllers\Api\Admin\PostCategory;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\PostCategory\PostCategoryService;
use App\Http\Requests\Admin\PostCategory\PostCategoryRequest;

class PostCategoryController extends BaseController
{
    protected $storeRequestClass = PostCategoryRequest::class;
    protected $updateRequestClass = PostCategoryRequest::class;

    /**
     * @var PostCategoryService
     */
    protected $service;

    public function __construct(PostCategoryService $service)
    {
        parent::__construct($service);
    }
}
