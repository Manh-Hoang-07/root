<?php

namespace App\Services\Public\Menu;

use App\Services\BaseService;
use App\Repositories\Menu\MenuRepository;

class MenuService extends BaseService
{
    /**
     * @var MenuRepository
     */
    protected $repo;
    public function __construct(MenuRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Lấy menu dạng cây
     */
    public function getTree(array $filters = []): array
    {
        try {
            $result = $this->repo->getMenuTree($filters);
            
            return [
                'success' => true,
                'message' => 'Lấy danh sách menu thành công',
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Không thể lấy danh sách menu',
                'data' => null
            ];
        }
    }
}
