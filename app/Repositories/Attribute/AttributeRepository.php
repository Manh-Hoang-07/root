<?php
namespace App\Repositories\Attribute;

use App\Models\Attribute;
use App\Repositories\BaseRepository;

class AttributeRepository extends BaseRepository
{
    public function model()
    {
        return Attribute::class;
    }
} 