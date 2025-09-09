<?php

namespace App\Http\Controllers\Api\Admin\PostTag;

use App\Http\Controllers\Api\BaseController;
use App\Services\PostTag\PostTagService;
use App\Http\Resources\Admin\PostTag\PostTagResource;
use App\Http\Requests\Admin\PostTag\PostTagRequest;

class PostTagController extends BaseController
{
    protected $indexRelations = [];
    protected $showRelations = [];

    public function __construct(PostTagService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = PostTagRequest::class;
        $this->updateRequestClass = PostTagRequest::class;
    }
}
