<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;


abstract class BaseService
{
    protected $repo;
    
    /** @var string Repository class to be injected */
    protected static $repositoryClass;


    public function __construct($repo = null)
    {
        // Auto-resolve repository if not provided and repositoryClass is defined
        if ($repo === null && static::$repositoryClass) {
            $this->repo = app(static::$repositoryClass);
        } else {
            $this->repo = $repo;
        }
    }

    public function list($filters = [], $perPage = 20, $relations = [], $fields = ['*']): array
    {
        return $this->repo->all($filters, $perPage, $relations, $fields);
    }

    public function find($id, $relations = [], $fields = ['*']): ?array
    {
        return $this->repo->find($id, $relations, $fields);
    }

    public function create($data): array
    {
        $data = $this->handleImageUpload($data);
        $result = $this->repo->create($data);
        
        return $result;
    }

    public function update($id, $data): ?array
    {
        $data = $this->handleImageUpload($data);
        $result = $this->repo->update($id, $data);
        
        return $result;
    }

    public function delete($id): bool
    {
        $item = $this->find($id);
        $result = $this->repo->delete($id);
        
        return $result;
    }

    public function getRepo(): mixed
    {
        return $this->repo;
    }

    protected function handleImageUpload($data): array
    {
        // Xử lý tự động cho trường image nếu là file upload
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('categories', 'public');
        }
        return $data;
    }

    /**
     * Generate a unique slug for a model based on name if slug is empty.
     */
    protected function ensureSlug(array $data): array
    {
        if (!isset($data['slug']) || !$data['slug']) {
            if (isset($data['name']) && $data['name']) {
                $base = Str::slug($data['name']);
                $data['slug'] = $this->generateUniqueSlug($base);
            }
        }
        return $data;
    }

    /**
     * Ensure slug uniqueness within repository's model table.
     */
    protected function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug ?: Str::random(8);
        $model = $this->repo->getModel();
        $exists = $model->newQuery()->where('slug', $slug)->exists();
        if (!$exists) return $slug;

        $counter = 2;
        while (true) {
            $candidate = $baseSlug . '-' . $counter;
            if (!$model->newQuery()->where('slug', $candidate)->exists()) {
                return $candidate;
            }
            $counter++;
        }
    }
} 