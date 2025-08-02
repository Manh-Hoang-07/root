<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public function model()
    {
        return User::class;
    }

    /**
     * Optimize relations for User model
     */
    protected function optimizeRelations($relations)
    {
        $optimizedRelations = [];
        foreach ($relations as $relation) {
            if (strpos($relation, ':') !== false) {
                // Nếu đã có select fields thì giữ nguyên
                $optimizedRelations[] = $relation;
            } else {
                // Tối ưu cho các relation của User
                switch ($relation) {
                    case 'roles':
                        $optimizedRelations[] = 'roles:id,name';
                        break;
                    case 'permissions':
                        $optimizedRelations[] = 'permissions:id,name';
                        break;
                    case 'profile':
                        $optimizedRelations[] = 'profile:id,user_id,name,phone,address';
                        break;
                    default:
                        $optimizedRelations[] = $relation;
                }
            }
        }
        return $optimizedRelations;
    }

    public function all($filters = [], $perPage = 20, $relations = [], $fields = ['*'])
    {
        $query = $this->model->newQuery();
        
        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('username', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
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
    
    protected function applySorting($query, $sortBy)
    {
        switch ($sortBy) {
            case 'created_at_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'username_asc':
                $query->orderBy('username', 'asc');
                break;
            case 'username_desc':
                $query->orderBy('username', 'desc');
                break;
            case 'email_asc':
                $query->orderBy('email', 'asc');
                break;
            case 'email_desc':
                $query->orderBy('email', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
    }

    public function changePassword($id, $newPassword)
    {
        $user = $this->find($id);
        if (!$user) {
            throw new \Exception('User not found');
        }
        
        $user->password = Hash::make($newPassword);
        $user->save();
        
        return $user;
    }
} 