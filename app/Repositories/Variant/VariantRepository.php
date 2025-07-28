<?php

namespace App\Repositories\Variant;

use App\Models\Variant;
use App\Repositories\BaseRepository;

class VariantRepository extends BaseRepository
{
    public function model()
    {
        return Variant::class;
    }

    /**
     * Get variants by product with relationships
     */
    public function getVariantsByProduct($productId)
    {
        return $this->model->with(['attributeValues.attribute'])
                          ->where('product_id', $productId)
                          ->orderBy('created_at', 'desc')
                          ->get();
    }
} 