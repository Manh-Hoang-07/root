<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop composite index if exists (using raw SQL to avoid errors)
        $indexes = DB::select("SHOW INDEX FROM products WHERE Key_name LIKE '%price%status%'");
        if (!empty($indexes)) {
            $indexName = $indexes[0]->Key_name;
            DB::statement("ALTER TABLE products DROP INDEX {$indexName}");
        }
        
        Schema::table('products', function (Blueprint $table) {
            // Drop columns (single column indexes will be automatically dropped by MySQL)
            $table->dropColumn([
                'price',
                'sale_price',
                'cost_price',
                'stock_quantity',
                'weight',
                'dimensions',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Re-add columns
            $table->decimal('price', 15, 2)->default(0)->after('short_description');
            $table->decimal('sale_price', 15, 2)->nullable()->after('price');
            $table->decimal('cost_price', 15, 2)->nullable()->after('sale_price');
            $table->integer('stock_quantity')->default(0)->after('cost_price');
            $table->decimal('weight', 8, 2)->nullable()->after('min_stock_level');
            $table->json('dimensions')->nullable()->after('weight');
            
            // Re-add indexes
            $table->index('price');
            $table->index('sale_price');
            $table->index('stock_quantity');
            $table->index(['price', 'status']);
        });
    }
};
