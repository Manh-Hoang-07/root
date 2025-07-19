<?php

namespace App\Repositories;

use App\Models\Attribute;

class AttributeRepository extends BaseRepository
{
    public function __construct(Attribute $model)
    {
        parent::__construct($model);
    }
    // Có thể bổ sung các hàm filter, search... nếu cần
} 