<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Trait for centralized error logging
 * 
 * Provides common logging functionality with consistent format
 * and additional context support.
 * 
 * @package App\Traits
 */
trait LoggingTrait
{
    /**
     * Log error with common context
     * 
     * @param string $operation
     * @param Exception $exception
     * @param array $additionalData
     * @return void
     */
    protected function logError(string $operation, Exception $exception, array $additionalData = []): void
    {
        $logData = array_merge([
            'controller' => static::class,
            'operation' => $operation,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ], $additionalData);

        Log::error("{$operation} operation failed", $logData);
    }
} 