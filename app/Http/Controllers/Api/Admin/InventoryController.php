<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\InventoryResource;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends BaseController
{
    public function __construct(InventoryService $inventoryService)
    {
        parent::__construct($inventoryService, InventoryResource::class);
    }

    // Lấy tồn kho tổng của sản phẩm
    public function productInventory($productId)
    {
        $inventories = \App\Models\Inventory::where('product_id', $productId)->get();
        return InventoryResource::collection($inventories);
    }

    // Lấy tồn kho theo kho
    public function warehouseInventory($warehouseId)
    {
        $inventories = \App\Models\Inventory::where('warehouse_id', $warehouseId)->get();
        return InventoryResource::collection($inventories);
    }

    // Điều chỉnh tồn kho (nhập/xuất kho)
    public function adjust(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'variant_id' => 'nullable|exists:variants,id',
            'quantity' => 'required|integer',
        ]);
        $inventory = \App\Models\Inventory::firstOrCreate([
            'product_id' => $data['product_id'],
            'warehouse_id' => $data['warehouse_id'],
            'variant_id' => $data['variant_id'] ?? null,
        ]);
        $inventory->quantity = $data['quantity'];
        $inventory->save();
        return new InventoryResource($inventory);
    }
} 