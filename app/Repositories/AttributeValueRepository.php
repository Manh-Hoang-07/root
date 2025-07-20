<?php

namespace App\Repositories;

use App\Models\AttributeValue;
use Illuminate\Support\Facades\Log;

class AttributeValueRepository extends BaseRepository
{
    public function __construct(AttributeValue $model)
    {
        parent::__construct($model);
    }

    public function all($filters = [], $perPage = 20, $relations = [], $fields = ['*'])
    {
        $relations = array_unique(array_merge($relations, ['attribute']));
        return parent::all($filters, $perPage, $relations, $fields);
    }

    public function update($id, array $data)
    {
        Log::info('AttributeValueRepository::update', [
            'id' => $id,
            'data' => $data
        ]);
        
        $item = $this->find($id);
        $result = $item->update($data);
        
        Log::info('AttributeValueRepository::update result', [
            'result' => $result,
            'updated_item' => $item->fresh()->toArray()
        ]);
        
        return $item;
    }
} 