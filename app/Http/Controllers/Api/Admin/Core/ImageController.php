<?php

namespace App\Http\Controllers\Api\Admin\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    /**
     * Upload image
     */
    public function upload(Request $request)
    {
        // Debug: Log that method was called
        file_put_contents(storage_path('logs/debug.log'), 
            date('Y-m-d H:i:s') . " - Upload method called\n" .
            date('Y-m-d H:i:s') . " - Request has file: " . ($request->hasFile('image') ? 'YES' : 'NO') . "\n",
            FILE_APPEND
        );
        
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        try {
            $file = $request->file('image');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Log file info for debugging
            Log::info('Uploading file:', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'file_name' => $fileName
            ]);
            
            // Debug disk configuration
            Log::info('Disk configuration:', [
                'default_disk' => config('filesystems.default'),
                'public_disk_root' => config('filesystems.disks.public.root'),
                'public_disk_url' => config('filesystems.disks.public.url'),
                'storage_path' => storage_path('app/public'),
                'storage_exists' => file_exists(storage_path('app/public')),
                'storage_writable' => is_writable(storage_path('app/public'))
            ]);
            
            // Check if storage directory exists and is writable
            $storagePath = storage_path('app/public/images');
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
                Log::info('Created storage directory: ' . $storagePath);
            }
            
            if (!is_writable($storagePath)) {
                throw new \Exception('Storage directory is not writable: ' . $storagePath);
            }
            
            // Upload to storage/app/public/images using public disk
            $path = $file->storeAs('images', $fileName, 'public');
            
            // Debug: Check where file was actually saved
            $actualPath = storage_path('app/public/' . $path);
            $fileExists = file_exists($actualPath);
            
            // Force log to file
            file_put_contents(storage_path('logs/debug.log'), 
                date('Y-m-d H:i:s') . " - File stored at: $path\n" .
                date('Y-m-d H:i:s') . " - Actual path: $actualPath\n" .
                date('Y-m-d H:i:s') . " - File exists: " . ($fileExists ? 'YES' : 'NO') . "\n" .
                date('Y-m-d H:i:s') . " - File size: " . ($fileExists ? filesize($actualPath) : 'N/A') . "\n\n",
                FILE_APPEND
            );
            
            // Check if file exists in public disk
            if (!Storage::disk('public')->exists($path)) {
                Log::error('File not found in public disk: ' . $path);
                
                // Check if file exists in local disk
                if (Storage::disk('local')->exists($path)) {
                    Log::info('File found in local disk instead: ' . $path);
                    throw new \Exception('File was saved to local disk instead of public disk');
                }
                
                // Check if file exists in default disk
                if (Storage::exists($path)) {
                    Log::info('File found in default disk: ' . $path);
                    throw new \Exception('File was saved to default disk instead of public disk');
                }
                
                throw new \Exception('File was not saved properly. Path: ' . $path);
            }
            
            // Return the URL
            $url = '/storage/' . $path;
            
            // Also return base64 for immediate display
            $base64 = base64_encode(Storage::disk('public')->get($path));
            $mimeType = $file->getMimeType();
            $dataUrl = "data:$mimeType;base64,$base64";
            
            Log::info('Upload successful:', [
                'path' => $path,
                'url' => $url,
                'size' => Storage::disk('public')->size($path)
            ]);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'dataUrl' => $dataUrl
            ]);
        } catch (\Exception $e) {
            Log::error('Image upload failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải ảnh lên: ' . $e->getMessage()
            ], 500);
        }
    }
} 