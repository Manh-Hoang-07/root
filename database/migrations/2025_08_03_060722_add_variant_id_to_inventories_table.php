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
            // Thêm variant_id
            $table->foreignId('variant_id')->nullable()->constrained('variants')->onDelete('cascade');
            
            // Xóa unique constraint cũ
            $table->dropUnique(['product_id', 'warehouse_id']);
            
            // Thêm unique constraint mới bao gồm variant_id
            $table->unique(['product_id', 'variant_id', 'warehouse_id'], 'inventories_product_variant_warehouse_unique');
            
            // Thêm index cho variant_id
            $table->index(['variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            // Xóa unique constraint mới
            $table->dropUnique('inventories_product_variant_warehouse_unique');
            
            // Xóa index
            $table->dropIndex(['variant_id']);
            
            // Xóa cột variant_id
            $table->dropForeign(['variant_id']);
            $table->dropColumn('variant_id');
            
            // Khôi phục unique constraint cũ
            $table->unique(['product_id', 'warehouse_id']);
        });
    }
};
