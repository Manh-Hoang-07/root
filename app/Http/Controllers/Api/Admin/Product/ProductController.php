<?php
namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\BaseController;
use App\Services\Product\ProductService;
use App\Http\Resources\Admin\Product\ProductResource;
use App\Http\Requests\Admin\Product\ProductRequest;

class ProductController extends BaseController
{
    public function __construct(ProductService $service)
    {
        parent::__construct($service, ProductResource::class);
        $this->storeRequestClass = ProductRequest::class;
        $this->updateRequestClass = ProductRequest::class;
    }
} 