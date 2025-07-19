<?php

namespace App\Repositories;

use App\Models\AttributeValue;

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
    // Có thể bổ sung các hàm filter, search... nếu cần
} 