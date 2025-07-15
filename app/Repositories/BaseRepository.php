<?php

namespace App\Repositories;

abstract class BaseRepository
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function all($filters = [], $perPage = 20, $relations = [], $fields = ['*'])
    {
        $query = $this->model->query();
        if (!empty($relations)) {
            $query->with($relations);
        }
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($fields);
        }
        foreach ($filters as $key => $value) {
            if ($value === '' || $value === null) continue;
            if ($key === 'search') {
                // Nếu model có quan hệ profile thì search cả profile->name
                $query->where(function($q) use ($value) {
                    if (method_exists($this->model, 'profile')) {
                        $q->orWhereHas('profile', function($q2) use ($value) {
                            $q2->where('name', 'like', "%$value%") ;
                        });
                    }
                    if (in_array('name', $this->model->getFillable())) {
                        $q->orWhere('name', 'like', "%$value%") ;
                    }
                    if (in_array('email', $this->model->getFillable())) {
                        $q->orWhere('email', 'like', "%$value%") ;
                    }
                    if (in_array('phone', $this->model->getFillable())) {
                        $q->orWhere('phone', 'like', "%$value%") ;
                    }
                });
            } elseif (in_array($key, ['page', 'per_page', 'sort', 'order'])) {
                continue;
            } else {
                $query->where($key, $value);
            }
        }
        return $query->paginate($perPage);
    }

    public function find($id, $relations = [], $fields = ['*'])
    {
        $query = $this->model->query();
        if (!empty($relations)) {
            $query->with($relations);
        }
        if (!empty($fields) && $fields !== ['*']) {
            $query->select($fields);
        }
        return $query->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $item = $this->find($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        $item = $this->find($id);
        return $item->delete();
    }
} 