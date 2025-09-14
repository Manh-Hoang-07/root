<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Traits\BaseFilterTrait;
use App\Repositories\Traits\BaseSearchTrait;
use App\Repositories\Traits\BaseSortingTrait;
use App\Repositories\Traits\BaseConditionTrait;
use App\Repositories\Traits\BaseQueryTrait;

/**
 * Base Repository class providing common database operations
 * 
 * @package App\Repositories
 */
abstract class BaseRepository
{
    use BaseFilterTrait, BaseSearchTrait, BaseSortingTrait, BaseConditionTrait, BaseQueryTrait;

    protected Model $model;

    /**
     * Constructor - Initialize the model
     */
    public function __construct()
    {
        $this->model = app($this->model());
    }

    /**
     * Get the model class name
     * @return string
     */
    abstract public function model();

    /**
     * Get all records with pagination and filtering
     */
    public function all(array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*']): array
    {
        $query = $this->buildQuery($relations, $fields);
        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters);
        return $this->formatPagination($query->paginate($perPage));
    }

    /**
     * Find a record by ID
     */
    public function find($id, array $relations = [], array $fields = ['*']): ?array
    {
        $model = $this->buildQuery($relations, $fields)->find($id);
        return $model ? $model->toArray() : null;
    }

    /**
     * Create a new record
     */
    public function create(array $data): array
    {
        return $this->model->create($data)->toArray();
    }

    /**
     * Update a record by ID
     */
    public function update($id, array $data): ?array
    {
        $model = $this->model->find($id);
        if (!$model) {
            return null;
        }
        
        $model->update($data);
        // Reload model to get updated data without extra query
        $model->refresh();
        return $model->toArray();
    }

    /**
     * Delete a record by ID
     */
    public function delete($id): bool
    {
        $model = $this->model->find($id);
        if (!$model) {
            return false;
        }
        return $model->delete();
    }

    /**
     * Check if record exists
     * @param mixed $id
     * @return bool
     */
    public function exists($id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Get first record or create if not exists
     */
    public function firstOrCreate(array $data): array
    {
        return $this->model->firstOrCreate($data)->toArray();
    }

    /**
     * Bulk insert records
     */
    public function bulkInsert(array $data): bool
    {
        return $this->model->insert($data);
    }

    /**
     * Get the model instance
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Search records with advanced filtering (alias for all method)
     */
    public function search(array $filters = [], int $perPage = 20, array $relations = [], array $fields = ['*']): array
    {
        return $this->all($filters, $perPage, $relations, $fields);
    }

    /**
     * Update records by condition
     */
    public function updateBy(array $conditions, array $data): int
    {
        $query = $this->model->query();
        $this->applyConditions($query, $conditions);
        return $query->update($data);
    }

    /**
     * Get records by condition (without pagination)
     */
    public function getBy(array $conditions = [], array $relations = [], array $fields = ['*']): array
    {
        $query = $this->buildQuery($relations, $fields);
        $this->applyConditions($query, $conditions);
        return $query->get()->toArray();
    }

    /**
     * Count records by condition
     */
    public function countBy(array $conditions = []): int
    {
        $query = $this->model->query();
        $this->applyConditions($query, $conditions);
        return $query->count();
    }

    /**
     * Soft delete a record (if model supports it)
     */
    public function softDelete($id): bool
    {
        $model = $this->model->find($id);
        if (!$model) {
            return false;
        }
        
        // Check if model uses SoftDeletes trait
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
            return false;
        }
        
        return $model->delete();
    }

    /**
     * Restore a soft deleted record (if model supports it)
     */
    public function restore($id): bool
    {
        $model = $this->model->withTrashed()->find($id);
        if (!$model) {
            return false;
        }
        
        // Check if model uses SoftDeletes trait
        if (!in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
            return false;
        }
        
        return $model->restore();
    }
} 