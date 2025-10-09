<?php

namespace App\Services\Admin\Menu;

use App\Services\BaseService;
use App\Repositories\Menu\MenuRepository;

class MenuService extends BaseService
{
    public function __construct(MenuRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Lấy menu dạng cây
     */
    public function getTree(array $filters = []): array
    {
        return $this->repo->getMenuTree($filters);
    }

    /**
     * Khi tạo/sửa menu, tự thêm order nếu chưa có
     */
    public function create($data): array
    {
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $this->repo->countBy(['parent_id' => $data['parent_id'] ?? null]) + 1;
        }
        return parent::create($data);
    }
}
