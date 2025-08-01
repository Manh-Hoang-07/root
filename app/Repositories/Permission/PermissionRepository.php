<?php
namespace App\Repositories\Permission;

use App\Models\Permission;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;

class PermissionRepository extends BaseRepository
{
    public function model()
    {
        return Permission::class;
    }

    public function all($filters = [], $perPage = 20, $relations = [], $fields = ['*'])
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
            $this->applySorting($query, $filters['sort_by']);
        }
        
        // Load relations
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        // Select fields
        if ($fields !== ['*']) {
            $query->select($fields);
        }
        
        $result = $query->paginate($perPage);
        

        
        return $result;
    }
    
    protected function applySorting($query, $sortBy)
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