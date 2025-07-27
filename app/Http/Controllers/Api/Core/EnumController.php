<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnumController extends Controller
{
    /**
     * Lấy danh sách enum theo type
     */
    public function get(Request $request, string $type): JsonResponse
    {
        $enums = [];

        switch ($type) {
            case 'user_status':
                $enums = \App\Enums\UserStatus::cases();
                break;
            case 'gender':
                $enums = \App\Enums\Gender::cases();
                break;
            case 'basic_status':
                $enums = \App\Enums\BasicStatus::cases();
                break;
            case 'role_status':
                $enums = \App\Enums\RoleStatus::cases();
                break;
            default:
                return $this->errorResponse('Loại enum không hợp lệ.', 400);
        }

        $data = collect($enums)->map(function ($enum) {
            return [
                'value' => $enum->value,
                'label' => $enum->label ?? $enum->value,
                'name' => $enum->name
            ];
        });

        return $this->successResponse($data, 'Lấy danh sách enum thành công.');
    }

    /**
     * Success response
     */
    protected function successResponse($data = null, string $message = 'Thành công', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message = 'Có lỗi xảy ra', int $status = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
} 