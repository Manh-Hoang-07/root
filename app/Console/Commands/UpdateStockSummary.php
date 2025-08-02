<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Inventory\StockSummaryService;

class UpdateStockSummary extends Command
{
    protected $signature = 'inventory:update-summary {--product-id= : Update specific product}';
    protected $description = 'Cập nhật bảng tổng hợp tồn kho';

    protected $stockSummaryService;

    public function __construct(StockSummaryService $stockSummaryService)
    {
        parent::__construct();
        $this->stockSummaryService = $stockSummaryService;
    }

    public function handle()
    {
        $productId = $this->option('product-id');

        if ($productId) {
            $this->info("Đang cập nhật tổng hợp cho sản phẩm ID: {$productId}");
            $this->stockSummaryService->updateProductSummary($productId);
            $this->info('✅ Hoàn thành!');
        } else {
            $this->info('Đang cập nhật tổng hợp cho tất cả sản phẩm...');
            $this->stockSummaryService->updateAllSummaries();
            $this->info('✅ Hoàn thành cập nhật tổng hợp!');
        }
    }
} 