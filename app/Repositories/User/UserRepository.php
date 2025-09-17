<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{
    public function model(): string
    {
        return User::class;
    }

    /**
     * Optimize relations for User model
     */
    protected function optimizeRelations(array $relations): array
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

    public function all(array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*']): array
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
            $this->applyCustomSorting($query, $filters['sort_by']);
        }
        
        // Load relations
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        // Select fields
        if ($fields !== ['*']) {
            $query->select($fields);
        }
        
        $paginator = $query->paginate($perPage);
        
        return [
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
            ]
        ];
    }
    
    protected function applyCustomSorting(\Illuminate\Database\Eloquent\Builder $query, string $sortBy): void
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

    public function changePassword($id, string $newPassword): array
    {
        $user = $this->model->find($id);
        if (!$user) {
            throw new \Exception('User not found');
        }
        
        $user->password = Hash::make($newPassword);
        $user->save();
        
        return $user->toArray();
    }

    /**
     * Get user profile
     */
    public function profile($id): ?array
    {
        $user = $this->model->with('profile')->find($id);
        return $user ? $user->toArray() : null;
    }

    /**
     * Update user profile
     */
    public function updateProfile($id, array $data): ?array
    {
        $user = $this->model->find($id);
        if (!$user) {
            return null;
        }

        if ($user->profile) {
            $user->profile->update($data);
        } else {
            $data['user_id'] = $user->id;
            $user->profile()->create($data);
        }

        return $user->load('profile')->toArray();
    }

    /**
     * Override searchable fields for User
     */
    protected function getSearchableFields(): array
    {
        return ['username', 'email', 'display_name'];
    }

    /**
     * Override search filter for User to include role search
     */
    protected function applySearchFilter(\Illuminate\Database\Eloquent\Builder $query, string $searchValue): void
    {
        $query->where(function($q) use ($searchValue) {
            // Search in main fields
            $q->orWhere('username', 'like', "%{$searchValue}%")
              ->orWhere('email', 'like', "%{$searchValue}%");
            
            // Search in role names
            $q->orWhereHas('roles', function($roleQuery) use ($searchValue) {
                $roleQuery->where('name', 'like', "%{$searchValue}%");
            });
        });
    }
} 