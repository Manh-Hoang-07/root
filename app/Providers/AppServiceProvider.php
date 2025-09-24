<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Admin\Brand\BrandService;
use App\Services\Admin\Category\CategoryService;
use App\Services\Admin\Warehouse\WarehouseService;
use App\Services\Admin\Product\ProductService;
use App\Services\Admin\Inventory\InventoryService;
use App\Services\Admin\Inventory\StockSummaryService;
use App\Services\Admin\Image\ImageService;
use App\Repositories\Brand\BrandRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Warehouse\WarehouseRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Inventory\InventoryRepository;
use App\Repositories\Image\ImageRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repositories
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
