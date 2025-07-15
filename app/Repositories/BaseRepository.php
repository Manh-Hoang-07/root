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