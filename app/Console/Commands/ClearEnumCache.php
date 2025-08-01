<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearEnumCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-enums {type? : The enum type to clear cache for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cache for enums';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        $enumTypes = ['user_status', 'gender', 'basic_status', 'role_status', 'product_status', 'order_status', 'variant_status'];

        if ($type) {
            // Clear cache for specific type
            if (!in_array($type, $enumTypes)) {
                $this->error("Enum type '{$type}' không hợp lệ. Các type hợp lệ: " . implode(', ', $enumTypes));
                return 1;
            }

            $cacheKey = "enums_{$type}";
            Cache::forget($cacheKey);
            $this->info("Đã xóa cache cho enum type: {$type}");
        } else {
            // Clear cache for all types
            foreach ($enumTypes as $enumType) {
                $cacheKey = "enums_{$enumType}";
                Cache::forget($cacheKey);
            }
            $this->info('Đã xóa cache cho tất cả enum types: ' . implode(', ', $enumTypes));
        }

        return 0;
    }
} 