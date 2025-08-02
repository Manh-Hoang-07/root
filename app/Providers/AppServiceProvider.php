<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Brand\BrandService;
use App\Services\Category\CategoryService;
use App\Services\Warehouse\WarehouseService;
use App\Services\Product\ProductService;
use App\Services\Inventory\InventoryService;
use App\Services\Inventory\StockSummaryService;
use App\Repositories\Brand\BrandRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Warehouse\WarehouseRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Inventory\InventoryRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->singleton(BrandRepository::class);
        $this->app->singleton(CategoryRepository::class);
        $this->app->singleton(WarehouseRepository::class);
        $this->app->singleton(ProductRepository::class);
        $this->app->singleton(InventoryRepository::class);

        // Register services
        $this->app->singleton(BrandService::class, function ($app) {
            return new BrandService($app->make(BrandRepository::class));
        });

        $this->app->singleton(CategoryService::class, function ($app) {
            return new CategoryService($app->make(CategoryRepository::class));
        });

        $this->app->singleton(WarehouseService::class, function ($app) {
            return new WarehouseService($app->make(WarehouseRepository::class));
        });

        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService($app->make(ProductRepository::class));
        });

        $this->app->singleton(StockSummaryService::class, function ($app) {
            return new StockSummaryService();
        });

        $this->app->singleton(InventoryService::class, function ($app) {
            return new InventoryService(
                $app->make(InventoryRepository::class),
                $app->make(ProductRepository::class),
                $app->make(WarehouseRepository::class),
                $app->make(StockSummaryService::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
