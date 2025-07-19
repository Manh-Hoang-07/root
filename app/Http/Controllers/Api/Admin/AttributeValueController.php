<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Services\AttributeValueService;
use App\Http\Resources\AttributeValueResource;
use Illuminate\Http\Request;

class AttributeValueController extends BaseController
{
    public function __construct(AttributeValueService $service)
    {
        parent::__construct($service, AttributeValueResource::class);
    }
    // Có thể bổ sung các hàm filter, search... nếu cần
} 