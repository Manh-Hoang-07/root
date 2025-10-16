<?php

namespace App\Services\Admin\Product;

use App\Services\BaseService;
use App\Repositories\Product\ProductRepository;

class ProductService extends BaseService
{
    public function __construct(ProductRepository $repo)
    {
        parent::__construct($repo);
    }



    /**
     * Update product status
     */
    public function updateStatus($id, string $status): ?array
    {
        return $this->repo->updateStatus($id, $status);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id): ?array
    {
        return $this->repo->toggleFeatured($id);
    }


    /**
     * Get product variants
     */
    public function getProductVariants($productId, array $relations = [], array $fields = ['*']): array
    {
        $product = $this->repo->find($productId);
        if (!$product) {
            return [];
        }
        
        return $this->repo->getBy(['product_id' => $productId], $relations, $fields);
    }

    /**
     * Override create to ensure slug generation
     */
    public function create($data): array
    {
        $data = $this->ensureSlug($data);
        return parent::create($data);
    }

    /**
     * Override update to ensure slug generation
     */
    public function update($id, $data): ?array
    {
        $data = $this->ensureSlug($data);
        return parent::update($id, $data);
    }
}
