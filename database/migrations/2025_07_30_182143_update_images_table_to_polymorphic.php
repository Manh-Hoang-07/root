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
        Schema::table('images', function (Blueprint $table) {
            // Drop existing foreign keys
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variant_id']);
            
            // Drop existing columns
            $table->dropColumn(['product_id', 'variant_id', 'alt', 'sort_order']);
            
            // Add polymorphic columns
            $table->string('imageable_type');
            $table->unsignedBigInteger('imageable_id');
            
            // Add index for polymorphic relationship
            $table->index(['imageable_type', 'imageable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            // Drop polymorphic columns
            $table->dropIndex(['imageable_type', 'imageable_id']);
            $table->dropColumn(['imageable_type', 'imageable_id']);
            
            // Restore original columns
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('variant_id')->nullable();
            $table->string('alt')->nullable();
            $table->integer('sort_order');
            
            // Restore foreign keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('variants')->onDelete('cascade');
        });
    }
};
