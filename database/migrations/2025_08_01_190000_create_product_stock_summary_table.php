<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_stock_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('total_quantity')->default(0);
            $table->integer('warehouse_count')->default(0);
            $table->integer('low_stock_count')->default(0); // Số kho có tồn kho thấp
            $table->timestamp('last_updated_at');
            $table->timestamps();
            
            $table->unique('product_id');
            $table->index(['total_quantity']);
            $table->index(['low_stock_count']);
            $table->index(['last_updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_stock_summary');
    }
}; 