<?php
namespace App\Http\Controllers\Api\Admin\Category;

use App\Http\Controllers\Api\BaseController;
use App\Services\Category\CategoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Admin\Category\CategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    public function __construct(CategoryService $service)
    {
        parent::__construct($service);
                $this->storeRequestClass = CategoryRequest::class;
        $this->updateRequestClass = CategoryRequest::class;
        
        // Tối ưu: Chỉ load parent relation khi cần thiết
        $this->indexRelations = []; // Không load relations mặc định cho list
        
        // Load đầy đủ thông tin cho show/edit
        $this->showRelations = ['parent:id,name', 'children:id,name,parent_id'];
    }

    /**
     * Override index method với tối ưu hiệu suất
     */
    public function index(Request $request)
    {
        // Parse relations từ request
        $requestRelations = $this->parseRelations($request->get('relations'));
        
        // Nếu có relations được yêu cầu thì dùng, không thì dùng default tối ưu
        $relations = !empty($requestRelations) 
            ? $requestRelations 
            : $this->indexRelations;
        
        $fields = $this->parseFields($request->get('fields'));
        
        // Tối ưu: Chỉ load fields cần thiết cho list
        if (empty($fields) || $fields === ['*']) {
            $fields = ['id', 'name', 'parent_id', 'status', 'created_at'];
        }
        
        $perPage = min($request->get('per_page', $this->defaultPerPage), $this->maxPerPage);
        $data = $this->service->list($request->all(), $perPage, $relations, $fields);
        
        return $this->successResponse($this->formatCollectionData($data), 'Lấy danh sách thành công');
    }

    /**
     * Override getDefaultListFields cho Category
     */
    protected function getDefaultListFields(): array
    {
        return ['id', 'name', 'parent_id', 'status', 'created_at'];
    }
} 
