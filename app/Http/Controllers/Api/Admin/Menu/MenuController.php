<?php

namespace App\Http\Controllers\Api\Admin\Menu;

use App\Http\Controllers\Api\Core\CrudController;
use App\Http\Requests\Admin\Menu\MenuRequest;
use App\Services\Admin\Menu\MenuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends CrudController
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
            $user = Auth::user();
        $filters = [
            'type' => $request->input('type', 'admin'),
        ];
            if ($user) {
                $filters['permissions'] = $user->getAllPermissions()->pluck('name')->toArray();
            }
            $data = $this->service->getTree($filters);

            return $this->successResponseWithFormat($data, 'Lấy danh sách menu thành công');
        } catch (\Exception $e) {
            $this->logError('Menu Index', $e);
            return $this->apiResponse(false, null, 'Không thể tải danh sách menu', 500);
        }
    }
}
