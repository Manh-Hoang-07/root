<?php
namespace App\Repositories\Attribute;

use App\Models\AttributeValue;
use App\Repositories\BaseRepository;

class AttributeValueRepository extends BaseRepository
{
    public function model()
    {
        return AttributeValue::class;
    }
} 