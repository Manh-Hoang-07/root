<?php

namespace App\Http\Controllers\Api\Admin\Attribute;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Attribute\AttributeService;
use App\Http\Resources\Admin\Attribute\AttributeResource;
use App\Http\Requests\Admin\Attribute\AttributeRequest;

class AttributeController extends BaseController
{
    public function __construct(AttributeService $service)
    {
        parent::__construct($service, AttributeResource::class);
        $this->storeRequestClass = AttributeRequest::class;
        $this->updateRequestClass = AttributeRequest::class;
    }
} 
