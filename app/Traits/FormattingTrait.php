<?php

namespace App\Traits;

/**
 * Trait for data formatting operations
 * 
 * Provides methods for formatting API responses, timestamps, and relationships
 * 
 * @package App\Traits
 */
trait FormattingTrait
{
    /**
     * Format response based on type
     * @param mixed $data
     * @param string $type
     * @return array
     */
    protected function format($data, string $type = 'collection')
    {
        if ($type === 'single') {
            return $this->formatSingle($data);
        }
        // Check if data is a paginated collection (Laravel pagination object)
        if (method_exists($data, 'toArray')) {
            $dataArray = $data->toArray();
            // Check for Laravel pagination structure
            if (isset($dataArray['data']) && (isset($dataArray['links']) || isset($dataArray['meta']))) {
                // Format the data array within pagination
                $dataArray['data'] = $this->formatCollection($dataArray['data']);
                // If meta data is at top level, move it to meta key
                if (!isset($dataArray['meta']) && isset($dataArray['current_page'])) {
                    $metaKeys = ['current_page', 'from', 'last_page', 'last_page_url', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to', 'total'];
                    $meta = [];
                    foreach ($metaKeys as $key) {
                        if (isset($dataArray[$key])) {
                            $meta[$key] = $dataArray[$key];
                            unset($dataArray[$key]);
                        }
                    }
                    $dataArray['meta'] = $meta;
                }
                return $dataArray;
            }
        }
        return $this->formatCollection($data);
    }

    /**
     * Format single data without Resource
     * @param mixed $data
     * @return array
     */
    protected function formatSingle($data)
    {
        if (!$data) {
            return null;
        }
        // If data is already an array, return as is
        if (is_array($data)) {
            return $data;
        }
        // Convert model to array with proper formatting
        $formatted = $data->toArray();
        // Format timestamps consistently
        $formatted = $this->formatTimes($formatted);
        // Format relationships if they exist
        $formatted = $this->formatRels($formatted, $data);
        return $formatted;
    }

    /**
     * Format collection data without Resource
     * @param mixed $data
     * @return array
     */
    protected function formatCollection($data)
    {
        if (!$data) {
            return [];
        }
        // If data is already an array, return as is
        if (is_array($data)) {
            return $data;
        }
        // Convert collection to array
        if (method_exists($data, 'map')) {
            $formatted = $data->map(function ($item) {
                return $this->formatSingle($item);
            })->toArray();
        } else {
            $formatted = $data;
        }
        return $formatted;
    }

    /**
     * Format timestamps in data
     * @param array $data
     * @return array
     */
    protected function formatTimes(array $data): array
    {
        $timestampFields = ['created_at', 'updated_at', 'deleted_at', 'email_verified_at', 'phone_verified_at', 'last_login_at'];
        foreach ($timestampFields as $field) {
            if (isset($data[$field]) && $data[$field]) {
                if (is_string($data[$field])) {
                    $data[$field] = date('Y-m-d H:i:s', strtotime($data[$field]));
                } elseif (is_object($data[$field]) && method_exists($data[$field], 'toDateTimeString')) {
                    $data[$field] = $data[$field]->toDateTimeString();
                }
            }
        }
        return $data;
    }

    /**
     * Format relationships in data
     * @param array $data
     * @param mixed $originalData
     * @return array
     */
    protected function formatRels(array $data, $originalData): array
    {
        // Handle common relationships
        $relationshipFields = ['profile', 'roles', 'brand', 'categories', 'variants', 'images'];
        foreach ($relationshipFields as $field) {
            if (isset($data[$field])) {
                // Chỉ format nếu relation đã được eager loaded để tránh N+1 query
                if ($originalData && method_exists($originalData, 'relationLoaded') && $originalData->relationLoaded($field)) {
                    if (is_object($data[$field]) && method_exists($data[$field], 'toArray')) {
                        $data[$field] = $data[$field]->toArray();
                    } elseif (is_array($data[$field])) {
                        // Handle collection relationships
                        $data[$field] = array_map(function ($item) {
                            if (is_object($item) && method_exists($item, 'toArray')) {
                                return $item->toArray();
                            }
                            return $item;
                        }, $data[$field]);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Check if data is paginated
     * @param mixed $data
     * @return bool
     */
    protected function isPaginated($data): bool
    {
        if (!method_exists($data, 'toArray')) {
            return false;
        }
        $dataArray = $data->toArray();
        return isset($dataArray['data']) && (isset($dataArray['links']) || isset($dataArray['meta']));
    }

    /**
     * Format paginated response
     * @param mixed $data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function formatPaginated($data)
    {
        $dataArray = $data->toArray();
        $dataArray['data'] = $this->formatCollection($dataArray['data']);
        $dataArray = $this->normalizeMeta($dataArray);
        return response()->json($dataArray);
    }

    /**
     * Normalize pagination meta data
     * @param array $dataArray
     * @return array
     */
    protected function normalizeMeta(array $dataArray): array
    {
        if (!isset($dataArray['meta']) && isset($dataArray['current_page'])) {
            $metaKeys = ['current_page', 'from', 'last_page', 'last_page_url', 'next_page_url', 'path', 'per_page', 'prev_page_url', 'to', 'total'];
            $meta = [];
            foreach ($metaKeys as $key) {
                if (isset($dataArray[$key])) {
                    $meta[$key] = $dataArray[$key];
                    unset($dataArray[$key]);
                }
            }
            $dataArray['meta'] = $meta;
        }
        return $dataArray;
    }
}
