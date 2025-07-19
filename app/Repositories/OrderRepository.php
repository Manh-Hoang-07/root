<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends BaseRepository
{
    public function model()
    {
        return Order::class;
    }

    public function filter(array $filters = [], $perPage = 15)
    {
        $query = $this->model->newQuery();
        if (!empty($filters['search'])) {
            $query->where('id', $filters['search'])
                  ->orWhere('note', 'like', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        $query->orderByDesc('id');
        return $query->paginate($perPage);
    }
} 