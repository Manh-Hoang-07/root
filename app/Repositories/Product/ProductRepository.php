<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;
use App\Enums\ProductStatus;

class ProductRepository extends BaseRepository
{
    public function model()
    {
        return Product::class;
    }

    /**
     * Find active product by ID
     */
    public function findActive($id)
    {
        return $this->model
            ->where('id', $id)
            ->where('status', ProductStatus::ACTIVE)
            ->first()
            ?->toArray();
    }
}
