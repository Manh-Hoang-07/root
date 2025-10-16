<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->enum('type', ['text', 'select', 'multiselect', 'color', 'image'])->default('select');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_variation')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
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
            $table->index('type');
            $table->index('is_required');
            $table->index('is_variation');
            $table->index('is_filterable');
            $table->index('status');
            $table->index('sort_order');
            $table->index('created_at');
            $table->index(['status', 'sort_order']);
            $table->index(['is_variation', 'status']);
            $table->index('created_user_id');
            $table->index('updated_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
