<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\CartHeader;
use App\Repositories\BaseRepository;

class CartRepository extends BaseRepository
{
    public function model()
    {
        return Cart::class;
    }

    /**
     * Get cart with items by session ID or user ID
     */
    public function getCartWithItems($cartId): ?array
    {
        // Determine if cartId is for user or session
        $isUserCart = strpos($cartId, 'user_') === 0;
        $userId = $isUserCart ? (int)substr($cartId, 5) : null;
        $sessionId = $isUserCart ? null : $cartId;

        // Build query based on cart type
        $query = Cart::with(['product:id,name,sku,price,sale_price', 'variant:id,name,sku,price,sale_price']);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return null;
        }

        $cartArray = [
            'cart_id' => $cartId,
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'product' => $item->product,
                    'variant' => $item->variant,
                ];
            })->toArray()
        ];

        // Calculate totals
        $subtotal = 0;
        foreach ($items as $item) {
            $price = $item->variant
                ? ($item->variant->sale_price ?? $item->variant->price)
                : ($item->product->sale_price ?? $item->product->price);
            $subtotal += $price * $item->quantity;
        }

        $taxAmount = $subtotal * 0.1; // 10% tax
        $shippingAmount = 30000; // Fixed shipping
        $totalAmount = $subtotal + $taxAmount + $shippingAmount;

        $cartArray['subtotal'] = $subtotal;
        $cartArray['tax_amount'] = $taxAmount;
        $cartArray['shipping_amount'] = $shippingAmount;
        $cartArray['discount_amount'] = 0;
        $cartArray['total_amount'] = $totalAmount;
        $cartArray['currency'] = 'VND';

        return $cartArray;
    }

    /**
     * Create a new empty cart
     */
    public function createEmptyCart($cartId): array
    {
        return [
            'cart_id' => $cartId,
            'items' => [],
            'subtotal' => 0,
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 0,
            'currency' => 'VND'
        ];
    }

    /**
     * Get cart item by ID
     */
    public function getCartItem($itemId): ?array
    {
        $item = Cart::find($itemId);
        return $item ? $item->toArray() : null;
    }

    /**
     * Add item to cart by session ID or user ID
     */
    public function addItem($cartId, array $itemData): array
    {
        // Determine if cartId is for user or session
        $isUserCart = strpos($cartId, 'user_') === 0;
        $userId = $isUserCart ? (int)substr($cartId, 5) : null;
        $sessionId = $isUserCart ? null : $cartId;

        // Build query based on cart type
        $query = Cart::where('product_id', $itemData['product_id'])
            ->where('product_variant_id', $itemData['product_variant_id'] ?? null);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $existingItem = $query->first();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $itemData['quantity'];
            $existingItem->update(['quantity' => $newQuantity]);
            return $existingItem->fresh()->toArray();
        } else {
            // Create new item with appropriate fields
            if ($userId) {
                $itemData['user_id'] = $userId;
            } else {
                $itemData['session_id'] = $sessionId;
            }
            $item = Cart::create($itemData);
            return $item->toArray();
        }
    }

    /**
     * Update cart item
     */
    public function updateItem($itemId, array $data): bool
    {
        return Cart::where('id', $itemId)->update($data) > 0;
    }

    /**
     * Remove cart item
     */
    public function removeItem($itemId): bool
    {
        return Cart::where('id', $itemId)->delete() > 0;
    }

    /**
     * Clear cart by session ID or user ID
     */
    public function clearCart($cartId): bool
    {
        // Determine if cartId is for user or session
        $isUserCart = strpos($cartId, 'user_') === 0;
        $userId = $isUserCart ? (int)substr($cartId, 5) : null;
        $sessionId = $isUserCart ? null : $cartId;

        // Build query based on cart type
        $query = Cart::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->delete() > 0;
    }

    /**
     * Update cart (not applicable for current structure)
     */
    public function updateCart($sessionId, array $data): bool
    {
        // Not needed for current cart structure
        return true;
    }

    /**
     * Recalculate cart totals
     */
    public function recalculateTotals($sessionId): ?array
    {
        return $this->getCartWithItems($sessionId);
    }

    /**
     * Get cart by session ID or user ID
     */
    public function getBySessionId($cartId, array $relations = [], array $fields = ['*']): array
    {
        return $this->getCartWithItems($cartId) ?? $this->createEmptyCart($cartId);
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($id, int $quantity): ?array
    {
        if ($quantity <= 0) {
            Cart::where('id', $id)->delete();
            return null;
        }

        Cart::where('id', $id)->update(['quantity' => $quantity]);
        return Cart::find($id)?->toArray();
    }

    /**
     * Clear cart by session ID or user ID
     */
    public function clearBySessionId($cartId): bool
    {
        return $this->clearCart($cartId);
    }

    /**
     * Get cart total by session ID or user ID
     */
    public function getCartTotal($cartId): array
    {
        // Determine if cartId is for user or session
        $isUserCart = strpos($cartId, 'user_') === 0;
        $userId = $isUserCart ? (int)substr($cartId, 5) : null;
        $sessionId = $isUserCart ? null : $cartId;

        // Build query based on cart type
        $query = Cart::with(['product:id,name,price,sale_price', 'variant:id,name,price,sale_price']);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
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
