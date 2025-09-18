<?php

namespace App\Http\Controllers\Api\Admin\PostTag;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\PostTag\PostTagService;
use App\Http\Requests\Admin\PostTag\PostTagRequest;

class PostTagController extends BaseController
{
    /**
     * @var PostTagService
     */
    protected $service;

    protected $indexRelations = [];
    protected $showRelations = [];

    public function __construct(PostTagService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = PostTagRequest::class;
        $this->updateRequestClass = PostTagRequest::class;
    }
}
