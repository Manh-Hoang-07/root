<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{
    public function model()
    {
        return Product::class;
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(array $relations = [], array $fields = ['*']): array
    {
        $query = $this->buildQuery($relations, $fields);
        $query->whereRaw('stock_quantity <= min_stock_level');
        
        return $query->get()->toArray();
    }


    /**
     * Update product status
     */
    public function updateStatus($id, string $status): ?array
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id): ?array
    {
        $product = $this->model->find($id);
        if (!$product) {
            return null;
        }
        
        return $this->update($id, ['is_featured' => !$product->is_featured]);
    }

    /**
     * Bulk update products
     */
    public function bulkUpdate(array $ids, string $action, $value = null): array
    {
        $data = [];
        
        switch ($action) {
            case 'activate':
                $data['status'] = 'active';
                break;
            case 'deactivate':
                $data['status'] = 'inactive';
                break;
            case 'featured':
                $data['is_featured'] = true;
                break;
            case 'unfeatured':
                $data['is_featured'] = false;
                break;
            case 'delete':
                return $this->bulkDelete($ids);
        }
        
        if (!empty($data)) {
            $updated = $this->model->whereIn('id', $ids)->update($data);
            return ['updated' => $updated, 'ids' => $ids];
        }
        
        return ['updated' => 0, 'ids' => $ids];
    }

    /**
     * Bulk delete products
     */
    public function bulkDelete(array $ids): array
    {
        $deleted = 0;
        foreach ($ids as $id) {
            if ($this->delete($id)) {
                $deleted++;
            }
        }
        
        return ['deleted' => $deleted, 'ids' => $ids];
    }
}
