<?php

namespace App\Services;

use App\Repositories\AttributeRepository;

class AttributeService extends BaseService
{
    public function __construct(AttributeRepository $repository)
    {
        parent::__construct($repository);
    }
    // Có thể bổ sung các hàm filter, search... nếu cần
} 