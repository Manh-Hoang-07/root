<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('sku', 100)->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('sale_price', 15, 2)->nullable();
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(0);
            $table->decimal('weight', 8, 2)->nullable(); // kg
            $table->json('dimensions')->nullable(); // {length, width, height}
            $table->string('image', 500)->nullable();
            $table->json('gallery')->nullable(); // Array of image paths
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_variable')->default(false);
            $table->boolean('is_digital')->default(false);
            $table->integer('download_limit')->nullable(); // For digital products
            $table->string('meta_title', 255)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->string('og_title', 255)->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 500)->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            // Foreign keys
            $table->foreign('created_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_user_id')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('name');
            $table->index('slug');
            $table->index('sku');
            $table->index('price');
            $table->index('sale_price');
            $table->index('stock_quantity');
            $table->index('status');
            $table->index('is_featured');
            $table->index('is_variable');
            $table->index('is_digital');
            $table->index('created_at');
            $table->index(['status', 'is_featured']);
            $table->index(['status', 'created_at']);
            $table->index(['price', 'status']);
            $table->index('created_user_id');
            $table->index('updated_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
