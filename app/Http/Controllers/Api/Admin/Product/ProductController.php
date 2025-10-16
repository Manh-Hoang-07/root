<?php

namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\BaseController;
use App\Services\Admin\Product\ProductService;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Http\Requests\Admin\Product\StatusUpdateRequest;
use Illuminate\Http\JsonResponse;

class ProductController extends BaseController
{
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
            $product = $this->service->getRepo()->toggleFeatured($id);
            if (!$product) {
                return $this->apiResponse(false, null, 'Không tìm thấy sản phẩm', 404);
            }
            
            return $this->apiResponse(true, $product, 'Cập nhật trạng thái nổi bật thành công');
        } catch (\Exception $e) {
            return $this->apiResponse(false, null, $e->getMessage(), 500);
        }
    }



}
