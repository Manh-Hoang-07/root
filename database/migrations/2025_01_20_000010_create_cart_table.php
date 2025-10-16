<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('session_id', 100)->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->integer('quantity');
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->nullOnDelete();
            $table->foreign('created_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_user_id')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('user_id');
            $table->index('session_id');
            $table->index('product_id');
            $table->index('product_variant_id');
            $table->index('quantity');
            $table->index('created_at');
            $table->index(['user_id', 'product_id', 'product_variant_id']);
            $table->index(['session_id', 'product_id', 'product_variant_id']);
            $table->index('created_user_id');
            $table->index('updated_user_id');

            // Unique constraints
            $table->unique(['user_id', 'product_id', 'product_variant_id'], 'unique_user_cart_item');
            $table->unique(['session_id', 'product_id', 'product_variant_id'], 'unique_session_cart_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart');
    }
};
