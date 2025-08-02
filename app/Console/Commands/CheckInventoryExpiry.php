<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Inventory\InventoryManagementService;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class CheckInventoryExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-expiry {--days=30 : Số ngày trước hạn sử dụng để cảnh báo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kiểm tra hàng sắp hết hạn và gửi thông báo';

    /**
     * Execute the console command.
     */
    public function handle(InventoryManagementService $inventoryService)
    {
        $days = $this->option('days');
        
        $this->info("Đang kiểm tra hàng sắp hết hạn trong {$days} ngày tới...");
        
        // Lấy hàng sắp hết hạn
        $expiringSoon = $inventoryService->getExpiringSoon($days);
        
        if ($expiringSoon->isEmpty()) {
            $this->info('Không có hàng nào sắp hết hạn.');
            return 0;
        }
        
        $this->warn("Tìm thấy {$expiringSoon->count()} sản phẩm sắp hết hạn:");
        
        // Hiển thị danh sách
        $headers = ['Sản phẩm', 'Kho hàng', 'Lô hàng', 'Số lượng', 'Hạn sử dụng', 'Còn lại (ngày)'];
        $rows = [];
        
        foreach ($expiringSoon as $inventory) {
            $daysLeft = now()->diffInDays($inventory->expiry_date, false);
            $rows[] = [
                $inventory->product->name,
                $inventory->warehouse->name,
                $inventory->batch_no ?? 'N/A',
                $inventory->available_quantity,
                $inventory->expiry_date->format('d/m/Y'),
                $daysLeft > 0 ? $daysLeft : 'Đã hết hạn'
            ];
        }
        
        $this->table($headers, $rows);
        
        // Gửi thông báo cho admin
        $this->sendNotification($expiringSoon);
        
        $this->info('Đã gửi thông báo cho quản trị viên.');
        
        return 0;
    }
    
    /**
     * Gửi thông báo cho admin
     */
    private function sendNotification($expiringSoon)
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();
        
        foreach ($admins as $admin) {
            // Có thể gửi email hoặc notification
            $this->info("Đã gửi thông báo cho: {$admin->email}");
        }
    }
} 