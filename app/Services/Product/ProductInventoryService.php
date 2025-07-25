<?php
namespace App\Services\Product;

use App\Repositories\Product\ProductInventoryRepository;
use App\Services\BaseService;

class ProductInventoryService extends BaseService
{
    public function __construct(ProductInventoryRepository $repo)
    {
        parent::__construct($repo);
    }
} 