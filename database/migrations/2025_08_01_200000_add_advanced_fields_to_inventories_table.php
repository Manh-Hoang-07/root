<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Thông tin lô hàng
            $table->string('batch_no')->nullable()->after('quantity')->comment('Mã lô hàng');
            $table->string('lot_no')->nullable()->after('batch_no')->comment('Số lô sản xuất');
            
            // Hạn sử dụng
            $table->date('expiry_date')->nullable()->after('lot_no')->comment('Hạn sử dụng');
            
            // Số lượng đã giữ chỗ và có thể bán
            $table->integer('reserved_quantity')->default(0)->after('expiry_date')->comment('Số lượng đã giữ chỗ');
            $table->integer('available_quantity')->default(0)->after('reserved_quantity')->comment('Số lượng có thể bán');
            
            // Giá vốn
            $table->decimal('cost_price', 15, 2)->nullable()->after('available_quantity')->comment('Giá vốn');
            
            // Indexes cho performance
            $table->index(['batch_no']);
            $table->index(['lot_no']);
            $table->index(['expiry_date']);
            $table->index(['reserved_quantity']);
            $table->index(['available_quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['batch_no']);
            $table->dropIndex(['lot_no']);
            $table->dropIndex(['expiry_date']);
            $table->dropIndex(['reserved_quantity']);
            $table->dropIndex(['available_quantity']);
            
            $table->dropColumn([
                'batch_no',
                'lot_no', 
                'expiry_date',
                'reserved_quantity',
                'available_quantity',
                'cost_price'
            ]);
        });
    }
}; 