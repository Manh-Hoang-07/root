<?php

namespace App\Services\Admin\Inventory;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryManagementService
{
    /**
     * Giữ chỗ số lượng cho đơn hàng
     */
    public function reserveQuantity(int $productId, int $warehouseId, int $quantity, ?string $batchNo = null): bool
    {
        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $batchNo) {
            $inventory = $this->findAvailableInventory($productId, $warehouseId, $quantity, $batchNo);
            
            if (!$inventory) {
                throw new \Exception('Không đủ hàng trong kho');
            }
            
            // Kiểm tra số lượng có thể giữ chỗ
            if ($inventory->available_quantity < $quantity) {
                throw new \Exception('Số lượng có thể bán không đủ');
            }
            
            // Cập nhật số lượng đã giữ chỗ
            $inventory->reserved_quantity += $quantity;
            $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
            $inventory->save();
            
            return true;
        });
    }
    
    /**
     * Giải phóng số lượng đã giữ chỗ
     */
    public function releaseQuantity(int $productId, int $warehouseId, int $quantity, ?string $batchNo = null): bool
    {
        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $batchNo) {
            $inventory = $this->findInventory($productId, $warehouseId, $batchNo);
            
            if (!$inventory) {
                throw new \Exception('Không tìm thấy tồn kho');
            }
            
            // Kiểm tra số lượng đã giữ chỗ
            if ($inventory->reserved_quantity < $quantity) {
                throw new \Exception('Số lượng đã giữ chỗ không đủ');
            }
            
            // Giải phóng số lượng
            $inventory->reserved_quantity -= $quantity;
            $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
            $inventory->save();
            
            return true;
        });
    }
    
    /**
     * Xuất kho (giảm số lượng thực tế)
     */
    public function reduceQuantity(int $productId, int $warehouseId, int $quantity, ?string $batchNo = null): bool
    {
        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $batchNo) {
            $inventory = $this->findInventory($productId, $warehouseId, $batchNo);
            
            if (!$inventory) {
                throw new \Exception('Không tìm thấy tồn kho');
            }
            
            // Kiểm tra số lượng có thể xuất
            if ($inventory->quantity < $quantity) {
                throw new \Exception('Số lượng trong kho không đủ');
            }
            
            // Giảm số lượng
            $inventory->quantity -= $quantity;
            $inventory->available_quantity = $inventory->quantity - $inventory->reserved_quantity;
            $inventory->save();
            
            return true;
        });
    }
    
    /**
     * Nhập kho (tăng số lượng thực tế)
     */
    public function addQuantity(int $productId, int $warehouseId, int $quantity, array $data = []): bool
    {
        return DB::transaction(function () use ($productId, $warehouseId, $quantity, $data) {
            $inventory = Inventory::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->first();
            
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
                Inventory::create([
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
            
            return true;
        });
    }
    
    /**
     * Tìm tồn kho có thể bán (ưu tiên theo FIFO và hạn sử dụng)
     */
    private function findAvailableInventory(int $productId, int $warehouseId, int $quantity, ?string $batchNo = null): ?Inventory
    {
        $query = Inventory::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('available_quantity', '>=', $quantity);
        
        if ($batchNo) {
            $query->where('batch_no', $batchNo);
        }
        
        // Ưu tiên theo thứ tự: hạn sử dụng gần nhất, tạo sớm nhất
        return $query->whereNotNull('expiry_date')
            ->orderBy('expiry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->first();
    }
    
    /**
     * Tìm tồn kho theo lô hàng
     */
    private function findInventory(int $productId, int $warehouseId, ?string $batchNo = null): ?Inventory
    {
        $query = Inventory::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId);
        
        if ($batchNo) {
            $query->where('batch_no', $batchNo);
        }
        
        return $query->first();
    }
    
    /**
     * Kiểm tra hàng sắp hết hạn
     */
    public function getExpiringSoon(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return Inventory::with(['product', 'warehouse'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', Carbon::now()->addDays($days))
            ->where('expiry_date', '>', Carbon::now())
            ->where('available_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }
    
    /**
     * Kiểm tra hàng đã hết hạn
     */
    public function getExpired(): \Illuminate\Database\Eloquent\Collection
    {
        return Inventory::with(['product', 'warehouse'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', Carbon::now())
            ->where('available_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }
    
    /**
     * Kiểm tra hàng sắp hết
     */
    public function getLowStock(int $threshold = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Inventory::with(['product', 'warehouse'])
            ->where('available_quantity', '<=', $threshold)
            ->where('available_quantity', '>', 0)
            ->orderBy('available_quantity', 'asc')
            ->get();
    }
    
    /**
     * Tự động xử lý đơn hàng (giữ chỗ khi đơn hàng được tạo)
     */
    public function processOrder(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->reserveQuantity(
                    $item->product_id,
                    $order->warehouse_id,
                    $item->quantity
                );
            }
            return true;
        });
    }
    
    /**
     * Tự động xử lý khi đơn hàng bị hủy (giải phóng số lượng đã giữ chỗ)
     */
    public function cancelOrder(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->releaseQuantity(
                    $item->product_id,
                    $order->warehouse_id,
                    $item->quantity
                );
            }
            return true;
        });
    }
    
    /**
     * Tự động xử lý khi đơn hàng được xác nhận (xuất kho thực tế)
     */
    public function confirmOrder(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                // Giải phóng số lượng đã giữ chỗ
                $this->releaseQuantity(
                    $item->product_id,
                    $order->warehouse_id,
                    $item->quantity
                );
                
                // Xuất kho thực tế
                $this->reduceQuantity(
                    $item->product_id,
                    $order->warehouse_id,
                    $item->quantity
                );
            }
            return true;
        });
    }
} 