<?php

namespace App\Http\Controllers\Api\Core\File;

use App\Http\Controllers\Controller;
use App\Services\Core\FileService;
use App\Http\Requests\Core\File\UploadFileRequest;
use App\Http\Requests\Core\File\UploadMultipleFilesRequest;
use App\Http\Requests\Core\File\GetFileInfoRequest;
use App\Http\Requests\Core\File\ListFilesRequest;
use App\Http\Requests\Core\File\DeleteFileRequest;
use App\Traits\ResponseTrait;
use App\Traits\LoggingTrait;
use Illuminate\Http\Request;
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
            $this->logError('UploadFile', $e);
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
            $this->logError('UploadMultipleFiles', $e);
            return $this->apiResponse(false, null, 'Upload files thất bại: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get file info
     */
    public function getInfo(GetFileInfoRequest $request): JsonResponse
    {
        try {
            $path = $request->get('path');
            $info = $this->fileService->getFileInfo($path);
            
            if (!$info) {
                return $this->apiResponse(false, null, 'File không tồn tại', 404);
            }
            
            return $this->apiResponse(true, $info, 'Lấy thông tin file thành công');
        } catch (Exception $e) {
            $this->logError('GetFileInfo', $e, ['path' => $request->get('path')]);
            return $this->apiResponse(false, null, 'Không thể lấy thông tin file', 500);
        }
    }

    /**
     * List files
     */
    public function list(ListFilesRequest $request): JsonResponse
    {
        try {
            $type = $request->get('type');
            $date = $request->get('date');
            
            $files = $this->fileService->listFiles($type, $date);
            
            return $this->apiResponse(true, $files, 'Lấy danh sách file thành công');
        } catch (Exception $e) {
            $this->logError('ListFiles', $e, [
                'type' => $request->get('type'),
                'date' => $request->get('date')
            ]);
            return $this->apiResponse(false, null, 'Không thể lấy danh sách file', 500);
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
            $this->logError('DeleteFile', $e, ['path' => $request->get('path')]);
            return $this->apiResponse(false, null, 'Không thể xóa file', 500);
        }
    }

    /**
     * Get available dates
     */
    public function getAvailableDates(Request $request): JsonResponse
    {
        try {
            $type = $request->get('type');
            $dates = $this->fileService->getAvailableDates($type);
            
            return $this->apiResponse(true, $dates, 'Lấy danh sách ngày có file thành công');
        } catch (Exception $e) {
            $this->logError('GetAvailableDates', $e, ['type' => $request->get('type')]);
            return $this->apiResponse(false, null, 'Không thể lấy danh sách ngày', 500);
        }
    }

    /**
     * Get supported file types
     */
    public function getSupportedTypes(): JsonResponse
    {
        try {
            $types = [
                'image' => ['jpeg', 'jpg', 'png', 'gif', 'webp', 'svg'],
                'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'],
                'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'],
                'audio' => ['mp3', 'wav', 'ogg', 'aac', 'm4a'],
                'archive' => ['zip', 'rar', '7z', 'tar', 'gz'],
            ];
            
            return $this->apiResponse(true, $types, 'Lấy danh sách loại file được hỗ trợ thành công');
        } catch (Exception $e) {
            $this->logError('GetSupportedTypes', $e);
            return $this->apiResponse(false, null, 'Không thể lấy danh sách loại file', 500);
        }
    }
}
