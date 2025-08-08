<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Success response
     */
    public static function success($data = null, string $message = 'Thành công', int $statusCode = 200): JsonResponse
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
    public static function error(string $message = 'Có lỗi xảy ra', int $statusCode = 500): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }

    /**
     * Not found response
     */
    public static function notFound(string $message = 'Không tìm thấy dữ liệu'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 404);
    }

    /**
     * Validation error response
     */
    public static function validationError(array $errors, string $message = 'Dữ liệu không hợp lệ'): JsonResponse
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
    public static function unauthorized(string $message = 'Không có quyền truy cập'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 401);
    }

    /**
     * Forbidden response
     */
    public static function forbidden(string $message = 'Truy cập bị từ chối'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 403);
    }

    /**
     * Bad request response
     */
    public static function badRequest(string $message = 'Yêu cầu không hợp lệ'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 400);
    }

    /**
     * Server error response
     */
    public static function serverError(string $message = 'Lỗi máy chủ'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 500);
    }

    /**
     * Created response
     */
    public static function created($data = null, string $message = 'Tạo thành công'): JsonResponse
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
    public static function updated($data = null, string $message = 'Cập nhật thành công'): JsonResponse
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
    public static function deleted(string $message = 'Xóa thành công'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }

    /**
     * No content response
     */
    public static function noContent(): JsonResponse
    {
        return response()->json([], 204);
    }

    /**
     * Conflict response
     */
    public static function conflict(string $message = 'Xung đột dữ liệu'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 409);
    }

    /**
     * Too many requests response
     */
    public static function tooManyRequests(string $message = 'Quá nhiều yêu cầu'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 429);
    }

    /**
     * Service unavailable response
     */
    public static function serviceUnavailable(string $message = 'Dịch vụ không khả dụng'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], 503);
    }

    /**
     * Custom response with data
     */
    public static function custom($data, string $message = '', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => $statusCode >= 200 && $statusCode < 300,
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Paginated response
     */
    public static function paginated($data, string $message = 'Thành công'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ]
        ], 200);
    }
} 