<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postcategory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            $table->foreign('parent_id')->references('id')->on('postcategory')->nullOnDelete();

            // Indexes
            $table->index('name');
            $table->index('slug');
            $table->index('parent_id');
            $table->index('status');
            $table->index('sort_order');
            $table->index('created_at');
            $table->index(['status', 'sort_order']);
            $table->index(['parent_id', 'status']);
            $table->index('created_user_id');
            $table->index('updated_user_id');
        });

        Schema::create('posttag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            // Indexes
            $table->index('name');
            $table->index('slug');
            $table->index('status');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
            $table->index('created_user_id');
            $table->index('updated_user_id');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('image')->nullable();
            $table->string('cover_image')->nullable();
            $table->unsignedBigInteger('primary_postcategory_id')->nullable();
            $table->enum('status', ['draft','scheduled','published','archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->dateTime('published_at')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            $table->foreign('primary_postcategory_id')->references('id')->on('postcategory')->nullOnDelete();

            // Indexes
            $table->index('name');
            $table->index('slug');
            $table->index('primary_postcategory_id');
            $table->index('status');
            $table->index('is_featured');
            $table->index('is_pinned');
            $table->index('published_at');
            $table->index('view_count');
            $table->index('created_at');
            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'status']);
            $table->index(['primary_postcategory_id', 'status']);
            $table->index('created_user_id');
            $table->index('updated_user_id');
        });

        Schema::create('post_posttag', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('posttag_id');
            $table->timestamps();

            $table->primary(['post_id', 'posttag_id']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('posttag_id')->references('id')->on('posttag')->onDelete('cascade');

            // Indexes
            $table->index('post_id');
            $table->index('posttag_id');
            $table->index('created_at');
        });

        Schema::create('post_postcategory', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('postcategory_id');
            $table->timestamps();

            $table->primary(['post_id', 'postcategory_id']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('postcategory_id')->references('id')->on('postcategory')->onDelete('cascade');

            // Indexes
            $table->index('post_id');
            $table->index('postcategory_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_postcategory');
        Schema::dropIfExists('post_posttag');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('posttag');
        Schema::dropIfExists('postcategory');
    }
};
