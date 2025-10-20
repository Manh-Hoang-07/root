<?php

namespace App\Services\Core\File;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Các loại file được phép upload
     */
    protected array $allowedTypes = [
        'image' => ['jpeg', 'jpg', 'png', 'gif', 'webp', 'svg'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'],
        'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'],
        'audio' => ['mp3', 'wav', 'ogg', 'aac', 'm4a'],
        'archive' => ['zip', 'rar', '7z', 'tar', 'gz'],
    ];

    /**
     * Kích thước tối đa cho từng loại file (MB)
     */
    protected array $maxSizes = [
        'image' => 10,
        'document' => 50,
        'video' => 500,
        'audio' => 100,
        'archive' => 200,
    ];

    /**
     * Upload file
     */
    public function uploadFile(UploadedFile $file): array
    {
        $fileType = $this->getFileType($file);
        $validation = $this->validateFile($file, $fileType);
        
        if (!$validation['success']) {
            return $validation;
        }
        
        $fileName = $this->generateFileName($file);
        $path = $this->getStoragePath($fileType);
        
        // Tạo thư mục nếu chưa tồn tại
        $this->ensureDirectoryExists($path);
        
        $storedPath = $file->storeAs($path, $fileName, 'public');
        
        return [
            'success' => true,
            'message' => 'Tải file lên thành công',
            'data' => [
                'url' => '/storage/' . $storedPath,
                'path' => $storedPath,
                'filename' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'type' => $fileType
            ]
        ];
    }

    /**
     * Upload multiple files
     */
    public function uploadMultipleFiles(array $files): array
    {
        $results = [];
        $errors = [];
        
        // Tạo thư mục trước khi upload tất cả file
        $this->ensureTodayDirectoriesExist();
        
        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                $result = $this->uploadFile($file);
                if ($result['success']) {
                    $results[] = $result;
                } else {
                    $errors[] = "File {$index}: " . $result['message'];
                }
            }
        }
        
        return [
            'success' => empty($errors),
            'message' => empty($errors) ? 'Tải tất cả file lên thành công' : 'Một số file tải lên thất bại',
            'data' => $results,
            'errors' => $errors
        ];
    }

    /**
     * Xác định loại file
     */
    protected function getFileType(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        foreach ($this->allowedTypes as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return $type;
            }
        }
        
        return 'general';
    }

    /**
     * Validate file
     */
    protected function validateFile(UploadedFile $file, string $fileType): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Kiểm tra extension
        if ($fileType !== 'general' && !in_array($extension, $this->allowedTypes[$fileType])) {
            return [
                'success' => false,
                'message' => "Định dạng file không được hỗ trợ cho loại {$fileType}",
                'data' => null
            ];
        }
        
        // Kiểm tra kích thước
        $maxSize = $this->maxSizes[$fileType] ?? 10; // MB
        $maxSizeBytes = $maxSize * 1024 * 1024;
        
        if ($file->getSize() > $maxSizeBytes) {
            return [
                'success' => false,
                'message' => "Kích thước file không được vượt quá {$maxSize}MB",
                'data' => null
            ];
        }
        
        return [
            'success' => true,
            'message' => 'File hợp lệ',
            'data' => null
        ];
    }

    /**
     * Tạo tên file unique
     */
    protected function generateFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = time();
        $random = Str::random(8);
        
        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Lấy đường dẫn lưu trữ theo ngày
     */
    protected function getStoragePath(string $fileType): string
    {
        $date = date('Ymd'); // Format: 20241214
        return "files/{$fileType}/{$date}";
    }

    /**
     * Đảm bảo thư mục tồn tại
     */
    protected function ensureDirectoryExists(string $path): void
    {
        $fullPath = Storage::disk('public')->path($path);
        
        if (!file_exists($fullPath)) {
            Storage::disk('public')->makeDirectory($path, 0755, true);
        }
    }

    /**
     * Tạo tất cả thư mục cho ngày hôm nay
     */
    protected function ensureTodayDirectoriesExist(): void
    {
        $date = date('Ymd');
        
        foreach (array_keys($this->allowedTypes) as $fileType) {
            $path = "files/{$fileType}/{$date}";
            $this->ensureDirectoryExists($path);
        }
    }

    /**
     * Xóa file
     */
    public function deleteFile(string $path): array
    {
        if (Storage::disk('public')->exists($path)) {
            $deleted = Storage::disk('public')->delete($path);
            return [
                'success' => $deleted,
                'message' => $deleted ? 'Xóa file thành công' : 'Xóa file thất bại',
                'data' => null
            ];
        }
        
        return [
            'success' => false,
            'message' => 'File không tồn tại',
            'data' => null
        ];
    }
}
