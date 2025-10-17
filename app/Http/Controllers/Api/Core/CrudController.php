<?php

namespace App\Http\Controllers\Api\Core;

use App\Services\BaseService;
use App\Traits\LoggingTrait;
use App\Traits\ResponseTrait;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Abstract Base Controller for API endpoints
 * Provides common CRUD operations with optimized data loading,
 * flexible relations handling, and standardized response formatting.
 *
 * @package App\Http\Controllers\Api
 */
abstract class CrudController extends ListController
{
    use ResponseTrait;
    use LoggingTrait;

    /** @var string Request class for store operations */
    protected $storeRequestClass = Request::class;

    /** @var string Request class for update operations */
    protected $updateRequestClass = Request::class;

    /** @var string Request class for status update operations */
    protected $statusUpdateRequestClass = Request::class;

    // Inherit service, relations, caching, rate limit, fields configuration from ListController

    protected function getStoreRequestClass(): string { return $this->storeRequestClass; }

    protected function getUpdateRequestClass(): string { return $this->updateRequestClass; }

    protected function getStatusUpdateRequestClass(): string { return $this->statusUpdateRequestClass; }

    /**
     * Store a newly created resource
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        try {
            $request = app($this->getStoreRequestClass());
            $data = $this->service->create($request->validated());
            return $this->successResponseWithFormat($data, 'Tạo dữ liệu thành công', 201);
        } catch (ValidationException|HttpResponseException $e) {
            throw $e; // Let the framework return 422 with validation errors
        } catch (\Exception $e) {
            $this->logError('Store', $e);
            return $this->apiResponse(false, null, 'Không thể tạo dữ liệu', 500);
        }
    }

    /**
     * Update the specified resource
     * @param int|string $id
     * @return JsonResponse
     */
    public function update($id): JsonResponse
    {
        try {
            $request = app($this->getUpdateRequestClass());
            $data = $this->service->update($id, $request->validated());
            if (!$data) {
                return $this->apiResponse(false, null, '', 404);
            }
            return $this->successResponseWithFormat($data, 'Cập nhật dữ liệu thành công', 200);
        } catch (ValidationException|HttpResponseException $e) {
            throw $e; // Let the framework return 422 with validation errors
        } catch (\Exception $e) {
            $this->logError('Update', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể cập nhật dữ liệu', 500);
        }
    }
    /**
     * Remove the specified resource
     * @param int|string $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $result = $this->service->delete($id);
            if ($result) {
                return $this->apiResponse(true, null, '', 200);
            }
            return $this->apiResponse(false, null, 'Không thể xóa dữ liệu', 500);
        } catch (\Exception $e) {
            $this->logError('Destroy', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể xóa dữ liệu', 500);
        }
    }

    /**
     * Update status of a resource (only if different)
     * @param int|string $id
     * @return JsonResponse
     */
    public function updateStatus($id): JsonResponse
    {
        try {
            $request = app($this->getStatusUpdateRequestClass());
            $result = $this->service->updateStatus($id, $request->status);

            if (!$result) {
                return $this->apiResponse(false, null, 'Không tìm thấy dữ liệu để cập nhật', 404);
            }

            return $this->apiResponse(true, $result, 'Cập nhật trạng thái thành công');
        } catch (ValidationException|HttpResponseException $e) {
            throw $e; // Let the framework return 422 with validation errors
        } catch (\Exception $e) {
            $this->logError('UpdateStatus', $e, ['id' => $id]);
            return $this->apiResponse(false, null, 'Không thể cập nhật trạng thái', 500);
        }
    }

}
