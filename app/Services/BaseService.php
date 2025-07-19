<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

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
        $data = $this->handleImageUpload($data);
        return $this->repo->create($data);
    }

    public function update($id, $data)
    {
        $data = $this->handleImageUpload($data);
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        $item = $this->find($id);
        return $this->repo->delete($id);
    }

    protected function handleImageUpload($data)
    {
        // Xử lý tự động cho trường image nếu là file upload
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('categories', 'public');
        }
        return $data;
    }
} 