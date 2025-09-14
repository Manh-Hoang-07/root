<?php

namespace App\Services\Public\PostTag;

use App\Services\BaseService;
use App\Repositories\PostTag\PostTagRepository;
use Illuminate\Database\Eloquent\Collection;

class PostTagService extends BaseService
{
    public function __construct(PostTagRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Lấy danh sách tag công khai
     */
    public function getPublishedTags()
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'slug', 'description']);
    }

    /**
     * Lấy tag theo slug
     */
    public function findBySlug(string $slug)
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->where('slug', $slug)
            ->first(['id', 'name', 'slug', 'description']);
    }

    /**
     * Lấy tag có bài viết
     */
    public function getTagsWithPosts()
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->whereHas('posts', function($query) {
                $query->where('status', 'published');
            })
            ->withCount('posts')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'slug', 'description']);
    }

    /**
     * Lấy tag phổ biến (có nhiều bài viết nhất)
     */
    public function getPopularTags($limit = 20)
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->whereHas('posts', function($query) {
                $query->where('status', 'published');
            })
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'description']);
    }

    /**
     * Lấy tag cloud (tất cả tag với số lượng bài viết)
     */
    public function getTagCloud()
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->whereHas('posts', function($query) {
                $query->where('status', 'published');
            })
            ->withCount('posts')
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->get(['id', 'name', 'slug', 'posts_count']);
    }
}
