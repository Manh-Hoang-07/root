<?php

namespace App\Repositories;

use App\Models\Warehouse;

class WarehouseRepository extends BaseRepository
{
    public function model()
    {
        return Warehouse::class;
    }
} 