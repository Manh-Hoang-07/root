<?php

namespace App\Http\Controllers\Api\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Upload image
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'image.required' => 'Vui lòng chọn file ảnh.',
            'image.image' => 'File phải là ảnh.',
            'image.mimes' => 'Định dạng ảnh không hợp lệ. Chỉ chấp nhận: jpeg, png, jpg, gif.',
            'image.max' => 'Kích thước ảnh không được vượt quá 2MB.'
        ]);

        try {
            $file = $request->file('image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/images', $fileName);

            return $this->successResponse([
                'url' => Storage::url($path),
                'path' => $path,
                'filename' => $fileName
            ], 'Upload ảnh thành công.');

        } catch (\Exception $e) {
            return $this->errorResponse('Upload ảnh thất bại: ' . $e->getMessage(), 500);
        }
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