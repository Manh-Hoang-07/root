<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Http\Requests\Admin\Product\StatusUpdateRequest;
use App\Services\Admin\Product\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends CrudController
{
    /**
     * @var ProductService
     */
    protected $service;
    protected $storeRequestClass = ProductRequest::class;
    protected $updateRequestClass = ProductRequest::class;
    protected $statusUpdateRequestClass = StatusUpdateRequest::class;
    protected $indexRelations = ['categories:id,name', 'variants:id,product_id,name,price,stock_quantity'];
    protected $showRelations = ['categories:id,name', 'variants:id,product_id,name,price,stock_quantity', 'createdUser:id,name', 'updatedUser:id,name'];

    public function __construct(ProductService $service)
    {
        parent::__construct($service);
    }

    protected function getSearchFields(): array
    {
        return ['id', 'name', 'sku'];
    }


    /**
     * Toggle featured status
     */
    public function toggleFeatured($id): JsonResponse
    {
        try {
            $result = $this->service->toggleFeatured($id);
            
            if ($result['success']) {
                return $this->apiResponse(true, $result['data'], $result['message']);
            } else {
                $statusCode = strpos($result['message'], 'Không tìm thấy') !== false ? 404 : 500;
                return $this->apiResponse(false, null, $result['message'], $statusCode);
            }
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }
}
