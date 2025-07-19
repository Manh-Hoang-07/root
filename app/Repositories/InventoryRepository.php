<?php

namespace App\Repositories;

use App\Models\Inventory;

class InventoryRepository extends BaseRepository
{
    public function model()
    {
        return Inventory::class;
    }
} 