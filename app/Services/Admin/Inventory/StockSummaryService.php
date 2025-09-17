<?php

namespace App\Services\Admin\Inventory;

use App\Models\ProductStockSummary;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StockSummaryService
{
    /**
     * Cập nhật tổng hợp cho một sản phẩm
     */
    public function updateProductSummary($productId)
    {
        $summary = DB::table('inventories')
            ->select([
                'product_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT warehouse_id) as warehouse_count'),
                DB::raw('SUM(CASE WHEN quantity <= 10 THEN 1 ELSE 0 END) as low_stock_count')
            ])
            ->where('product_id', $productId)
            ->groupBy('product_id')
            ->first();

        if ($summary) {
            ProductStockSummary::updateOrCreate(
                ['product_id' => $productId],
                [
                    'total_quantity' => $summary->total_quantity ?? 0,
                    'warehouse_count' => $summary->warehouse_count ?? 0,
                    'low_stock_count' => $summary->low_stock_count ?? 0,
                    'last_updated_at' => now()
                ]
            );
        } else {
            // Nếu không có inventory, tạo record với giá trị 0
            ProductStockSummary::updateOrCreate(
                ['product_id' => $productId],
                [
                    'total_quantity' => 0,
                    'warehouse_count' => 0,
                    'low_stock_count' => 0,
                    'last_updated_at' => now()
                ]
            );
        }

        // Clear cache
        $this->clearCache($productId);
    }

    /**
     * Cập nhật tổng hợp cho tất cả sản phẩm
     */
    public function updateAllSummaries(): void
    {
        $productIds = DB::table('products')->pluck('id');
        
        foreach ($productIds as $productId) {
            $this->updateProductSummary($productId);
        }
    }

    /**
     * Lấy danh sách tổng hợp với cache
     */
    public function getStockSummary($filters = [], $perPage = 20)
    {
        $cacheKey = 'stock_summary_' . md5(serialize($filters) . $perPage);
        
        return Cache::remember($cacheKey, 300, function () use ($filters, $perPage) {
            $query = ProductStockSummary::with(['product.brand:id,name', 'product.categories:id,name'])
                ->select([
                    'product_stock_summary.*',
                    'products.name',
                    'products.code',
                    'products.image',
                    'products.status',
                    'products.created_at',
                    'products.updated_at'
                ])
                ->join('products', 'product_stock_summary.product_id', '=', 'products.id');

            // Apply filters
            if (isset($filters['search']) && !empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('products.name', 'like', "%{$search}%")
                      ->orWhere('products.code', 'like', "%{$search}%");
                });
            }

            if (isset($filters['brand_id']) && !empty($filters['brand_id'])) {
                $query->where('products.brand_id', $filters['brand_id']);
            }

            if (isset($filters['category_id']) && !empty($filters['category_id'])) {
                $query->whereHas('product.categories', function($q) use ($filters) {
                    $q->where('categories.id', $filters['category_id']);
                });
            }

            if (isset($filters['low_stock']) && $filters['low_stock']) {
                $threshold = $filters['threshold'] ?? 10;
                $query->where('total_quantity', '<=', $threshold);
            }

            return $query->orderBy('total_quantity', 'asc')->paginate($perPage);
        });
    }

    /**
     * Lấy sản phẩm tồn kho thấp
     */
    public function getLowStockProducts($threshold = 10)
    {
        $cacheKey = "low_stock_products_{$threshold}";
        
        return Cache::remember($cacheKey, 300, function () use ($threshold) {
            return ProductStockSummary::with(['product.brand:id,name'])
                ->where('total_quantity', '<=', $threshold)
                ->orderBy('total_quantity', 'asc')
                ->get();
        });
    }

    /**
     * Clear cache
     */
    private function clearCache($productId = null)
    {
        if ($productId) {
            Cache::forget("stock_summary_product_{$productId}");
        }
        
        // Clear all stock summary cache
        Cache::flush();
    }
} 