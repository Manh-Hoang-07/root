<?php

namespace App\Services\Core\SystemConfig;

use App\Enums\ConfigType;
use App\Enums\ConfigGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ConfigValidationService
{
    /**
     * Simple validation - trả về dữ liệu đã validate như Form Request
     * Nếu lỗi sẽ throw HttpResponseException với mã 422
     */
    public function validate(array $data): array
    {
        $rules = [
            'key' => 'required|string|max:255|regex:/^[a-zA-Z0-9._-]+$/',
            'type' => 'required|in:' . implode(',', array_column(ConfigType::getOptions(), 'value')),
            'group' => 'required|in:' . implode(',', array_column(ConfigGroup::getOptions(), 'value')),
            'value' => 'nullable',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
            'is_encrypted' => 'boolean',
            'status' => 'required|in:active,inactive',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        return $validator->validated();
    }
}