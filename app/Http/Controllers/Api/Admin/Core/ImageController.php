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
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        try {
            $file = $request->file('image');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Upload to storage/app/public/images
            $path = $file->storeAs('public/images', $fileName);
            
            // Check if file exists
            if (!Storage::exists($path)) {
                throw new \Exception('File was not saved properly');
            }
            
            // Return the URL
            $url = Storage::url($path);
            
            // Also return base64 for immediate display
            $base64 = base64_encode(Storage::get($path));
            $mimeType = Storage::mimeType($path);
            $dataUrl = "data:$mimeType;base64,$base64";
            
            // Debug log
            Log::info('Image uploaded successfully', [
                'fileName' => $fileName,
                'path' => $path,
                'url' => $url,
                'exists' => Storage::exists($path),
                'size' => Storage::size($path)
            ]);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'dataUrl' => $dataUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải ảnh lên: ' . $e->getMessage()
            ], 500);
        }
    }
} 