<?php
namespace App\Http\Controllers\Api\Admin\Brand;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Brand\BrandService;
use App\Http\Requests\Admin\Brand\BrandRequest;

class BrandController extends BaseController
{
    protected $storeRequestClass = BrandRequest::class;
    protected $updateRequestClass = BrandRequest::class;
    
    /**
     * @var BrandService
     */
    protected $service;

    public function __construct(BrandService $service)
    {
        parent::__construct($service);
    }
}
