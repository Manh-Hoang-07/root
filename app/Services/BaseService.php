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
        $result = $this->repo->create($data);
        

        
        return $result;
    }

    public function update($id, $data)
    {
        $data = $this->handleImageUpload($data);
        $result = $this->repo->update($id, $data);
        

        
        return $result;
    }

    public function delete($id)
    {
        $item = $this->find($id);
        $result = $this->repo->delete($id);
        

        
        return $result;
    }

    public function getRepo()
    {
        return $this->repo;
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