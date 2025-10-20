<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('cart_id'); // Reference to cart_headers.id
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->string('product_name', 255);
            $table->string('product_sku', 100);
            $table->string('variant_name')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->json('product_attributes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            // Foreign keys
            $table->foreign('cart_id')->references('id')->on('cart_headers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->nullOnDelete();
            $table->foreign('created_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_user_id')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('cart_id');
            $table->index('product_id');
            $table->index('product_variant_id');
            $table->index('quantity');
            $table->index('created_at');
            $table->index(['cart_id', 'product_id', 'product_variant_id']);
            $table->index('created_user_id');
            $table->index('updated_user_id');

            // Unique constraints
            $table->unique(['cart_id', 'product_id', 'product_variant_id'], 'unique_cart_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
