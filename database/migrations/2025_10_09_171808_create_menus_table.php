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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('api')->nullable();
            $table->string('path')->nullable();
            $table->string('icon')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->json('permissions')->nullable();
            $table->enum('type', ['admin', 'public'])->default('admin');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            // Indexes
            $table->index('title');
            $table->index('api');
            $table->index('path');
            $table->index('parent_id');
            $table->index('sort_order');
            $table->index('status');
            $table->index('created_at');
            $table->index(['parent_id', 'sort_order']);
            $table->index(['status', 'sort_order']);
            $table->index('created_user_id');
            $table->index('updated_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
