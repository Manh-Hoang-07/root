<?php

namespace App\Models;

use App\Enums\PostStatus;
use App\Enums\BasicStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'excerpt',
        'content',
        'image',
        'cover_image',
        'primary_postcategory_id',
        'status',
        'is_featured',
        'is_pinned',
        'published_at',
        'view_count',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'is_featured' => 'boolean',
        'is_pinned' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    public function primaryCategory()
    {
        return $this->belongsTo(PostCategory::class, 'primary_postcategory_id');
    }

    public function categories()
    {
        return $this->belongsToMany(PostCategory::class, 'post_postcategory', 'post_id', 'postcategory_id');
    }

    public function tags()
    {
        return $this->belongsToMany(PostTag::class, 'post_posttag', 'post_id', 'posttag_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
