<?php
namespace App\Repositories\Permission;

use App\Models\Permission;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;

class PermissionRepository extends BaseRepository
{
    public function model(): string
    {
        return Permission::class;
    }

    public function all(array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*']): array
    {
        $query = $this->model->newQuery();
        // Thêm subquery để đếm children
        $query->addSelect([
            'children_count' => Permission::selectRaw('count(*)')
                ->whereColumn('parent_id', 'permissions.id')
        ]);
        // LEFT JOIN để lấy parent_name
        $query->leftJoin('permissions as parent_permissions', 'permissions.parent_id', '=', 'parent_permissions.id')
              ->addSelect('parent_permissions.display_name as parent_name');
        // Select fields với table prefix để tránh ambiguous column
        if ($fields !== ['*']) {
            $selectFields = [];
            foreach ($fields as $field) {
                if ($field === 'id' || $field === 'name' || $field === 'display_name' || 
                    $field === 'guard_name' || $field === 'parent_id' || $field === 'status' ||
                    $field === 'created_at' || $field === 'updated_at') {
                    $selectFields[] = 'permissions.' . $field;
                } else {
                    $selectFields[] = $field;
                }
            }
            $query->select($selectFields);
        } else {
            // Select tất cả fields từ permissions table với prefix
            $query->select([
                'permissions.id',
                'permissions.name',
                'permissions.display_name',
                'permissions.guard_name',
                'permissions.parent_id',
                'permissions.status',
                'permissions.created_at',
                'permissions.updated_at'
            ]);
        }
        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('permissions.name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('permissions.display_name', 'like', '%' . $filters['search'] . '%');
            });
        }
        if (!empty($filters['status'])) {
            $query->where('permissions.status', $filters['status']);
        }
        // Apply sorting
        if (!empty($filters['sort_by'])) {
            $this->applyCustomSorting($query, $filters['sort_by']);
        }
        // Load relations
        if (!empty($relations)) {
            $query->with($relations);
        }
        $result = $query->paginate($perPage);
        return [
            'data' => $result->items(),
            'pagination' => [
                'current_page' => $result->currentPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'from' => $result->firstItem(),
                'to' => $result->lastItem(),
                'has_more_pages' => $result->hasMorePages(),
            ]
        ];
    }
    
    protected function applyCustomSorting(\Illuminate\Database\Eloquent\Builder $query, string $sortBy): void
    {
        switch ($sortBy) {
            case 'created_at_desc':
                $query->orderBy('permissions.created_at', 'desc');
                break;
            case 'created_at_asc':
                $query->orderBy('permissions.created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('permissions.name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('permissions.name', 'desc');
                break;
            default:
                $query->orderBy('permissions.created_at', 'desc');
        }
    }
} 