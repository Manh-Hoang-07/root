<?php

namespace App\Models;

use App\Enums\BasicStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posttag';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'meta_title',
        'meta_description',
        'canonical_url',
    ];

    protected $casts = [
        'status' => BasicStatus::class,
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_posttag');
    }
}
