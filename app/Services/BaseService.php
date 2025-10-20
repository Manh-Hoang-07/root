<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


abstract class BaseService
{
    /**
     * @var BaseRepository
     */
    protected $repo;
    protected static array $fields = ['*'];

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
        try {
            $result = $this->repo->create($data);
            $this->onCreateSuccess($result, $data);
            
            return [
                'success' => true,
                'message' => 'Tạo dữ liệu thành công',
                'data' => $result
            ];
        } catch (\Exception $e) {
            $this->onCreateFail($data);
            
            return [
                'success' => false,
                'message' => 'Không thể tạo dữ liệu',
                'data' => null
            ];
        }
    }

    public function update($id, $data): array
    {
        try {
            $result = $this->repo->update($id, $data);
            if ($result) {
                $this->onUpdateSuccess($result, $id, $data);
                
                return [
                    'success' => true,
                    'message' => 'Cập nhật dữ liệu thành công',
                    'data' => $result
                ];
            } else {
                $this->onUpdateFail($id, $data);
                
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy dữ liệu để cập nhật',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            $this->onUpdateFail($id, $data);
            
            return [
                'success' => false,
                'message' => 'Không thể cập nhật dữ liệu',
                'data' => null
            ];
        }
    }

    public function delete($id): array
    {
        try {
            $item = $this->find($id);
            if (!$item) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy dữ liệu để xóa',
                    'data' => null
                ];
            }
            
            $result = $this->repo->delete($id);
            if ($result) {
                $this->onDeleteSuccess($item, $id);
                
                return [
                    'success' => true,
                    'message' => 'Xóa dữ liệu thành công',
                    'data' => null
                ];
            } else {
                $this->onDeleteFail($id, $item);
                
                return [
                    'success' => false,
                    'message' => 'Không thể xóa dữ liệu',
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            $this->onDeleteFail($id, $item ?? null);
            
            return [
                'success' => false,
                'message' => 'Không thể xóa dữ liệu',
                'data' => null
            ];
        }
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

    /**
     * Get records by condition (without pagination)
     */
    public function getBy(array $conditions = [], array $relations = [], array $fields = ['*']): array
    {
        return $this->repo->getBy($conditions, $relations, $fields);
    }

    /**
     * Create or update record by conditions
     */
    public function createOrUpdate(array $conditions, array $data): array
    {
        try {
            $result = $this->repo->createOrUpdate($conditions, $data);
            $this->onCreateOrUpdateSuccess($result, $conditions, $data);
            
            return [
                'success' => true,
                'message' => 'Tạo hoặc cập nhật dữ liệu thành công',
                'data' => $result
            ];
        } catch (\Exception $e) {
            $this->onCreateOrUpdateFail($conditions, $data);
            
            return [
                'success' => false,
                'message' => 'Không thể tạo hoặc cập nhật dữ liệu',
                'data' => null
            ];
        }
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

    /**
     * Hook method called when create operation succeeds
     * Override in child classes to add custom logic
     * 
     * @param array $result The created record
     * @param array $data The original data used for creation
     */
    protected function onCreateSuccess(array $result, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when create operation fails
     * Override in child classes to add custom error handling
     * 
     * @param array $data The original data used for creation
     */
    protected function onCreateFail(array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when update operation succeeds
     * Override in child classes to add custom logic
     * 
     * @param array $result The updated record
     * @param mixed $id The ID of the updated record
     * @param array $data The original data used for update
     */
    protected function onUpdateSuccess(array $result, $id, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when update operation fails
     * Override in child classes to add custom error handling
     * 
     * @param mixed $id The ID of the record that failed to update
     * @param array $data The original data used for update
     */
    protected function onUpdateFail($id, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when delete operation succeeds
     * Override in child classes to add custom logic
     * 
     * @param array|null $item The deleted record (before deletion)
     * @param mixed $id The ID of the deleted record
     */
    protected function onDeleteSuccess(?array $item, $id): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when delete operation fails
     * Override in child classes to add custom error handling
     * 
     * @param mixed $id The ID of the record that failed to delete
     * @param array|null $item The record that failed to delete
     */
    protected function onDeleteFail($id, ?array $item): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when createOrUpdate operation succeeds
     * Override in child classes to add custom logic
     * 
     * @param array $result The created/updated record
     * @param array $conditions The conditions used for lookup
     * @param array $data The original data used for create/update
     */
    protected function onCreateOrUpdateSuccess(array $result, array $conditions, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook method called when createOrUpdate operation fails
     * Override in child classes to add custom error handling
     * 
     * @param array $conditions The conditions used for lookup
     * @param array $data The original data used for create/update
     */
    protected function onCreateOrUpdateFail(array $conditions, array $data): void
    {
        // Override in child classes if needed
    }

    /**
     * Update any field of a record
     *
     * @param mixed $id The ID of the record
     * @param mixed $value The new value
     * @param string $field The field name
     * @return array The result with success, message and data
     */
    public function updateField($id, $value, string $field): array
    {
        try {
            $result = $this->repo->update($id, [$field => $value]);
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => "Cập nhật trường {$field} thành công",
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Không thể cập nhật trường {$field}",
                    'data' => null
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Không thể cập nhật trường {$field}",
                'data' => null
            ];
        }
    }

    /**
     * Update status of a record (only if status is different)
     * 
     * @param mixed $id The ID of the record
     * @param mixed $newStatus The new status value
     * @return array|null The updated record or null if not found
     */
    public function updateStatus($id, $newStatus): array
    {
        // Get current record
        $currentRecord = $this->find($id);
        if (!$currentRecord) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy dữ liệu để cập nhật',
                'data' => null
            ];
        }

        // Check if status is different
        $currentStatus = $currentRecord['status'] ?? null;
        if ($currentStatus === $newStatus) {
            // Status is the same, return current record without update
            return [
                'success' => true,
                'message' => 'Trạng thái không thay đổi',
                'data' => $currentRecord
            ];
        }

        // Status is different, update it
        $result = $this->updateField($id, $newStatus, 'status');
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $result['data']
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Không thể cập nhật trạng thái',
                'data' => null
            ];
        }
    }
}
