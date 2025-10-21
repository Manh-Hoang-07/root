<?php

namespace App\Repositories\Menu;

use App\Repositories\BaseRepository;
use App\Models\Menu;

class MenuRepository extends BaseRepository
{
    public function model()
    {
        return Menu::class;
    }

    /**
     * Lấy menu dạng cây (tree)
     */
    public function getMenuTree(array $filters = []): array
    {
        $query = $this->model->query()->where('status', 'active');
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['permissions'])) {
            $permissions = (array)$filters['permissions'];
            $query->where(function ($q) use ($permissions) {
                foreach ($permissions as $permission) {
                    $q->orWhereJsonContains('permissions', $permission);
                }
            });
        }
        $menus = $query->with(['children' => function ($childQuery) use ($filters) {
            $childQuery->where('status', 'active')->orderBy('sort_order');
            if (!empty($filters['permissions'])) {
                $permissions = (array)$filters['permissions'];
                $childQuery->where(function ($cq) use ($permissions) {
                    foreach ($permissions as $permission) {
                        $cq->orWhereJsonContains('permissions', $permission);
                    }
                });
            }
        }])
            ->orderBy('sort_order')
            ->get();
        $tree = $menus->whereNull('parent_id')->values()->toArray();
        return [
            'data' => $tree,
            'pagination' => [
                'total' => count($tree),
                'per_page' => count($tree),
                'current_page' => 1,
                'last_page' => 1,
            ],
        ];
    }


}
