<?php

namespace App\Repositories;

use App\Models\Image;

class ImageRepository extends BaseRepository
{
    public function model(): string
    {
        return Image::class;
    }
} 