<?php

namespace App\Services;

use App\Repositories\InventoryRepository;

class InventoryService extends BaseService
{
    public function __construct(InventoryRepository $repository)
    {
        parent::__construct($repository);
    }
} 