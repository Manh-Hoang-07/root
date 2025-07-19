<?php

namespace App\Services;

use App\Repositories\AttributeValueRepository;

class AttributeValueService extends BaseService
{
    public function __construct(AttributeValueRepository $repository)
    {
        parent::__construct($repository);
    }
    // Có thể bổ sung các hàm filter, search... nếu cần
} 