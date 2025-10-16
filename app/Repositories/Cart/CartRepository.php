<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Repositories\BaseRepository;

class CartRepository extends BaseRepository
{
    public function model()
    {
        return Cart::class;
    }

    /**
     * Get cart by user ID
     */
    public function getByUserId($userId, array $relations = [], array $fields = ['*']): array
    {
        $query = $this->buildQuery($relations, $fields);
        $query->where('user_id', $userId);
        
        return $query->get()->toArray();
    }

    /**
     * Get cart by session ID
     */
    public function getBySessionId($sessionId, array $relations = [], array $fields = ['*']): array
    {
        $query = $this->buildQuery($relations, $fields);
        $query->where('session_id', $sessionId);
        
        return $query->get()->toArray();
    }

    /**
     * Add item to cart
     */
    public function addItem(array $data): array
    {
        // Check if item already exists
        $existingItem = $this->model->where('user_id', $data['user_id'])
            ->where('product_id', $data['product_id'])
            ->where('product_variant_id', $data['product_variant_id'] ?? null)
            ->first();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $data['quantity'];
            return $this->update($existingItem->id, ['quantity' => $newQuantity]);
        } else {
            // Create new item
            return $this->create($data);
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($id, int $quantity): ?array
    {
        if ($quantity <= 0) {
            $this->delete($id);
            return null;
        }
        
        return $this->update($id, ['quantity' => $quantity]);
    }

    /**
     * Clear cart by user ID
     */
    public function clearByUserId($userId): bool
    {
        return $this->model->where('user_id', $userId)->delete() > 0;
    }

    /**
     * Clear cart by session ID
     */
    public function clearBySessionId($sessionId): bool
    {
        return $this->model->where('session_id', $sessionId)->delete() > 0;
    }

    /**
     * Get cart total
     */
    public function getCartTotal($userId = null, $sessionId = null): array
    {
        $query = $this->buildQuery(['product:id,name,price,sale_price', 'variant:id,name,price,sale_price']);
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        $items = $query->get();
        
        $subtotal = 0;
        $totalItems = 0;
        
        foreach ($items as $item) {
            $price = $item->variant 
                ? ($item->variant->sale_price ?? $item->variant->price)
                : ($item->product->sale_price ?? $item->product->price);
            
            $subtotal += $price * $item->quantity;
            $totalItems += $item->quantity;
        }
        
        return [
            'subtotal' => $subtotal,
            'total_items' => $totalItems,
            'items_count' => $items->count()
        ];
    }

    /**
     * Apply filters specific to cart
     */
    protected function applyFilters(\Illuminate\Database\Eloquent\Builder $query, array $filters): void
    {
        parent::applyFilters($query, $filters);
        
        // Filter by user ID
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        // Filter by session ID
        if (!empty($filters['session_id'])) {
            $query->where('session_id', $filters['session_id']);
        }
        
        // Filter by product ID
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        
        // Filter by variant ID
        if (!empty($filters['product_variant_id'])) {
            $query->where('product_variant_id', $filters['product_variant_id']);
        }
    }
}
