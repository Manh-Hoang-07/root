<?php
namespace App\Http\Controllers\Api\Admin\Brand;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Brand\BrandService;
use App\Http\Resources\Admin\Brand\BrandResource;
use App\Http\Requests\Admin\Brand\BrandRequest;

class BrandController extends BaseController
{
    public function __construct(BrandService $service)
    {
        parent::__construct($service, BrandResource::class);
        $this->storeRequestClass = BrandRequest::class;
        $this->updateRequestClass = BrandRequest::class;
    }
} 
