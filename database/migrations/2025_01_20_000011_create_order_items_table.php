<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->string('product_name', 255);
            $table->string('product_sku', 100);
            $table->string('variant_name', 255)->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->json('product_attributes')->nullable(); // Snapshot of variant attributes
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->nullOnDelete();
            $table->foreign('created_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_user_id')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('order_id');
            $table->index('product_id');
            $table->index('product_variant_id');
            $table->index('product_sku');
            $table->index('quantity');
            $table->index('unit_price');
            $table->index('total_price');
            $table->index('created_at');
            $table->index(['order_id', 'product_id']);
            $table->index('created_user_id');
            $table->index('updated_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
