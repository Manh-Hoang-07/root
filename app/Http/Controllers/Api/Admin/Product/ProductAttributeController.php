<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Product\ProductAttributeService;
use App\Http\Requests\Admin\Product\ProductAttributeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductAttributeController extends BaseController
{
    protected $storeRequestClass = ProductAttributeRequest::class;
    protected $updateRequestClass = ProductAttributeRequest::class;
    protected $indexRelations = ['values:id,product_attribute_id,value'];
    protected $showRelations = ['values:id,product_attribute_id,value', 'createdUser:id,name', 'updatedUser:id,name'];

    public function __construct(ProductAttributeService $service)
    {
        parent::__construct($service);
    }

    protected function getSearchFields(): array
    {
        return ['id', 'name'];
    }

    /**
     * Update attribute status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|in:active,inactive'
            ]);

            $attribute = $this->service->getRepo()->updateStatus($id, $request->status);
            if (!$attribute) {
                return $this->apiResponse(false, null, 'Không tìm thấy thuộc tính', 404);
            }
            
            return $this->apiResponse(true, $attribute, 'Cập nhật trạng thái thuộc tính thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }


}
