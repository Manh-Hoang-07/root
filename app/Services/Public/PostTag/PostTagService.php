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
    public function getPublishedTags(): array
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'slug', 'description'])
            ->toArray();
    }

    /**
     * Lấy tag theo slug
     */
    public function findBySlug(string $slug): ?array
    {
        $tag = $this->repo->getModel()
            ->where('status', 'active')
            ->where('slug', $slug)
            ->first(['id', 'name', 'slug', 'description']);
        
        return $tag ? $tag->toArray() : null;
    }

    /**
     * Lấy tag có bài viết
     */
    public function getTagsWithPosts(): array
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->whereHas('posts', function($query) {
                $query->where('status', 'published');
            })
            ->withCount('posts')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'slug', 'description'])
            ->toArray();
    }

    /**
     * Lấy tag phổ biến (có nhiều bài viết nhất)
     */
    public function getPopularTags($limit = 20): array
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->whereHas('posts', function($query) {
                $query->where('status', 'published');
            })
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'description'])
            ->toArray();
    }

    /**
     * Lấy tag cloud (tất cả tag với số lượng bài viết)
     */
    public function getTagCloud(): array
    {
        return $this->repo->getModel()
            ->where('status', 'active')
            ->whereHas('posts', function($query) {
                $query->where('status', 'published');
            })
            ->withCount('posts')
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->get(['id', 'name', 'slug', 'posts_count'])
            ->toArray();
    }
}
