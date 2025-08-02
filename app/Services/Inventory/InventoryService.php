<?php

namespace App\Services\Inventory;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Warehouse;
use App\Repositories\Inventory\InventoryRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class InventoryService
{
    protected $inventoryRepository;

    public function __construct(InventoryRepository $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Lấy danh sách tồn kho với phân trang và bộ lọc
     */
    public function list(array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*']): LengthAwarePaginator
    {
        return $this->inventoryRepository->all($filters, $perPage, $relations, $fields);
    }

    /**
     * Lấy danh sách tồn kho với relationships
     */
    public function getInventories(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->inventoryRepository->getInventoriesWithRelations($filters, $perPage);
    }

    /**
     * Tìm tồn kho theo ID
     */
    public function find(int $id, array $relations = [], array $fields = ['*']): Inventory
    {
        return $this->inventoryRepository->find($id, $relations, $fields);
    }

    /**
     * Tìm tồn kho theo ID với relationships
     */
    public function findById(int $id): ?Inventory
    {
        return Inventory::with(['product.brand', 'warehouse'])->find($id);
    }

    /**
     * Tạo tồn kho mới
     */
    public function create(array $data): Inventory
    {
        // Tự động tính available_quantity nếu không được cung cấp
        if (!isset($data['available_quantity'])) {
            $data['available_quantity'] = $data['quantity'] - ($data['reserved_quantity'] ?? 0);
        }

        return $this->inventoryRepository->create($data);
    }

    /**
     * Cập nhật tồn kho
     */
    public function update(int $id, array $data): Inventory
    {
        // Tự động tính available_quantity nếu không được cung cấp
        if (!isset($data['available_quantity'])) {
            $inventory = $this->find($id);
            $data['available_quantity'] = $data['quantity'] - ($data['reserved_quantity'] ?? $inventory->reserved_quantity);
        }

        return $this->inventoryRepository->update($id, $data);
    }

    /**
     * Xóa tồn kho
     */
    public function delete(int $id): bool
    {
        return $this->inventoryRepository->delete($id);
    }

    /**
     * Nhập kho (tăng số lượng)
     */
    public function import(int $productId, int $warehouseId, int $quantity, array $data = []): Inventory
    {
        $inventory = $this->inventoryRepository->findByProductAndWarehouse($productId, $warehouseId, $data['batch_no'] ?? null);
        
        if ($inventory) {
            // Cập nhật tồn kho hiện có
            $inventory->quantity += $quantity;
            $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
            
            // Cập nhật thông tin lô hàng nếu có
            if (isset($data['batch_no'])) $inventory->batch_no = $data['batch_no'];
            if (isset($data['lot_no'])) $inventory->lot_no = $data['lot_no'];
            if (isset($data['expiry_date'])) $inventory->expiry_date = $data['expiry_date'];
            if (isset($data['cost_price'])) $inventory->cost_price = $data['cost_price'];
            
            $inventory->save();
        } else {
            // Tạo tồn kho mới
            $inventory = $this->create([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'available_quantity' => $quantity,
                'batch_no' => $data['batch_no'] ?? null,
                'lot_no' => $data['lot_no'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? null,
                'cost_price' => $data['cost_price'] ?? null,
            ]);
        }
        
        return $inventory;
    }

    /**
     * Xuất kho (giảm số lượng)
     */
    public function export(int $productId, int $warehouseId, int $quantity, ?string $batchNo = null): Inventory
    {
        $inventory = $this->inventoryRepository->findByProductAndWarehouse($productId, $warehouseId, $batchNo);
        
        if (!$inventory) {
            throw new \Exception('Không tìm thấy tồn kho');
        }
        
        if ($inventory->quantity < $quantity) {
            throw new \Exception('Số lượng trong kho không đủ');
        }
        
        $inventory->quantity -= $quantity;
        $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
        $inventory->save();
        
        return $inventory;
    }



    /**
     * Lấy hàng sắp hết hạn
     */
    public function getExpiringSoon(int $days = 30): Collection
    {
        return $this->inventoryRepository->getExpiringSoon($days);
    }

    /**
     * Lấy hàng đã hết hạn
     */
    public function getExpired(): Collection
    {
        return $this->inventoryRepository->getExpired();
    }

    /**
     * Lấy hàng sắp hết
     */
    public function getLowStock(int $threshold = 10): Collection
    {
        return $this->inventoryRepository->getLowStock($threshold);
    }

    /**
     * Lấy tùy chọn bộ lọc
     */
    public function getFilterOptions(): array
    {
        try {
            return [
                'brands' => Brand::select('id', 'name')->get(),
                'categories' => Category::select('id', 'name')->get(),
                'warehouses' => Warehouse::select('id', 'name')->get(),
                'products' => Product::select('id', 'name', 'code')->get(),
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in getFilterOptions: ' . $e->getMessage());
            return [
                'brands' => [],
                'categories' => [],
                'warehouses' => [],
                'products' => [],
            ];
        }
    }

    /**
     * Giữ chỗ số lượng cho đơn hàng
     */
    public function reserveQuantity(int $productId, int $warehouseId, int $quantity, ?string $batchNo = null): bool
    {
        $inventory = $this->inventoryRepository->findByProductAndWarehouse($productId, $warehouseId, $batchNo);
        
        if (!$inventory) {
            throw new \Exception('Không tìm thấy tồn kho');
        }
        
        if ($inventory->available_quantity < $quantity) {
            throw new \Exception('Số lượng có thể bán không đủ');
        }
        
        $inventory->reserved_quantity += $quantity;
        $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
        $inventory->save();
        
        return true;
    }

    /**
     * Giải phóng số lượng đã giữ chỗ
     */
    public function releaseQuantity(int $productId, int $warehouseId, int $quantity, ?string $batchNo = null): bool
    {
        $inventory = $this->inventoryRepository->findByProductAndWarehouse($productId, $warehouseId, $batchNo);
        
        if (!$inventory) {
            throw new \Exception('Không tìm thấy tồn kho');
        }
        
        if ($inventory->reserved_quantity < $quantity) {
            throw new \Exception('Số lượng đã giữ chỗ không đủ');
        }
        
        $inventory->reserved_quantity -= $quantity;
        $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
        $inventory->save();
        
        return true;
    }
} 