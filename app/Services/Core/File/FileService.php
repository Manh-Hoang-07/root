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
        $this->validateFile($file, $fileType);
        
        $fileName = $this->generateFileName($file);
        $path = $this->getStoragePath($fileType);
        
        // Tạo thư mục nếu chưa tồn tại
        $this->ensureDirectoryExists($path);
        
        $storedPath = $file->storeAs($path, $fileName, 'public');
        
        return [
            'url' => '/storage/' . $storedPath,
            'path' => $storedPath,
            'filename' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'type' => $fileType
        ];
    }

    /**
     * Upload multiple files
     */
    public function uploadMultipleFiles(array $files): array
    {
        $results = [];
        
        // Tạo thư mục trước khi upload tất cả file
        $this->ensureTodayDirectoriesExist();
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $results[] = $this->uploadFile($file);
            }
        }
        
        return $results;
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
    protected function validateFile(UploadedFile $file, string $fileType): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Kiểm tra extension
        if ($fileType !== 'general' && !in_array($extension, $this->allowedTypes[$fileType])) {
            throw new \InvalidArgumentException("Định dạng file không được hỗ trợ cho loại {$fileType}");
        }
        
        // Kiểm tra kích thước
        $maxSize = $this->maxSizes[$fileType] ?? 10; // MB
        $maxSizeBytes = $maxSize * 1024 * 1024;
        
        if ($file->getSize() > $maxSizeBytes) {
            throw new \InvalidArgumentException("Kích thước file không được vượt quá {$maxSize}MB");
        }
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
    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        
        return false;
    }

    /**
     * Lấy thông tin file
     */
    public function getFileInfo(string $path): ?array
    {
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }
        
        $fullPath = Storage::disk('public')->path($path);
        
        return [
            'path' => $path,
            'url' => '/storage/' . $path,
            'size' => filesize($fullPath),
            'mime_type' => mime_content_type($fullPath),
            'last_modified' => filemtime($fullPath)
        ];
    }

    /**
     * Lấy danh sách file trong thư mục
     */
    public function listFiles(string $fileType = null, string $date = null): array
    {
        $path = "files";
        if ($fileType) {
            $path .= "/{$fileType}";
        }
        if ($date) {
            $path .= "/{$date}";
        }
        
        $files = Storage::disk('public')->files($path);
        $result = [];
        
        foreach ($files as $file) {
            $info = $this->getFileInfo($file);
            if ($info) {
                $result[] = $info;
            }
        }
        
        return $result;
    }

    /**
     * Lấy danh sách các ngày có file
     */
    public function getAvailableDates(string $fileType = null): array
    {
        $path = "files";
        if ($fileType) {
            $path .= "/{$fileType}";
        }
        
        $directories = Storage::disk('public')->directories($path);
        $dates = [];
        
        foreach ($directories as $dir) {
            $dirName = basename($dir);
            // Kiểm tra format yyyymmdd
            if (preg_match('/^\d{8}$/', $dirName)) {
                $dates[] = $dirName;
            }
        }
        
        // Sắp xếp từ mới nhất đến cũ nhất
        rsort($dates);
        
        return $dates;
    }
}
