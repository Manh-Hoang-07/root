<?php

namespace App\Services\Public\Menu;

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
}
