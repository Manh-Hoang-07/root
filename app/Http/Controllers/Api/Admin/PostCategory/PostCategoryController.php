<?php

namespace App\Http\Controllers\Api\Admin\PostCategory;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\PostCategory\PostCategoryRequest;
use App\Services\Admin\PostCategory\PostCategoryService;

class PostCategoryController extends CrudController
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
