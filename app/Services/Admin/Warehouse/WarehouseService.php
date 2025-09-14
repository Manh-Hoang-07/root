<?php
namespace App\Services\Admin\Warehouse;

use App\Repositories\Warehouse\WarehouseRepository;
use App\Services\BaseService;

class WarehouseService extends BaseService
{
    public function __construct(WarehouseRepository $repo)
    {
        parent::__construct($repo);
    }
    // ... logic đặc thù cho warehouse ...
} 