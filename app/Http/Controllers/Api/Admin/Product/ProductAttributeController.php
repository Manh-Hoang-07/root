<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\Product\AttributeStatusUpdateRequest;
use App\Http\Requests\Admin\Product\ProductAttributeRequest;
use App\Services\Admin\Product\ProductAttributeService;

class ProductAttributeController extends CrudController
{
    /**
     * @var ProductAttributeService
     */
    protected $service;
    protected $storeRequestClass = ProductAttributeRequest::class;
    protected $updateRequestClass = ProductAttributeRequest::class;
    protected $statusUpdateRequestClass = AttributeStatusUpdateRequest::class;
    protected $indexRelations = ['values:id,product_attribute_id,value'];
    protected $showRelations = ['values:id,product_attribute_id,value', 'createdUser:id,username', 'updatedUser:id,username'];

    public function __construct(ProductAttributeService $service)
    {
        parent::__construct($service);
    }

    protected function getSearchFields(): array
    {
        return ['id', 'name'];
    }

    /**
     * Get values of a product attribute
     */
    public function values($id)
    {
        try {
            $attribute = $this->service->find($id, ['values']);
            
            if (!$attribute) {
                return $this->apiResponse(false, null, 'Không tìm thấy thuộc tính sản phẩm', 404);
            }

            return $this->apiResponse(true, $attribute['values'] ?? [], 'Lấy danh sách giá trị thuộc tính thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }
}
