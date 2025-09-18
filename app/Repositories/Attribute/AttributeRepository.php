<?php

namespace App\Repositories\Attribute;

use App\Models\Attribute;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class AttributeRepository extends BaseRepository
{
    public function model(): string
    {
        return Attribute::class;
    }


} 