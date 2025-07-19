<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public function model()
    {
        return Product::class;
    }
    // Có thể bổ sung các hàm truy vấn đặc thù cho sản phẩm ở đây

    public function filter(array $filters = [], $perPage = 15)
    {
        $query = $this->model->newQuery();
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        $query->orderByDesc('id');
        return $query->paginate($perPage);
    }
} 