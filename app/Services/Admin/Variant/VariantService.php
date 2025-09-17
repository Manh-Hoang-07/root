<?php

namespace App\Services\Admin\Variant;

use App\Repositories\Variant\VariantRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class VariantService extends BaseService
{
    public function __construct(VariantRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Get variants by product
     */
    public function getVariantsByProduct($productId): array
    {
        return $this->repo->getVariantsByProduct($productId);
    }

    /**
     * Create variant with attribute values
     */
    public function createVariant($data): array
    {
        return DB::transaction(function () use ($data) {
            $variant = $this->repo->create($data);
            
            // Get the model instance for relationships
            $variantModel = $this->repo->getModel()->find($variant['id']);
            
            // Attach attribute values if provided
            if (isset($data['attribute_values']) && is_array($data['attribute_values'])) {
                $attributeValues = [];
                foreach ($data['attribute_values'] as $attrId => $valueId) {
                    if ($valueId) {
                        $attributeValues[] = $valueId;
                    }
                }
                $variantModel->attributeValues()->attach($attributeValues);
            }
            
            return $variant;
        });
    }

    /**
     * Update variant with attribute values
     */
    public function updateVariant($id, $data): ?array
    {
        return DB::transaction(function () use ($id, $data) {
            $variant = $this->repo->update($id, $data);
            
            if (!$variant) {
                return null;
            }
            
            // Get the model instance for relationships
            $variantModel = $this->repo->getModel()->find($variant['id']);
            
            // Sync attribute values if provided
            if (isset($data['attribute_values']) && is_array($data['attribute_values'])) {
                $attributeValues = [];
                foreach ($data['attribute_values'] as $attrId => $valueId) {
                    if ($valueId) {
                        $attributeValues[] = $valueId;
                    }
                }
                $variantModel->attributeValues()->sync($attributeValues);
            }
            
            return $variant;
        });
    }
} 