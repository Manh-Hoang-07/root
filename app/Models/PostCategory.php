<?php

namespace App\Models;

use App\Enums\BasicStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'postcategory';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'image',
        'status',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_image',
        'sort_order',
    ];

    protected $casts = [
        'status' => BasicStatus::class,
        'sort_order' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(PostCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PostCategory::class, 'parent_id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_postcategory');
    }
}
