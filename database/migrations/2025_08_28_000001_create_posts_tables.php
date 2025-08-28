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
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('postcategory')->nullOnDelete();
        });

        Schema::create('posttag', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('image')->nullable();
            $table->string('cover_image')->nullable();
            $table->unsignedBigInteger('primary_postcategory_id')->nullable()->index();
            $table->enum('status', ['draft','scheduled','published','archived'])->default('draft')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_pinned')->default(false)->index();
            $table->dateTime('published_at')->nullable()->index();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('primary_postcategory_id')->references('id')->on('postcategory')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('post_posttag', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('posttag_id');
            $table->timestamps();

            $table->primary(['post_id', 'posttag_id']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('posttag_id')->references('id')->on('posttag')->onDelete('cascade');
        });

        Schema::create('post_postcategory', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('postcategory_id');
            $table->timestamps();

            $table->primary(['post_id', 'postcategory_id']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('postcategory_id')->references('id')->on('postcategory')->onDelete('cascade');
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
