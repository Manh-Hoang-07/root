<?php

namespace App\Http\Controllers\Api\Admin\PostTag;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\PostTag\PostTagService;
use App\Http\Requests\Admin\PostTag\PostTagRequest;

class PostTagController extends BaseController
{
    protected $storeRequestClass = PostTagRequest::class;
    protected $updateRequestClass = PostTagRequest::class;

    /**
     * @var PostTagService
     */
    protected $service;

    public function __construct(PostTagService $service)
    {
        parent::__construct($service);
    }
}
