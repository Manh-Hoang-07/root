<?php

namespace App\Http\Controllers\Api\Admin\Inventory;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\Inventory\InventoryRequest;
use App\Http\Requests\Admin\Inventory\ImportInventoryRequest;
use App\Http\Requests\Admin\Inventory\ExportInventoryRequest;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InventoryController extends BaseController
{
    public function __construct(InventoryService $service)
    {
        parent::__construct($service);
        $this->storeRequestClass = InventoryRequest::class;
        $this->updateRequestClass = InventoryRequest::class;
        $this->indexRelations = ['product:id,name,code', 'variant:id,sku,name', 'warehouse:id,name'];
        $this->showRelations = ['product', 'variant', 'warehouse'];
    }

    // /**
    //  * Lấy danh sách tồn kho với phân trang và bộ lọc
    //  */
    // public function index(Request $request)
    // {
    //     try {
    //         $filters = $request->only([
    //             'search', 'warehouse_id', 'product_id', 'brand_id', 'category_id',
    //             'low_stock', 'expiring_soon', 'expired', 'out_of_stock',
    //             'sort_by', 'sort_direction'
    //         ]);

    //         $perPage = $request->get('per_page', 15);
            
    //         // Load relations manually
    //         $inventories = $this->service->getInventories($filters, $perPage);
            
    //         return $this->successResponse(
    //             $this->formatCollectionData($inventories),
    //             'Lấy danh sách tồn kho thành công'
    //         );
    //     } catch (\Exception $e) {
    //         return $this->errorResponse($e->getMessage());
    //     }
    // }

    /**
     * Lấy thông tin chi tiết tồn kho
     */
    public function show($id, Request $request = null)
    {
        try {
            $inventory = $this->service->findById($id);
            
            if (!$inventory) {
                return $this->notFoundResponse('Không tìm thấy tồn kho');
            }

            return $this->successResponse(
                $this->formatSingleData($inventory),
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
            $inventory = $this->service->create($request->validated());

            return $this->successResponse(
                $this->formatSingleData($inventory),
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
            $inventory = $this->service->update($id, $request->validated());

            return $this->successResponse(
                $this->formatSingleData($inventory),
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
            $this->service->delete($id);

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

            return $this->successResponse(
                $this->formatSingleData($inventory),
                'Nhập kho thành công'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
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

            return $this->successResponse(
                $this->formatSingleData($inventory),
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
            $inventories = $this->service->getExpiringSoon($days);

            return $this->successResponse(
                $this->formatCollectionData($inventories),
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
            $inventories = $this->service->getExpired();

            return $this->successResponse(
                $this->formatCollectionData($inventories),
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
            $inventories = $this->service->getLowStock($threshold);

            return $this->successResponse(
                $this->formatCollectionData($inventories),
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
            $options = $this->service->getFilterOptions();

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
