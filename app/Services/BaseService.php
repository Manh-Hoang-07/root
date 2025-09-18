<?php

namespace App\Services;

use Illuminate\Support\Str;


abstract class BaseService
{
    protected $repo;


    public function __construct($repo)
    {
        $this->repo = $repo;
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
        $result = $this->repo->create($data);
        return $result;
    }

    public function update($id, $data): ?array
    {
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

    /**
     * Find a single record by multiple conditions.
     */
    public function findOneBy(array $conditions = [], array $relations = [], array $fields = ['*']): ?array
    {
        return $this->repo->findOneBy($conditions, $relations, $fields);
    }

    // Consolidated: Use findOneBy for arbitrary field lookups

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