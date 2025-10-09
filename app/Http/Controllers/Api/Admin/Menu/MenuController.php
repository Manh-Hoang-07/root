<?php

namespace App\Http\Controllers\Api\Admin\Menu;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Admin\Menu\MenuRequest;
use App\Services\Admin\Menu\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends BaseController
{
    protected $storeRequestClass = MenuRequest::class;
    protected $updateRequestClass = MenuRequest::class;
    protected $indexRelations = ['children'];
    protected $showRelations = ['parent', 'children'];

    /**
     * @var MenuService
     */
    protected $service;

    public function __construct(MenuService $service)
    {
        parent::__construct($service);
    }

    /**
     * Lấy danh sách menu dạng cây
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['roles']);
            $data = $this->service->getTree($filters);
            return $this->successResponseWithFormat($data, 'Lấy danh sách menu thành công');
        } catch (\Exception $e) {
            $this->logError('Menu Index', $e);
            return $this->apiResponse(false, null, 'Không thể tải danh sách menu', 500);
        }
    }
}
