<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');

            // Indexes
            $table->index('product_id');
            $table->index('category_id');
            $table->index('created_at');

            // Unique constraint
            $table->unique(['product_id', 'category_id'], 'unique_product_category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_category');
    }
};
