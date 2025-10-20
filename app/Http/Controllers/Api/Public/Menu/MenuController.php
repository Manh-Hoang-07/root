<?php

namespace App\Http\Controllers\Api\Public\Menu;

use App\Http\Controllers\Api\Core\CrudController;
use App\Services\Public\Menu\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends CrudController
{
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
        $filters = [
            'type' => $request->input('type', 'public'),
        ];
            $result = $this->service->getTree($filters);
            return $this->apiResponse($result['success'], $result['data'], $result['message']);
        } catch (\Exception $e) {
            $this->logError('Menu Index', $e);
            return $this->apiResponse(false, null, 'Không thể tải danh sách menu', 500);
        }
    }
}
