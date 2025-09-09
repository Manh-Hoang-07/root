<?php
namespace App\Http\Controllers\Api\Admin\Brand;

use App\Http\Controllers\Api\BaseController;
use App\Services\Brand\BrandService;
use App\Http\Requests\Admin\Brand\BrandRequest;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    public function __construct(BrandService $service)
    {
        parent::__construct($service);
                $this->storeRequestClass = BrandRequest::class;
        $this->updateRequestClass = BrandRequest::class;
        
        // Tối ưu: Không load relations mặc định cho list
        $this->indexRelations = [];
    }

    /**
     * Tối ưu search method
     */
    public function search(Request $request)
    {
        $search = $request->get('search', '');
        $id = $request->get('id');
        $ids = $request->get('ids');
        $limit = min($request->get('limit', 10), 100); // Max 100 items

        // Tối ưu: Sử dụng service thay vì query trực tiếp
        $filters = [];
        
        if ($id) {
            $filters['id'] = $id;
        } elseif ($ids) {
            $filters['ids'] = $ids;
        } elseif ($search) {
            $filters['search'] = $search;
        }

        // Chỉ load fields cần thiết cho search
        $fields = ['id', 'name'];
        $relations = [];
        
        $results = $this->service->list($filters, $limit, $relations, $fields);

        return ApiResponse::success($results->map(function ($brand) {
            return [
                'value' => $brand->id,
                'label' => $brand->name
            ];
        }));
    }

    /**
     * Override getDefaultListFields cho Brand
     */
    protected function getDefaultListFields(): array
    {
        return ['id', 'name', 'status', 'created_at'];
    }
} 
