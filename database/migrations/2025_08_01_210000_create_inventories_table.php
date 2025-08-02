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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->integer('quantity')->default(0)->comment('Tổng số lượng');
            $table->string('batch_no')->nullable()->comment('Mã lô hàng');
            $table->string('lot_no')->nullable()->comment('Số lô sản xuất');
            $table->date('expiry_date')->nullable()->comment('Hạn sử dụng');
            $table->integer('reserved_quantity')->default(0)->comment('Số lượng đã giữ chỗ');
            $table->integer('available_quantity')->default(0)->comment('Số lượng có thể bán');
            $table->decimal('cost_price', 15, 2)->nullable()->comment('Giá vốn');
            $table->timestamps();
            
            // Unique constraint để tránh duplicate
            $table->unique(['product_id', 'warehouse_id', 'batch_no'], 'inventory_unique');
            
            // Indexes cho performance
            $table->index(['product_id']);
            $table->index(['warehouse_id']);
            $table->index(['batch_no']);
            $table->index(['lot_no']);
            $table->index(['expiry_date']);
            $table->index(['reserved_quantity']);
            $table->index(['available_quantity']);
            $table->index(['quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
}; 