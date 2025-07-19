<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Services\BrandService;
use App\Http\Resources\BrandResource;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    public function __construct(BrandService $service)
    {
        parent::__construct($service, BrandResource::class);
    }
    // Có thể bổ sung các hàm filter, search... nếu cần
} 