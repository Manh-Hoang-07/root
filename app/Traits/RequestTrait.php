<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Trait for request handling operations
 * 
 * Provides methods for request parsing, validation, and rate limiting
 * 
 * @package App\Traits
 */
trait RequestTrait
{
    /**
     * Parse and clean request data
     * @param Request $request
     * @return array
     */
    protected function parseData(Request $request): array
    {
        $data = $request->all();
        // Remove empty values
        $data = array_filter($data, function($value) {
            return $value !== '' && $value !== null;
        });
        return $data;
    }

    /**
     * Check rate limiting
     * @param Request $request
     * @return bool
     */
    protected function checkLimit(Request $request): bool
    {
        $key = $this->genLimitKey($request);
        // Simple rate limiting implementation
        $attempts = Cache::get($key, 0);
        if ($attempts >= $this->rateLimitAttempts) {
            return false;
        }
        Cache::put($key, $attempts + 1, 60); // 1 minute window
        return true;
    }

    /**
     * Generate a unique key for rate limiter
     * @param Request $request
     * @return string
     */
    protected function genLimitKey(Request $request): string
    {
        return 'rate_limit:' . $request->ip() . ':' . $request->path();
    }

    /**
     * Get validated per page value
     * @param Request $request
     * @return int
     */
    protected function getPerPage(Request $request): int
    {
        return min($request->get('per_page', $this->defaultPerPage), $this->maxPerPage);
    }
}
