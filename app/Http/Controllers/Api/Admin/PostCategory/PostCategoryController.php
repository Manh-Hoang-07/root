<?php

namespace App\Http\Controllers\Api\Admin\PostCategory;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\PostCategory\PostCategoryService;
use App\Http\Requests\Admin\PostCategory\PostCategoryRequest;

class PostCategoryController extends BaseController
{
    protected static $serviceClass = PostCategoryService::class;
    protected $indexRelations = [];
    protected $showRelations = [];
    protected $storeRequestClass = PostCategoryRequest::class;
    protected $updateRequestClass = PostCategoryRequest::class;
}
