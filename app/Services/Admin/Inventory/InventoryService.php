<?php

namespace App\Services\Admin\Inventory;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Warehouse;
use App\Repositories\Inventory\InventoryRepository;
use App\Services\BaseService;
 

class InventoryService extends BaseService
{
    protected $inventoryRepository;

    public function __construct(InventoryRepository $inventoryRepository)
    {
        parent::__construct($inventoryRepository);
        $this->inventoryRepository = $inventoryRepository;
    }

    /**
     * Lấy danh sách tồn kho với relationships
     */
    public function getInventories(array $filters = [], int $perPage = 15): array
    {
        return $this->inventoryRepository->getInventoriesWithRelations($filters, $perPage);
    }

    /**
     * Tìm tồn kho theo ID với relationships
     */
    public function findById(int $id): ?array
    {
        $inventory = Inventory::with(['product.brand', 'variant', 'warehouse'])->find($id);
        return $inventory ? $inventory->toArray() : null;
    }

    /**
     * Tạo tồn kho mới
     */
    public function create($data): array
    {
        // Validate business logic
        $this->validateBusinessLogic($data);

        // Tự động tính available_quantity nếu không được cung cấp
        if (!isset($data['available_quantity'])) {
            $data['available_quantity'] = $data['quantity'] - ($data['reserved_quantity'] ?? 0);
        }

        return parent::create($data);
    }

    /**
     * Cập nhật tồn kho
     */
    public function update($id, $data): ?array
    {
        // Validate business logic
        $this->validateBusinessLogic($data);

        // Tự động tính available_quantity nếu không được cung cấp
        if (!isset($data['available_quantity'])) {
            $inventory = $this->find($id);
            if ($inventory) {
                $data['available_quantity'] = $data['quantity'] - ($data['reserved_quantity'] ?? $inventory['reserved_quantity']);
            }
        }

        return parent::update($id, $data);
    }

    /**
     * Xóa tồn kho
     */
    // Sử dụng delete() từ BaseService

    /**
     * Nhập kho (tăng số lượng)
     */
    public function import(int $productId, int $warehouseId, int $quantity, array $data = []): array
    {
        $inventory = $this->inventoryRepository->findByProductAndWarehouse($productId, $warehouseId, $data['batch_no'] ?? null);
        
        if ($inventory) {
            // Get the model instance for updates
            $inventoryModel = Inventory::find($inventory['id']);
            
            // Cập nhật tồn kho hiện có
            $inventoryModel->quantity += $quantity;
            $inventoryModel->available_quantity = $inventoryModel->quantity - $inventoryModel->reserved_quantity;
            
            // Cập nhật thông tin lô hàng nếu có
            if (isset($data['batch_no'])) $inventoryModel->batch_no = $data['batch_no'];
            if (isset($data['lot_no'])) $inventoryModel->lot_no = $data['lot_no'];
            if (isset($data['expiry_date'])) $inventoryModel->expiry_date = $data['expiry_date'];
            if (isset($data['cost_price'])) $inventoryModel->cost_price = $data['cost_price'];
            
            $inventoryModel->save();
            
            return $inventoryModel->toArray();
        } else {
            // Tạo tồn kho mới
            return $this->create([
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
    }

    /**
     * Xuất kho (giảm số lượng)
     */
    public function export(int $productId, int $warehouseId, int $quantity, ?string $batchNo = null): array
    {
        $inventory = $this->inventoryRepository->findByProductAndWarehouse($productId, $warehouseId, $batchNo);
        
        if (!$inventory) {
            throw new \Exception('Không tìm thấy tồn kho');
        }
        
        if ($inventory['quantity'] < $quantity) {
            throw new \Exception('Số lượng trong kho không đủ');
        }
        
        // Get the model instance for updates
        $inventoryModel = Inventory::find($inventory['id']);
        
        $inventoryModel->quantity -= $quantity;
        $inventoryModel->available_quantity = $inventoryModel->quantity - $inventoryModel->reserved_quantity;
        $inventoryModel->save();
        
        return $inventoryModel->toArray();
    }



    /**
     * Lấy hàng sắp hết hạn
     */
    public function getExpiringSoon(int $days = 30): array
    {
        return $this->inventoryRepository->getExpiringSoon($days);
    }

    /**
     * Lấy hàng đã hết hạn
     */
    public function getExpired(): array
    {
        return $this->inventoryRepository->getExpired();
    }

    /**
     * Lấy hàng sắp hết
     */
    public function getLowStock(int $threshold = 10): array
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
                'brands' => Brand::select('id', 'name')->get()->toArray(),
                'categories' => Category::select('id', 'name')->get()->toArray(),
                'warehouses' => Warehouse::select('id', 'name')->get()->toArray(),
                'products' => Product::select('id', 'name', 'code')->get()->toArray(),
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
        
        if ($inventory['available_quantity'] < $quantity) {
            throw new \Exception('Số lượng có thể bán không đủ');
        }
        
        // Get the model instance for updates
        $inventoryModel = Inventory::find($inventory['id']);
        
        $inventoryModel->reserved_quantity += $quantity;
        $inventoryModel->available_quantity = $inventoryModel->quantity - $inventoryModel->reserved_quantity;
        $inventoryModel->save();
        
        return true;
    }

    /**
     * Validate business logic
     */
    private function validateBusinessLogic(array $data): void
    {
        $quantity = (int) ($data['quantity'] ?? 0);
        $reservedQuantity = (int) ($data['reserved_quantity'] ?? 0);
        $availableQuantity = (int) ($data['available_quantity'] ?? 0);

        // Kiểm tra reserved_quantity không được lớn hơn quantity
        if ($reservedQuantity > $quantity) {
            throw new \Exception('Số lượng đã giữ chỗ không được lớn hơn tổng số lượng.');
        }

        // Kiểm tra available_quantity
        if (isset($data['available_quantity'])) {
            $expectedAvailable = $quantity - $reservedQuantity;
            if ($availableQuantity > $expectedAvailable) {
                throw new \Exception('Số lượng có thể bán không được lớn hơn số lượng thực tế.');
            }
        }
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
        
        if ($inventory['reserved_quantity'] < $quantity) {
            throw new \Exception('Số lượng đã giữ chỗ không đủ');
        }
        
        // Get the model instance for updates
        $inventoryModel = Inventory::find($inventory['id']);
        
        $inventoryModel->reserved_quantity -= $quantity;
        $inventoryModel->available_quantity = $inventoryModel->quantity - $inventoryModel->reserved_quantity;
        $inventoryModel->save();
        
        return true;
    }
} 