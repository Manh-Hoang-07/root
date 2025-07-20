<?php
namespace App\Services\Inventory;

use App\Repositories\Inventory\InventoryRepository;
use App\Services\BaseService;

class InventoryService extends BaseService
{
    public function __construct(InventoryRepository $repo)
    {
        parent::__construct($repo);
    }
} 