<?php

namespace App\Services\Variant;

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
    public function getVariantsByProduct($productId)
    {
        return $this->repo->getVariantsByProduct($productId);
    }

    /**
     * Create variant with attribute values
     */
    public function createVariant($data)
    {
        return DB::transaction(function () use ($data) {
            $variant = $this->repo->create($data);
            
            // Attach attribute values if provided
            if (isset($data['attribute_values']) && is_array($data['attribute_values'])) {
                $attributeValues = [];
                foreach ($data['attribute_values'] as $attrId => $valueId) {
                    if ($valueId) {
                        $attributeValues[] = $valueId;
                    }
                }
                $variant->attributeValues()->attach($attributeValues);
            }
            
            return $variant;
        });
    }

    /**
     * Update variant with attribute values
     */
    public function updateVariant($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $variant = $this->repo->update($id, $data);
            
            // Sync attribute values if provided
            if (isset($data['attribute_values']) && is_array($data['attribute_values'])) {
                $attributeValues = [];
                foreach ($data['attribute_values'] as $attrId => $valueId) {
                    if ($valueId) {
                        $attributeValues[] = $valueId;
                    }
                }
                $variant->attributeValues()->sync($attributeValues);
            }
            
            return $variant;
        });
    }
} 