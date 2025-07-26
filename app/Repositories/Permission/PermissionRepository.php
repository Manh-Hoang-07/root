<?php
namespace App\Repositories\Permission;

use App\Models\Permission;
use App\Repositories\BaseRepository;

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
        
        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('display_name', 'like', '%' . $filters['search'] . '%');
            });
        }
        
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
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
        
        return $query->paginate($perPage);
    }
    
    private function applySorting($query, $sortBy)
    {
        switch ($sortBy) {
            case 'created_at_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
    }
} 