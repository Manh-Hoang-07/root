<?php

namespace App\Traits;

/**
 * Trait for data optimization operations
 * 
 * Provides methods for optimized data loading, caching, and query optimization
 * 
 * @package App\Traits
 */
trait OptimizationTrait
{
    /**
     * Get optimized data with common logic
     * @param array $filters
     * @param int $limit
     * @param string $context
     * @param bool $single
     * @return mixed
     */
    protected function getData(array $filters, int $perPage, string $context = 'index', bool $single = false)
    {
        // Check caching
        if ($this->cacheService->shouldCache()) {
            $cacheKey = $this->cacheService->generateKey($filters, $perPage, $context, $single, static::class);
            $cachedData = $this->cacheService->get($cacheKey);
            if ($cachedData !== null) {
                return $cachedData;
            }
        }
        // Parse relations from request
        $requestRelations = $this->parseRels($filters['relations'] ?? null);
        // Use context-specific relations if no relations requested
        $defaultRelations = $context === 'show' ? $this->showRelations : $this->indexRelations;
        $relations = !empty($requestRelations) ? $requestRelations : $defaultRelations;
        // Parse fields from request
        $requestFields = $filters['fields'] ?? null;
        $fields = $this->parseFields($requestFields);
        // Optimize: Use default fields if none specified
        if (empty($fields) || $fields === ['*']) {
            $fields = $context === 'show' ? $this->getShowFields() : $this->getListFields();
        }
        // Remove non-filter parameters
        unset($filters['relations'], $filters['fields'], $filters['per_page']);
        if ($single) {
            $data = $this->service->find($filters['id'], $relations, $fields);
        } else {
            $data = $this->service->list($filters, $perPage, $relations, $fields);
        }
        // Cache the response if enabled
        if ($this->cacheService->shouldCache()) {
            $this->cacheService->put($cacheKey, $data);
        }
        return $data;
    }

    /**
     * Parse relations from request
     * @param mixed $relations
     * @return array
     */
    protected function parseRels($relations): array
    {
        if (is_array($relations)) {
            return $relations;
        }
        if (is_string($relations)) {
            return array_filter(array_map('trim', explode(',', $relations)));
        }
        return [];
    }

    /**
     * Parse fields from request
     * @param mixed $fields
     * @return array
     */
    protected function parseFields($fields): array
    {
        if (is_array($fields)) {
            return $fields;
        }
        if (is_string($fields)) {
            return array_filter(array_map('trim', explode(',', $fields)));
        }
        return ['*'];
    }

    /**
     * Get default fields for list view
     * @return array
     */
    protected function getListFields(): array
    {
        return ['*'];
    }

    /**
     * Get default fields for show view
     * @return array
     */
    protected function getShowFields(): array
    {
        return ['*'];
    }

    /**
     * Get fields for search operation
     * @return array
     */
    protected function getSearchFields(): array
    {
        return ['id', 'name'];
    }

    /**
     * Get relations for search operation
     * @return array
     */
    protected function getSearchRels(): array
    {
        return [];
    }
}
