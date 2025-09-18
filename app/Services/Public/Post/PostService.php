<?php

namespace App\Services\Public\Post;

use App\Services\BaseService;
use App\Repositories\Post\PostRepository;

class PostService extends BaseService
{
    /**
     * @var PostRepository
     */
    protected $repo;

    public function __construct(PostRepository $repo)
    {
        parent::__construct($repo);
    }

    /**
     * Lấy danh sách bài viết công khai
     */
    public function getPublishedPosts($filters = [], $perPage = 10)
    {
        $query = $this->repo->getModel()
            ->where('status', 'published')
            ->with(['categories:id,name,slug', 'tags:id,name,slug'])
            ->orderBy('created_at', 'desc');
        // Lọc theo danh mục
        if (isset($filters['category_id'])) {
            $query->whereHas('categories', function($q) use ($filters) {
                $q->where('id', $filters['category_id']);
            });
        }
        // Lọc theo tag
        if (isset($filters['tag_id'])) {
            $query->whereHas('tags', function($q) use ($filters) {
                $q->where('id', $filters['tag_id']);
            });
        }
        // Tìm kiếm theo từ khóa
        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('content', 'like', '%' . $filters['search'] . '%');
            });
        }
        return $query->paginate($perPage);
    }

    /**
     * Lấy bài viết theo slug
     */
    public function findBySlug(string $slug)
    {
        return $this->repo->getModel()
            ->where('status', 'published')
            ->where('slug', $slug)
            ->with(['categories:id,name,slug', 'tags:id,name,slug'])
            ->first();
    }

    /**
     * Lấy bài viết liên quan
     */
    public function getRelatedPosts($postId, $limit = 5)
    {
        $post = $this->repo->getModel()->find($postId);
        if (!$post) return collect();

        $categoryIds = $post->categories->pluck('id')->toArray();

        return $this->repo->getModel()
            ->where('status', 'published')
            ->where('id', '!=', $postId)
            ->whereHas('categories', function($q) use ($categoryIds) {
                $q->whereIn('id', $categoryIds);
            })
            ->with(['categories:id,name,slug', 'tags:id,name,slug'])
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy bài viết nổi bật
     */
    public function getFeaturedPosts($limit = 5)
    {
        return $this->repo->getModel()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->with(['categories:id,name,slug', 'tags:id,name,slug'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy bài viết mới nhất
     */
    public function getLatestPosts($limit = 10)
    {
        return $this->repo->getModel()
            ->where('status', 'published')
            ->with(['categories:id,name,slug', 'tags:id,name,slug'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
