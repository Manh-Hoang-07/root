<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);
        $file = $request->file('image');
        $path = $file->store('images', 'public');
        $url = Storage::url($path);
        return response()->json([
            'url' => $url,
            'path' => $path,
        ]);
    }
} 