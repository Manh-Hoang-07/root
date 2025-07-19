<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Services\AttributeService;
use App\Http\Resources\AttributeResource;
use Illuminate\Http\Request;

class AttributeController extends BaseController
{
    public function __construct(AttributeService $service)
    {
        parent::__construct($service, AttributeResource::class);
    }
    // Có thể bổ sung các hàm filter, search... nếu cần
} 