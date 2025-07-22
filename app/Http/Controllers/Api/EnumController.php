<?php

namespace App\Http\Controllers\Api;

use App\Enums\BasicStatus;
use App\Enums\Gender;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EnumController extends Controller
{
    public function get(string $type)
    {
        $data = match ($type) {
            'UserStatus' => UserStatus::toArray(),
            'Gender' => Gender::toArray(),
            'BasicStatus' => BasicStatus::toArray(),
            default => null,
        };

        if ($data === null) {
            return response()->json(['error' => 'Enum not found'], 404);
        }

        return response()->json($data);
    }
} 