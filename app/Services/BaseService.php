<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use App\Services\Traits\HasServiceHooks;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


abstract class BaseService
{
    use HasServiceHooks;
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
        $result = [
            'success' => false,
            'message' => 'Không thể tạo dữ liệu',
            'data' => null
        ];
        
        try {
            $createResult = $this->repo->create($data);
            
            // Check if create operation was successful
            if ($createResult) {
                $this->onCreateSuccess($createResult, $data);
                
                $result = [
                    'success' => true,
                    'message' => 'Tạo dữ liệu thành công',
                    'data' => $createResult
                ];
            } else {
                $this->onCreateFail($data);
            }
        } catch (\Exception $e) {
            $this->onCreateFail($data);
        }
        
        return $result;
    }

    public function update($id, $data): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể cập nhật dữ liệu',
            'data' => null
        ];
        
        try {
            $updateResult = $this->repo->update($id, $data);
            if ($updateResult) {
                $this->onUpdateSuccess($updateResult, $id, $data);
                
                $result = [
                    'success' => true,
                    'message' => 'Cập nhật dữ liệu thành công',
                    'data' => $updateResult
                ];
            } else {
                $this->onUpdateFail($id, $data);
                $result['message'] = 'Không tìm thấy dữ liệu để cập nhật';
            }
        } catch (\Exception $e) {
            $this->onUpdateFail($id, $data);
        }
        
        return $result;
    }

    public function delete($id): array
    {
        $result = [
            'success' => false,
            'message' => 'Không thể xóa dữ liệu',
            'data' => null
        ];
        
        try {
            $item = $this->find($id);
            if (!$item) {
                $result['message'] = 'Không tìm thấy dữ liệu để xóa';
                return $result;
            }
            
            $deleteResult = $this->repo->delete($id);
            if ($deleteResult) {
                $this->onDeleteSuccess($item, $id);
                
                $result = [
                    'success' => true,
                    'message' => 'Xóa dữ liệu thành công',
                    'data' => null
                ];
            } else {
                $this->onDeleteFail($id, $item);
            }
        } catch (\Exception $e) {
            $this->onDeleteFail($id, $item ?? null);
        }
        
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
        $result = [
            'success' => false,
            'message' => 'Không thể tạo hoặc cập nhật dữ liệu',
            'data' => null
        ];
        
        try {
            $createOrUpdateResult = $this->repo->createOrUpdate($conditions, $data);
            
            // Check if createOrUpdate operation was successful
            if ($createOrUpdateResult) {
                $this->onCreateOrUpdateSuccess($createOrUpdateResult, $conditions, $data);
                
                $result = [
                    'success' => true,
                    'message' => 'Tạo hoặc cập nhật dữ liệu thành công',
                    'data' => $createOrUpdateResult
                ];
            } else {
                $this->onCreateOrUpdateFail($conditions, $data);
            }
        } catch (\Exception $e) {
            $this->onCreateOrUpdateFail($conditions, $data);
        }
        
        return $result;
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
     * Update any field of a record
     *
     * @param mixed $id The ID of the record
     * @param mixed $value The new value
     * @param string $field The field name
     * @return array The result with success, message and data
     */
    public function updateField($id, $value, string $field): array
    {
        $result = [
            'success' => false,
            'message' => "Không thể cập nhật trường {$field}",
            'data' => null
        ];
        
        try {
            $updateResult = $this->repo->update($id, [$field => $value]);
            
            if ($updateResult) {
                $result = [
                    'success' => true,
                    'message' => "Cập nhật trường {$field} thành công",
                    'data' => $updateResult
                ];
            }
        } catch (\Exception $e) {
            // Exception already handled by default result
        }
        
        return $result;
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
        $result = [
            'success' => false,
            'message' => 'Không thể cập nhật trạng thái',
            'data' => null
        ];
        
        // Get current record
        $currentRecord = $this->find($id);
        if (!$currentRecord) {
            $result['message'] = 'Không tìm thấy dữ liệu để cập nhật';
            return $result;
        }

        // Check if status is different
        $currentStatus = $currentRecord['status'] ?? null;
        if ($currentStatus === $newStatus) {
            // Status is the same, return current record without update
            $result = [
                'success' => true,
                'message' => 'Trạng thái không thay đổi',
                'data' => $currentRecord
            ];
            return $result;
        }

        // Status is different, update it
        $updateResult = $this->updateField($id, $newStatus, 'status');
        
        if ($updateResult['success']) {
            $result = [
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $updateResult['data']
            ];
        }
        
        return $result;
    }
}
