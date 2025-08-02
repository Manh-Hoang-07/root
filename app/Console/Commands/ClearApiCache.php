<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ClearApiCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-api {--pattern=api_*} {--controller=} {--model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear API cache by pattern, controller, or model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pattern = $this->option('pattern');
        $controller = $this->option('controller');
        $model = $this->option('model');
        
        if ($controller) {
            $pattern = 'api_' . $controller . '_*';
        } elseif ($model) {
            $pattern = strtolower($model) . '_*';
        }
        
        $this->info("Clearing cache with pattern: {$pattern}");
        
        $clearedCount = 0;
        
        // Clear specific cache keys
        $keys = Cache::get($pattern) ?: [];
        foreach ($keys as $key) {
            Cache::forget($key);
            $clearedCount++;
        }
        
        // Also clear by pattern (if using Redis)
        if (config('cache.default') === 'redis') {
            try {
                $redis = Cache::getRedis();
                $keys = $redis->keys($pattern);
                foreach ($keys as $key) {
                    $redis->del($key);
                    $clearedCount++;
                }
            } catch (\Exception $e) {
                $this->warn("Failed to clear Redis cache by pattern: " . $e->getMessage());
            }
        }
        
        $this->info("Cleared {$clearedCount} cache entries");
        
        Log::info('API cache cleared via command', [
            'pattern' => $pattern,
            'cleared_count' => $clearedCount,
            'user' => 'console'
        ]);
        
        return 0;
    }
} 