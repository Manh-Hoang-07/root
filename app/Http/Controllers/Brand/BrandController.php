<?php
namespace App\Http\Controllers\Brand;

use App\Http\Controllers\BaseController;
use App\Services\Brand\BrandService;
use App\Http\Resources\BrandResource;

class BrandController extends BaseController
{
    public function __construct(BrandService $service)
    {
        parent::__construct($service, BrandResource::class);
    }
} 