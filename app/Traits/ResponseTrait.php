<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * Success response
     */
    protected function successResponse($data = null, string $message = 'Thành công', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message = 'Có lỗi xảy ra', int $statusCode = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }

    /**
     * Not found response
     */
    protected function notFoundResponse(string $message = 'Không tìm thấy dữ liệu'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 404);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse(array $errors, string $message = 'Dữ liệu không hợp lệ'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], 422);
    }

    /**
     * Unauthorized response
     */
    protected function unauthorizedResponse(string $message = 'Không có quyền truy cập'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 401);
    }

    /**
     * Forbidden response
     */
    protected function forbiddenResponse(string $message = 'Truy cập bị từ chối'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 403);
    }

    /**
     * Bad request response
     */
    protected function badRequestResponse(string $message = 'Yêu cầu không hợp lệ'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 400);
    }

    /**
     * Server error response
     */
    protected function serverErrorResponse(string $message = 'Lỗi máy chủ'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 500);
    }

    /**
     * Created response
     */
    protected function createdResponse($data = null, string $message = 'Tạo thành công'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], 201);
    }

    /**
     * Updated response
     */
    protected function updatedResponse($data = null, string $message = 'Cập nhật thành công'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    /**
     * Deleted response
     */
    protected function deletedResponse(string $message = 'Xóa thành công'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }

    /**
     * No content response
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json([], 204);
    }

    /**
     * Conflict response
     */
    protected function conflictResponse(string $message = 'Xung đột dữ liệu'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 409);
    }

    /**
     * Too many requests response
     */
    protected function tooManyRequestsResponse(string $message = 'Quá nhiều yêu cầu'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 429);
    }

    /**
     * Service unavailable response
     */
    protected function serviceUnavailableResponse(string $message = 'Dịch vụ không khả dụng'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 503);
    }

    /**
     * Success response with automatic data formatting and unified structure
     */
    protected function successResponseWithFormat($data = null, string $message = 'Thành công', int $statusCode = 200): JsonResponse
    {
        $formattedData = $this->formatResponse($data);
        // Always return unified structure with success, message, data, links, meta
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $formattedData['data'] ?? $formattedData,
            'links' => $formattedData['links'] ?? [],
            'meta' => $formattedData['meta'] ?? []
        ], $statusCode);
    }

    /**
     * Format response with automatic type detection
     * @param mixed $data
     * @return array
     */
    protected function formatResponse($data)
    {
        // Auto-detect if single item or collection
        if ($this->isSingleItem($data)) {
            return $this->formatSingleData($data);
        }
        // Check if data is a paginated collection (Laravel pagination object)
        if (method_exists($data, 'toArray')) {
            $dataArray = $data->toArray();
            // Check for Laravel pagination structure
            if (isset($dataArray['data']) && (isset($dataArray['links']) || isset($dataArray['meta']))) {
                // Format the data array within pagination
                $dataArray['data'] = $this->formatCollectionData($dataArray['data']);
                // If meta data is at top level, move it to meta key
                if (!isset($dataArray['meta']) && isset($dataArray['current_page'])) {
                    $metaKeys = ['current_page', 'from', 'last_page', 'last_page_url', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to', 'total'];
                    $meta = [];
                    foreach ($metaKeys as $key) {
                        if (isset($dataArray[$key])) {
                            $meta[$key] = $dataArray[$key];
                            unset($dataArray[$key]);
                        }
                    }
                    $dataArray['meta'] = $meta;
                }
                return $dataArray;
            }
        }
        return $this->formatCollectionData($data);
    }

    /**
     * Check if data is a single item (not collection or pagination)
     * @param mixed $data
     * @return bool
     */
    protected function isSingleItem($data): bool
    {
        // If it's null or empty, treat as single
        if (!$data) {
            return true;
        }
        // If it's already an array, check if it looks like a single item
        if (is_array($data)) {
            // If it has pagination structure, it's not single
            if (isset($data['data']) && (isset($data['links']) || isset($data['meta']))) {
                return false;
            }
            // If it's a numeric array with multiple items, it's collection
            if (array_keys($data) === range(0, count($data) - 1) && count($data) > 1) {
                return false;
            }
            return true;
        }
        // If it's an object with toArray method, check if it's a model (single item)
        if (is_object($data) && method_exists($data, 'toArray')) {
            // If it has pagination methods, it's pagination
            if (method_exists($data, 'links') || method_exists($data, 'meta')) {
                return false;
            }
            // If it's a collection, it's not single
            if (method_exists($data, 'count') && method_exists($data, 'map')) {
                return false;
            }
            return true;
        } 
        return true;
    }

    /**
     * Format single data without Resource
     * @param mixed $data
     * @return array
     */
    protected function formatSingleData($data)
    {
        if (!$data) {
            return null;
        }
        // If data is already an array, return as is
        if (is_array($data)) {
            return $data;
        }
        // Convert model to array with proper formatting
        return $data->toArray();
    }

    /**
     * Format collection data without Resource
     * @param mixed $data
     * @return array
     */
    protected function formatCollectionData($data)
    {
        if (!$data) {
            return [];
        }
        // If data is already an array, return as is
        if (is_array($data)) {
            return $data;
        }
        // Convert collection to array
        if (method_exists($data, 'map')) {
            $formatted = $data->map(function ($item) {
                return $this->formatSingleData($item);
            })->toArray();
        } else {
            $formatted = $data;
        }
        return $formatted;
    }

    /**
     * Normalize pagination meta data
     * @param array $dataArray
     * @return array
     */
    protected function normalizePaginationMeta(array $dataArray): array
    {
        if (!isset($dataArray['meta']) && isset($dataArray['current_page'])) {
            $metaKeys = ['current_page', 'from', 'last_page', 'last_page_url', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to', 'total'];
            $meta = [];
            foreach ($metaKeys as $key) {
                if (isset($dataArray[$key])) {
                    $meta[$key] = $dataArray[$key];
                    unset($dataArray[$key]);
                }
            }
            $dataArray['meta'] = $meta;
        }
        return $dataArray;
    }
} 