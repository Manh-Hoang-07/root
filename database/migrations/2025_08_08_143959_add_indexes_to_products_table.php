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
        Schema::table('products', function (Blueprint $table) {
            // Indexes cho các trường thường được tìm kiếm
            $table->index('name');
            $table->index('slug');
            $table->index('brand_id');
            $table->index('status');
            $table->index('created_at');
            $table->index('updated_at');
            
            // Composite index cho search
            $table->index(['name', 'status']);
            $table->index(['brand_id', 'status']);
            
            // Fulltext index cho search (nếu database hỗ trợ)
            if (config('database.default') === 'mysql') {
                $table->fullText(['name', 'description', 'short_description']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['brand_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['name', 'status']);
            $table->dropIndex(['brand_id', 'status']);
            
            if (config('database.default') === 'mysql') {
                $table->dropFullText(['name', 'description', 'short_description']);
            }
        });
    }
};
