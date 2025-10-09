<?php

namespace App\Repositories\Menu;

use App\Repositories\BaseRepository;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Collection;

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
        $query = $this->model->query()->where('is_active', true);
        if (!empty($filters['roles'])) {
            $roles = (array)$filters['roles'];
            $query->where(function ($q) use ($roles) {
                foreach ($roles as $role) {
                    $q->orWhereJsonContains('roles', $role);
                }
            });
        }
        $menus = $query->with(['children'])->orderBy('sort_order')->get();
        $tree = $menus->whereNull('parent_id')->values()->toArray();
        return [
            'data' => $tree,
            'pagination' => [
                'total' => count($tree),
                'per_page' => count($tree),
                'current_page' => 1,
                'last_page' => 1
            ]
        ];
    }
}
