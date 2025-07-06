<?php

namespace App\Repositories\Admin;

use App\Models\User;

class UserRepository
{
    public function all($filters = [])
    {
        $query = User::query();
        // Thêm filter cho admin nếu cần
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                  ->orWhere('email', 'like', '%'.$filters['search'].'%');
            });
        }
        return $query->orderByDesc('id')->paginate($filters['per_page'] ?? 20);
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = $this->find($id);
        return $user->delete();
    }

    public function toggleStatus($id)
    {
        $user = $this->find($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
        return $user;
    }
} 