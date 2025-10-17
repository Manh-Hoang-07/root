<?php

namespace App\Http\Controllers\Api\Admin\PostTag;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\PostTag\PostTagRequest;
use App\Services\Admin\PostTag\PostTagService;

class PostTagController extends CrudController
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
