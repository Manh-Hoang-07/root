<?php

namespace App\Services;

abstract class BaseService
{
    protected $repo;

    public function __construct($repo)
    {
        $this->repo = $repo;
    }

    public function list($filters = [], $perPage = 20, $relations = [], $fields = ['*'])
    {
        return $this->repo->all($filters, $perPage, $relations, $fields);
    }

    public function find($id, $relations = [], $fields = ['*'])
    {
        return $this->repo->find($id, $relations, $fields);
    }

    public function create($data)
    {
        return $this->repo->create($data);
    }

    public function update($id, $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }
} 