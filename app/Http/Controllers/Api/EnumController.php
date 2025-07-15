<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class EnumController extends Controller
{
    public function get($type)
    {
        $className = Str::studly($type);
        $enumClass = "App\\Enums\\$className";
        if (!class_exists($enumClass) || !method_exists($enumClass, 'cases')) {
            return response()->json(['error' => 'Enum not found'], 404);
        }
        $data = collect($enumClass::cases())->map(fn($case) => [
            'name' => Str::headline(strtolower($case->name)),
            'value' => $case->value
        ])->values();
        return response()->json(['data' => $data]);
    }
} 