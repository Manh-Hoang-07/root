<?php
namespace App\Repositories\Product;

use App\Models\ProductInventory;
use App\Repositories\BaseRepository;

class ProductInventoryRepository extends BaseRepository
{
    public function model(): string
    {
        return ProductInventory::class;
    }
} 