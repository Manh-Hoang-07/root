<?php
namespace App\Repositories\Warehouse;

use App\Models\Warehouse;
use App\Repositories\BaseRepository;

class WarehouseRepository extends BaseRepository
{
    public function model(): string
    {
        return Warehouse::class;
    }
} 