<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description');
            $table->decimal('price', 15, 2);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->string('image');
            $table->float('weight');
            $table->float('length');
            $table->float('width');
            $table->float('height');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->json('attributes')->nullable();
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->index('deleted_at');
            $table->index('code');
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

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}; 