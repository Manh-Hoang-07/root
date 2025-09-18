<?php

namespace App\Http\Controllers\Api\Admin\Inventory;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\Inventory\InventoryRequest;
use App\Http\Requests\Admin\Inventory\ImportInventoryRequest;
use App\Http\Requests\Admin\Inventory\ExportInventoryRequest;
use App\Services\Admin\Inventory\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InventoryController extends BaseController
{
    /**
     * @var InventoryService
     */
    protected $service;

    public function __construct(InventoryService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = InventoryRequest::class;
        $this->updateRequestClass = InventoryRequest::class;
        $this->indexRelations = ['product:id,name,code', 'variant:id,sku,name', 'warehouse:id,name'];
        $this->showRelations = ['product', 'variant', 'warehouse'];
    }

    /**
     * Lấy thông tin chi tiết tồn kho
     */
    public function show($id, ?Request $request = null): JsonResponse
    {
        try {
            $inventory = $this->service->findById($id);
            
            if (!$inventory) {
                return $this->apiResponse(false, null, 'Không tìm thấy tồn kho', 404);
            }

            return $this->successResponseWithFormat($inventory, 'Lấy thông tin tồn kho thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null,$e->getMessage());
        }
    }

    /**
     * Tạo tồn kho mới
     */
    public function store(): JsonResponse
    {
        try {
            $request = app($this->getStoreRequestClass());
            $inventory = $this->service->create($request->validated());

            return $this->successResponseWithFormat($inventory, 'Tạo tồn kho thành công', 201);
        } catch (\Exception $e) {
            return $this->apiResponse(false, null,$e->getMessage());
        }
    }

    /**
     * Cập nhật tồn kho
     */
    public function update($id): JsonResponse
    {
        try {
            $request = app($this->getUpdateRequestClass());
            $inventory = $this->service->update($id, $request->validated());

            return $this->successResponseWithFormat($inventory, 'Cập nhật tồn kho thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null,$e->getMessage());
        }
    }

    /**
     * Xóa tồn kho
     */
    public function destroy($id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->apiResponse(true, null, 'Xóa tồn kho thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null,$e->getMessage());
        }
    }

    /**
     * Nhập kho (tăng số lượng)
     */
    public function import(ImportInventoryRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            $inventory = $this->service->import(
                $validated['product_id'],
                $validated['warehouse_id'],
                $validated['quantity'],
                array_filter($validated, function($key) {
                    return in_array($key, ['batch_no', 'lot_no', 'expiry_date', 'cost_price']);
                }, ARRAY_FILTER_USE_KEY)
            );

            return $this->successResponseWithFormat($inventory, 'Nhập kho thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null,$e->getMessage());
        }
    }

    /**
     * Xuất kho (giảm số lượng)
     */
    public function export(ExportInventoryRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            
            $inventory = $this->service->export(
                $validated['product_id'],
                $validated['warehouse_id'],
                $validated['quantity'],
                $validated['batch_no'] ?? null
            );

            return $this->successResponseWithFormat($inventory, 'Xuất kho thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null,$e->getMessage());
        }
    }



    /**
     * Lấy hàng sắp hết hạn
     */
    public function expiringSoon(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 30);
            $inventories = $this->service->getExpiringSoon($days);

            return $this->successResponseWithFormat($inventories, 'Lấy danh sách hàng sắp hết hạn thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null,$e->getMessage());
        }
    }

    /**
     * Lấy hàng đã hết hạn
     */
    public function expired(): JsonResponse
    {
        try {
            $inventories = $this->service->getExpired();

            return $this->successResponseWithFormat($inventories, 'Lấy danh sách hàng đã hết hạn thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null,$e->getMessage());
        }
    }

    /**
     * Lấy hàng sắp hết
     */
    public function lowStock(Request $request): JsonResponse
    {
        try {
            $threshold = $request->get('threshold', 10);
            $inventories = $this->service->getLowStock($threshold);

            return $this->successResponseWithFormat($inventories, 'Lấy danh sách hàng sắp hết thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null,$e->getMessage());
        }
    }

    /**
     * Lấy tùy chọn bộ lọc
     */
    public function filterOptions(): JsonResponse
    {
        try {
            $options = $this->service->getFilterOptions();

            return $this->apiResponse(true, $options, 'Lấy tùy chọn bộ lọc thành công');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Filter options error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Stack trace: ' . $e->getTraceAsString());
            return $this->apiResponse(false, null,$e->getMessage());
        }
    }
}
