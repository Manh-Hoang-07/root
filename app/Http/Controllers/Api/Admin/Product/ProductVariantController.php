<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Product\ProductVariantService;
use App\Http\Requests\Admin\Product\ProductVariantRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductVariantController extends BaseController
{
    protected $storeRequestClass = ProductVariantRequest::class;
    protected $updateRequestClass = ProductVariantRequest::class;
    protected $indexRelations = ['product:id,name,sku', 'attributes.attribute:id,name', 'attributes.value:id,value'];
    protected $showRelations = ['product:id,name,sku', 'attributes.attribute:id,name', 'attributes.value:id,value', 'createdUser:id,name', 'updatedUser:id,name'];

    public function __construct(ProductVariantService $service)
    {
        parent::__construct($service);
    }

    protected function getSearchFields(): array
    {
        return ['id', 'name', 'sku'];
    }

    /**
     * Update variant status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            $variant = $this->service->getRepo()->updateStatus($id, $request->status);
            if (!$variant) {
                return $this->apiResponse(false, null, 'Không tìm thấy biến thể sản phẩm', 404);
            }
            
            return $this->apiResponse(true, $variant, 'Cập nhật trạng thái biến thể thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }


}
