<?php
namespace App\Repositories\Promotion;

use App\Models\Promotion;
use App\Repositories\BaseRepository;

class PromotionRepository extends BaseRepository
{
    public function model()
    {
        return Promotion::class;
    }
} 