<?php
namespace App\Services\Product;

use App\Repositories\Product\ProductRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class ProductService extends BaseService
{
    public function __construct(ProductRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Get products with relationships
     */
    public function getProductsWithRelations($filters = [])
    {
        return $this->repo->getProductsWithRelations($filters);
    }

    /**
     * List products with filters and pagination
     */
    public function list($filters = [], $perPage = 20, $relations = [], $fields = ['*'])
    {
        return $this->repo->all($filters, $perPage, $relations, $fields);
    }

    /**
     * Create product with categories, variants and images
     */
    public function createProduct($data)
    {
        return DB::transaction(function () use ($data) {
            // Auto-generate slug from name if not provided
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
            }
            
            // Process attributes if provided - keep full information
            if (isset($data['attributes']) && is_array($data['attributes'])) {
                // Keep the full array format with all details
                $data['attributes'] = $data['attributes'];
            }
            
            $product = $this->repo->create($data);
            
            // Attach categories if provided
            if (isset($data['categories']) && is_array($data['categories'])) {
                $product->categories()->attach($data['categories']);
            }
            
            // Create variants if provided
            if (isset($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $variantData['product_id'] = $product->id;
                    $variant = $product->variants()->create($variantData);
                    
                    // Attach attribute values if provided
                    if (isset($variantData['attribute_values']) && is_array($variantData['attribute_values'])) {
                        $attributeValues = [];
                        foreach ($variantData['attribute_values'] as $attrId => $valueId) {
                            if ($valueId) {
                                $attributeValues[] = $valueId;
                            }
                        }
                        $variant->attributeValues()->attach($attributeValues);
                    }
                }
            }
            
            // Create images if provided
            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $imageData) {
                    $product->images()->create([
                        'url' => $imageData['url'],
                        'imageable_type' => 'App\\Models\\Product',
                        'imageable_id' => $product->id,
                    ]);
                }
            }
            
            return $product;
        });
    }

    /**
     * Update product with categories, variants and images
     */
    public function updateProduct($id, $data)
    {
        return DB::transaction(function () use ($id, $data) {
            // Auto-generate slug from name if not provided
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
            }
            
            // Process attributes if provided - keep full information
            if (isset($data['attributes']) && is_array($data['attributes'])) {
                // Keep the full array format with all details
                $data['attributes'] = $data['attributes'];
            }
            
            $product = $this->repo->update($id, $data);
            
            // Sync categories if provided
            if (isset($data['categories']) && is_array($data['categories'])) {
                $product->categories()->sync($data['categories']);
            }
            
            // Update variants if provided
            if (isset($data['variants']) && is_array($data['variants'])) {
                // Delete existing variants
                $product->variants()->delete();
                
                // Create new variants
                foreach ($data['variants'] as $variantData) {
                    $variantData['product_id'] = $product->id;
                    $variant = $product->variants()->create($variantData);
                    
                    // Attach attribute values if provided
                    if (isset($variantData['attribute_values']) && is_array($variantData['attribute_values'])) {
                        $attributeValues = [];
                        foreach ($variantData['attribute_values'] as $attrId => $valueId) {
                            if ($valueId) {
                                $attributeValues[] = $valueId;
                            }
                        }
                        $variant->attributeValues()->attach($attributeValues);
                    }
                }
            }
            
            // Update images - always process to ensure sync
            // Delete existing images
            $product->images()->delete();
            
            // Create new images if provided
            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $imageData) {
                    $product->images()->create([
                        'url' => $imageData['url'],
                        'imageable_type' => 'App\\Models\\Product',
                        'imageable_id' => $product->id,
                    ]);
                }
            }
            
            return $product;
        });
    }

    /**
     * Get product with all relationships
     */
    public function getProductWithRelations($id)
    {
        return $this->repo->getProductWithRelations($id);
    }

    /**
     * Get products for dropdown/select
     */
    public function getProductsForSelect()
    {
        return $this->repo->getProductsForSelect();
    }
} 