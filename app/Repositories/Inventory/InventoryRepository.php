<?php
namespace App\Repositories\Inventory;

use App\Models\Inventory;
use App\Repositories\BaseRepository;

class InventoryRepository extends BaseRepository
{
    public function model()
    {
        return Inventory::class;
    }
} 