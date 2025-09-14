<?php

namespace App\Repositories\Inventory;

use App\Repositories\BaseRepository;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class InventoryRepository extends BaseRepository
{
    public function model(): string
    {
        return Inventory::class;
    }

    /**
     * Lấy danh sách tồn kho với relationships
     */
    public function getInventoriesWithRelations(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with(['product', 'variant', 'warehouse']);

        // Áp dụng filters
        $this->applyFilters($query, $filters);

        // Sắp xếp
        if (!empty($filters['sort_by'])) {
            $this->applyCustomSorting($query, $filters['sort_by']);
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Tìm tồn kho theo product_id và warehouse_id
     */
    public function findByProductAndWarehouse(int $productId, int $warehouseId, ?string $batchNo = null): ?Inventory
    {
        $query = $this->model
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId);

        if ($batchNo) {
            $query->where('batch_no', $batchNo);
        }

        return $query->first();
    }

    /**
     * Lấy tồn kho theo batch_no
     */
    public function findByBatchNo(string $batchNo): Collection
    {
        return $this->model
            ->where('batch_no', $batchNo)
            ->with(['product', 'warehouse'])
            ->get();
    }

    /**
     * Lấy tồn kho sắp hết hạn
     */
    public function getExpiringSoon(int $days = 30): Collection
    {
        return $this->model
            ->with(['product', 'warehouse'])
            ->expiringSoon($days)
            ->get();
    }

    /**
     * Lấy tồn kho đã hết hạn
     */
    public function getExpired(): Collection
    {
        return $this->model
            ->with(['product', 'warehouse'])
            ->expired()
            ->get();
    }

    /**
     * Lấy tồn kho sắp hết
     */
    public function getLowStock(int $threshold = 10): Collection
    {
        return $this->model
            ->with(['product', 'warehouse'])
            ->lowStock($threshold)
            ->get();
    }

    /**
     * Lấy tồn kho hết hàng
     */
    public function getOutOfStock(): Collection
    {
        return $this->model
            ->with(['product', 'warehouse'])
            ->outOfStock()
            ->get();
    }

    /**
     * Cập nhật số lượng tồn kho
     */
    public function updateQuantity(int $id, int $quantity): bool
    {
        $inventory = $this->find($id);
        
        if (!$inventory) {
            return false;
        }

        $inventory->quantity = $quantity;
        $inventory->available_quantity = $quantity - $inventory->reserved_quantity;
        
        return $inventory->save();
    }

    /**
     * Cập nhật số lượng đã giữ chỗ
     */
    public function updateReservedQuantity(int $id, int $reservedQuantity): bool
    {
        $inventory = $this->find($id);
        
        if (!$inventory) {
            return false;
        }

        $inventory->reserved_quantity = $reservedQuantity;
        $inventory->available_quantity = $inventory->quantity - $reservedQuantity;
        
        return $inventory->save();
    }

    /**
     * Tăng số lượng đã giữ chỗ
     */
    public function increaseReservedQuantity(int $id, int $quantity): bool
    {
        $inventory = $this->find($id);
        
        if (!$inventory) {
            return false;
        }

        $inventory->reserved_quantity += $quantity;
        $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
        
        return $inventory->save();
    }

    /**
     * Giảm số lượng đã giữ chỗ
     */
    public function decreaseReservedQuantity(int $id, int $quantity): bool
    {
        $inventory = $this->find($id);
        
        if (!$inventory) {
            return false;
        }

        $inventory->reserved_quantity -= $quantity;
        $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
        
        return $inventory->save();
    }

    /**
     * Tăng số lượng tồn kho
     */
    public function increaseQuantity(int $id, int $quantity): bool
    {
        $inventory = $this->find($id);
        
        if (!$inventory) {
            return false;
        }

        $inventory->quantity += $quantity;
        $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
        
        return $inventory->save();
    }

    /**
     * Giảm số lượng tồn kho
     */
    public function decreaseQuantity(int $id, int $quantity): bool
    {
        $inventory = $this->find($id);
        
        if (!$inventory) {
            return false;
        }

        $inventory->quantity -= $quantity;
        $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
        
        return $inventory->save();
    }



    /**
     * Áp dụng filters cho query
     */
    private function applyFilters($query, array $filters): void
    {
        // Tìm kiếm theo tên sản phẩm
        if (!empty($filters['search'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('code', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Lọc theo kho hàng
        if (!empty($filters['warehouse_id'])) {
            $query->byWarehouse($filters['warehouse_id']);
        }

        // Lọc theo sản phẩm
        if (!empty($filters['product_id'])) {
            $query->byProduct($filters['product_id']);
        }

        // Lọc theo thương hiệu
        if (!empty($filters['brand_id'])) {
            $query->byBrand($filters['brand_id']);
        }

        // Lọc theo danh mục
        if (!empty($filters['category_id'])) {
            $query->byCategory($filters['category_id']);
        }

        // Lọc theo trạng thái tồn kho
        if (!empty($filters['stock_status'])) {
            switch ($filters['stock_status']) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
                case 'in_stock':
                    $query->where('available_quantity', '>', 10);
                    break;
            }
        }

        // Lọc theo trạng thái hạn sử dụng
        if (!empty($filters['expiry_status'])) {
            switch ($filters['expiry_status']) {
                case 'expiring_soon':
                    $query->expiringSoon();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'valid':
                    $query->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>', \Carbon\Carbon::now()->addDays(30));
                    });
                    break;
            }
        }
    }

    /**
     * Áp dụng sắp xếp cho query
     */
    protected function applyCustomSorting(\Illuminate\Database\Eloquent\Builder $query, string $sortBy): void
    {
        $parts = explode('_', $sortBy);
        if (count($parts) >= 2) {
            $direction = array_pop($parts);
            $field = implode('_', $parts);
            
            // Xử lý các trường sắp xếp đặc biệt
            switch ($field) {
                case 'product_name':
                    $query->join('products', 'inventories.product_id', '=', 'products.id')
                          ->orderBy('products.name', $direction);
                    break;
                case 'warehouse_name':
                    $query->join('warehouses', 'inventories.warehouse_id', '=', 'warehouses.id')
                          ->orderBy('warehouses.name', $direction);
                    break;
                case 'expiry_date':
                    $query->orderBy('expiry_date', $direction);
                    break;
                case 'available_quantity':
                    $query->orderBy('available_quantity', $direction);
                    break;
                default:
                    if (in_array($field, $this->model->getFillable()) || 
                        in_array($field, ['id', 'created_at', 'updated_at'])) {
                        $query->orderBy($field, $direction);
                    }
                    break;
            }
        }
    }
} 