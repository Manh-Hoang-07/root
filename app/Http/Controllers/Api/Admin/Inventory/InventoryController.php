<?php

namespace App\Http\Controllers\Api\Admin\Inventory;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Http\Requests\Admin\Inventory\InventoryRequest;
use App\Http\Resources\Admin\Inventory\InventoryResource;
use App\Http\Resources\Admin\Inventory\InventoryCollection;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InventoryController extends BaseController
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
        parent::__construct($inventoryService, InventoryResource::class);
        $this->storeRequestClass = InventoryRequest::class;
        $this->updateRequestClass = InventoryRequest::class;
    }

    /**
     * Lấy danh sách tồn kho với phân trang và bộ lọc
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'search',
                'warehouse_id',
                'product_id',
                'brand_id',
                'category_id',
                'low_stock',
                'expiring_soon',
                'expired',
                'out_of_stock',
                'sort_by',
                'sort_direction'
            ]);

            $perPage = $request->get('per_page', 15);
            $inventories = $this->inventoryService->getInventories($filters, $perPage);

            return $this->successResponse(
                new InventoryCollection($inventories),
                'Lấy danh sách tồn kho thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Lấy thông tin chi tiết tồn kho
     */
    public function show($id, Request $request = null)
    {
        try {
            $inventory = $this->inventoryService->findById($id);
            
            if (!$inventory) {
                return $this->notFoundResponse('Không tìm thấy tồn kho');
            }

            return $this->successResponse(
                new InventoryResource($inventory),
                'Lấy thông tin tồn kho thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Tạo tồn kho mới
     */
    public function store()
    {
        try {
            $request = app($this->getStoreRequestClass());
            $inventory = $this->inventoryService->create($request->validated());

            return $this->successResponse(
                new InventoryResource($inventory),
                'Tạo tồn kho thành công',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Cập nhật tồn kho
     */
    public function update($id)
    {
        try {
            $request = app($this->getUpdateRequestClass());
            $inventory = $this->inventoryService->update($id, $request->validated());

            return $this->successResponse(
                new InventoryResource($inventory),
                'Cập nhật tồn kho thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Xóa tồn kho
     */
    public function destroy($id)
    {
        try {
            $this->inventoryService->delete($id);

            return $this->successResponse(
                null,
                'Xóa tồn kho thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Nhập kho (tăng số lượng)
     */
    public function import(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'quantity' => 'required|integer|min:1',
                'batch_no' => 'nullable|string|max:100',
                'lot_no' => 'nullable|string|max:100',
                'expiry_date' => 'nullable|date|after:today',
                'cost_price' => 'nullable|numeric|min:0',
            ]);

            $inventory = $this->inventoryService->import(
                $request->product_id,
                $request->warehouse_id,
                $request->quantity,
                $request->only(['batch_no', 'lot_no', 'expiry_date', 'cost_price'])
            );

            return $this->successResponse(
                new InventoryResource($inventory),
                'Nhập kho thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Xuất kho (giảm số lượng)
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'quantity' => 'required|integer|min:1',
                'batch_no' => 'nullable|string|max:100',
            ]);

            $inventory = $this->inventoryService->export(
                $request->product_id,
                $request->warehouse_id,
                $request->quantity,
                $request->batch_no
            );

            return $this->successResponse(
                new InventoryResource($inventory),
                'Xuất kho thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }



    /**
     * Lấy hàng sắp hết hạn
     */
    public function expiringSoon(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 30);
            $inventories = $this->inventoryService->getExpiringSoon($days);

            return $this->successResponse(
                new InventoryCollection($inventories),
                'Lấy danh sách hàng sắp hết hạn thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Lấy hàng đã hết hạn
     */
    public function expired(): JsonResponse
    {
        try {
            $inventories = $this->inventoryService->getExpired();

            return $this->successResponse(
                new InventoryCollection($inventories),
                'Lấy danh sách hàng đã hết hạn thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Lấy hàng sắp hết
     */
    public function lowStock(Request $request): JsonResponse
    {
        try {
            $threshold = $request->get('threshold', 10);
            $inventories = $this->inventoryService->getLowStock($threshold);

            return $this->successResponse(
                new InventoryCollection($inventories),
                'Lấy danh sách hàng sắp hết thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Lấy tùy chọn bộ lọc
     */
    public function filterOptions(): JsonResponse
    {
        try {
            $options = $this->inventoryService->getFilterOptions();

            return $this->successResponse(
                $options,
                'Lấy tùy chọn bộ lọc thành công'
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Filter options error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Stack trace: ' . $e->getTraceAsString());
            return $this->errorResponse($e->getMessage());
        }
    }
}
