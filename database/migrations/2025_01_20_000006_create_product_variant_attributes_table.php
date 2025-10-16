<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->unsignedBigInteger('product_attribute_id');
            $table->unsignedBigInteger('product_attribute_value_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('product_attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
            $table->foreign('product_attribute_value_id')->references('id')->on('product_attribute_values')->onDelete('cascade');

            // Indexes
            $table->index('product_variant_id');
            $table->index('product_attribute_id');
            $table->index('product_attribute_value_id');
            $table->index(['product_variant_id', 'product_attribute_id'], 'pva_variant_attr_idx');
            $table->index('created_at');

            // Unique constraint
            $table->unique(['product_variant_id', 'product_attribute_id'], 'unique_variant_attribute');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attributes');
    }
};
