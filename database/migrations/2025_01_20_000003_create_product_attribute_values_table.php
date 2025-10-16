<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_attribute_id');
            $table->string('value', 255);
            $table->string('color_code', 7)->nullable(); // Hex color code
            $table->string('image', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            // Foreign keys
            $table->foreign('product_attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
            $table->foreign('created_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_user_id')->references('id')->on('users')->nullOnDelete();

            // Indexes
            $table->index('product_attribute_id');
            $table->index('value');
            $table->index('color_code');
            $table->index('status');
            $table->index('sort_order');
            $table->index('created_at');
            $table->index(['product_attribute_id', 'status']);
            $table->index(['product_attribute_id', 'sort_order']);
            $table->index('created_user_id');
            $table->index('updated_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
    }
};
