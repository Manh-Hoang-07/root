<?php

namespace App\Http\Controllers\Api\Core\File;

use App\Http\Controllers\Api\Core\Traits\LoggingTrait;
use App\Http\Controllers\Api\Core\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\File\DeleteFileRequest;
use App\Http\Requests\Core\File\ListFilesRequest;
use App\Http\Requests\Core\File\UploadFileRequest;
use App\Http\Requests\Core\File\UploadMultipleFilesRequest;
use App\Services\Core\File\FileService;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    use ResponseTrait;
    use LoggingTrait;

    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Upload single file
     */
    public function upload(UploadFileRequest $request): JsonResponse
    {
        $file = $request->file('file');
        $result = $this->fileService->uploadFile($file);
        
        // Kiểm tra nếu có lỗi (có key 'success' và = false)
        if (isset($result['success']) && !$result['success']) {
            return $this->apiResponse(false, null, $result['message'] ?? 'Upload file thất bại', 400);
        }
        
        return $this->apiResponse(true, $result, 'Upload file thành công');
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(UploadMultipleFilesRequest $request): JsonResponse
    {
        $files = $request->file('files');
        $result = $this->fileService->uploadMultipleFiles($files);
        
        // Service đã trả về format đầy đủ với success, message, data, errors
        // Sử dụng apiResponse với errors parameter
        $statusCode = $result['success'] ? 200 : 400;
        return $this->apiResponse(
            $result['success'],
            $result['data'] ?? null,
            $result['message'],
            $statusCode,
            $result['errors'] ?? []
        );
    }

    /**
     * Delete file
     */
    public function delete(DeleteFileRequest $request): JsonResponse
    {
        $path = $request->get('path');
        $result = $this->fileService->deleteFile($path);
        
        // Kiểm tra nếu có lỗi
        if (!$result['success']) {
            $statusCode = str_contains($result['message'], 'không tồn tại') ? 404 : 500;
            return $this->apiResponse(false, null, $result['message'], $statusCode);
        }
        
        return $this->apiResponse(true, null, 'Xóa file thành công');
    }
}
