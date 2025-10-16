<?php

namespace App\Services\Admin\Product;

use App\Services\BaseService;
use App\Repositories\Product\ProductVariantRepository;

class ProductVariantService extends BaseService
{
    public function __construct(ProductVariantRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Update variant status
     */
    public function updateStatus($id, string $status): ?array
    {
        return $this->repo->updateStatus($id, $status);
    }


    /**
     * Override create to ensure SKU generation
     */
    public function create($data): array
    {
        $data = $this->ensureSku($data);
        return parent::create($data);
    }

    /**
     * Override update to ensure SKU generation
     */
    public function update($id, $data): ?array
    {
        $data = $this->ensureSku($data);
        return parent::update($id, $data);
    }

    /**
     * Ensure SKU is generated if not provided
     */
    private function ensureSku(array $data): array
    {
        if (empty($data['sku']) && !empty($data['product_id'])) {
            $product = \App\Models\Product::find($data['product_id']);
            if ($product) {
                $baseSku = $product->sku;
                $variants = $this->repo->getByProductId($data['product_id']);
                $variantCount = count($variants);
                $data['sku'] = $baseSku . '-' . str_pad($variantCount + 1, 3, '0', STR_PAD_LEFT);
            }
        }
        
        return $data;
    }
}
