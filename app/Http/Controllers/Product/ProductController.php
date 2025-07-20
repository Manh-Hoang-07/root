<?php
namespace App\Http\Controllers\Product;

use App\Http\Controllers\BaseController;
use App\Services\Product\ProductService;
use App\Http\Resources\ProductResource;

class ProductController extends BaseController
{
    public function __construct(ProductService $service)
    {
        parent::__construct($service, ProductResource::class);
    }
    // ... các hàm đặc thù ...
} 