<?php

namespace App\Http\Controllers\Api;

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
} 