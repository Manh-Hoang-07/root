<?php
namespace App\Http\Controllers\Api\Admin\Shipping;

use App\Http\Controllers\BaseController;
use App\Services\Shipping\BrandService;
use App\Http\Resources\BrandResource;

class BrandController extends BaseController
{
    public function __construct(BrandService $service)
    {
        parent::__construct($service, BrandResource::class);
    }
} 