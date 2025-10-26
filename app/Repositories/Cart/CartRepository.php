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
     * Get cart with items by cart header ID
     */
    public function getCartWithItems($cartHeaderId): ?array
    {
        // Get cart header
        $cartHeader = CartHeader::find($cartHeaderId);
        if (!$cartHeader) {
            return null;
        }

        // Get cart items
        $items = Cart::with(['product:id,name,sku,price,sale_price', 'variant:id,name,sku,price,sale_price'])
            ->where('cart_header_id', $cartHeaderId)
            ->get();

        if ($items->isEmpty()) {
            return [
                'cart_header_id' => $cartHeaderId,
                'items' => [],
                'subtotal' => 0,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'currency' => $cartHeader->currency ?? 'VND'
            ];
        }

        $cartArray = [
            'cart_header_id' => $cartHeaderId,
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'product' => $item->product,
                    'variant' => $item->variant,
                ];
            })->toArray()
        ];

        // Use cart header totals if available, otherwise calculate
        if ($cartHeader->total_amount > 0) {
            $cartArray['subtotal'] = $cartHeader->subtotal;
            $cartArray['tax_amount'] = $cartHeader->tax_amount;
            $cartArray['shipping_amount'] = $cartHeader->shipping_amount;
            $cartArray['discount_amount'] = $cartHeader->discount_amount;
            $cartArray['total_amount'] = $cartHeader->total_amount;
            $cartArray['currency'] = $cartHeader->currency;
        } else {
            // Calculate totals
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item->total_price;
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

            // Update cart header with calculated totals
            $cartHeader->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
            ]);
        }

        return $cartArray;
    }

    /**
     * Create a new empty cart
     */
    public function createEmptyCart($cartHeaderId): array
    {
        // First try to find existing cart header
        $cartHeader = CartHeader::find($cartHeaderId);
        
        // If not found, create a new one
        if (!$cartHeader) {
            $cartHeader = CartHeader::create([
                'id' => $cartHeaderId,
                'currency' => 'VND',
                'subtotal' => 0,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
            ]);
        }

        return [
            'cart_header_id' => $cartHeaderId,
            'items' => [],
            'subtotal' => 0,
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 0,
            'currency' => $cartHeader->currency
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
     * Add item to cart
     */
    public function addItem($cartHeaderId, array $itemData): array
    {
        // Get product and variant info
        $productId = $itemData['product_id'];
        $variantId = $itemData['product_variant_id'] ?? null;
        $quantity = $itemData['quantity'];

        // Get product info
        $product = \App\Models\Product::find($productId);
        if (!$product) {
            throw new \Exception('Product not found');
        }

        // Get variant info if provided
        $variant = null;
        if ($variantId) {
            $variant = \App\Models\ProductVariant::find($variantId);
            if (!$variant) {
                throw new \Exception('Product variant not found');
            }
        }

        // Determine price
        $unitPrice = $variant ? ($variant->sale_price ?? $variant->price) : ($product->sale_price ?? $product->price);
        $totalPrice = $unitPrice * $quantity;

        // Check if item already exists
        $existingItem = Cart::where('cart_header_id', $cartHeaderId)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($existingItem) {
            // Update quantity and price
            $newQuantity = $existingItem->quantity + $quantity;
            $newTotalPrice = $unitPrice * $newQuantity;
            
            $existingItem->update([
                'quantity' => $newQuantity,
                'total_price' => $newTotalPrice
            ]);
            
            return $existingItem->fresh()->toArray();
        } else {
            // Create new item
            $itemData = [
                'cart_header_id' => $cartHeaderId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'product_name' => $product->name,
                'product_sku' => $variant ? $variant->sku : $product->sku,
                'variant_name' => $variant ? $variant->name : null,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ];
            
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
     * Clear cart by cart header ID
     */
    public function clearCart($cartHeaderId): bool
    {
        // Delete all cart items for this cart header
        $deleted = Cart::where('cart_header_id', $cartHeaderId)->delete();
        
        // Reset cart header totals
        $cartHeader = CartHeader::find($cartHeaderId);
        if ($cartHeader) {
            $cartHeader->update([
                'subtotal' => 0,
                'tax_amount' => 0,
                'shipping_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
            ]);
        }
        
        return $deleted > 0;
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
    public function recalculateTotals($cartHeaderId): ?array
    {
        return $this->getCartWithItems($cartHeaderId);
    }

    /**
     * Get cart by cart header ID
     */
    public function getBySessionId($cartHeaderId, array $relations = [], array $fields = ['*']): array
    {
        return $this->getCartWithItems($cartHeaderId) ?? $this->createEmptyCart($cartHeaderId);
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
     * Clear cart by cart header ID
     */
    public function clearBySessionId($cartHeaderId): bool
    {
        return $this->clearCart($cartHeaderId);
    }

    /**
     * Get cart total by cart header ID
     */
    public function getCartTotal($cartHeaderId): array
    {
        $cartHeader = CartHeader::find($cartHeaderId);
        
        if ($cartHeader && $cartHeader->total_amount > 0) {
            $items = Cart::where('cart_header_id', $cartHeaderId)->get();
            
            return [
                'subtotal' => $cartHeader->subtotal,
                'total_items' => $items->sum('quantity'),
                'items_count' => $items->count(),
                'total_amount' => $cartHeader->total_amount
            ];
        }
        
        // Fallback to calculation if cart header totals are not set
        $items = Cart::with(['product:id,name,price,sale_price', 'variant:id,name,price,sale_price'])
            ->where('cart_header_id', $cartHeaderId)
            ->get();

        $subtotal = 0;
        $totalItems = 0;

        foreach ($items as $item) {
            $subtotal += $item->total_price;
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

        // Filter by cart header ID
        if (!empty($filters['cart_header_id'])) {
            $query->where('cart_header_id', $filters['cart_header_id']);
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
