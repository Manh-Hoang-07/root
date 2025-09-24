<?php

namespace App\Http\Controllers\Api\Core\File;

use App\Http\Controllers\Controller;
use App\Services\Core\File\FileService;
use App\Http\Requests\Core\File\UploadFileRequest;
use App\Http\Requests\Core\File\UploadMultipleFilesRequest;
use App\Http\Requests\Core\File\ListFilesRequest;
use App\Http\Requests\Core\File\DeleteFileRequest;
use App\Traits\ResponseTrait;
use App\Traits\LoggingTrait;
use Illuminate\Http\JsonResponse;
use Exception;

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
        try {
            $file = $request->file('file');
            $result = $this->fileService->uploadFile($file);
            return $this->apiResponse(true, $result, 'Upload file thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, 'Upload file thất bại: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(UploadMultipleFilesRequest $request): JsonResponse
    {
        try {
            $files = $request->file('files');
            $results = $this->fileService->uploadMultipleFiles($files);
            return $this->apiResponse(true, $results, 'Upload ' . count($results) . ' file thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, 'Upload files thất bại: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete file
     */
    public function delete(DeleteFileRequest $request): JsonResponse
    {
        try {
            $path = $request->get('path');
            $success = $this->fileService->deleteFile($path);
            if (!$success) {
                return $this->apiResponse(false, null, 'Không thể xóa file hoặc file không tồn tại', 404);
            }
            return $this->apiResponse(true, null, 'Xóa file thành công');
        } catch (Exception $e) {
            return $this->apiResponse(false, null, 'Không thể xóa file', 500);
        }
    }
}
