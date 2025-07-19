<?php

namespace App\Repositories;

use App\Models\Promotion;

class PromotionRepository extends BaseRepository
{
    public function model()
    {
        return Promotion::class;
    }
} 