<?php

namespace App\Http\Controllers\Api\Admin\PostTag;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\PostTag\PostTagService;
use App\Http\Requests\Admin\PostTag\PostTagRequest;

class PostTagController extends BaseController
{
    protected static $serviceClass = PostTagService::class;
    protected $indexRelations = [];
    protected $showRelations = [];
    protected $storeRequestClass = PostTagRequest::class;
    protected $updateRequestClass = PostTagRequest::class;
}
