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
        Schema::create('config_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('ID người dùng');
            $table->string('config_group')->comment('Nhóm cấu hình được phép');
            $table->boolean('can_read')->default(false)->comment('Quyền đọc');
            $table->boolean('can_write')->default(false)->comment('Quyền ghi');
            $table->boolean('can_delete')->default(false)->comment('Quyền xóa');
            $table->json('allowed_keys')->nullable()->comment('Danh sách key được phép (null = tất cả)');
            $table->json('restricted_keys')->nullable()->comment('Danh sách key bị hạn chế');
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();
            
            $table->unique(['user_id', 'config_group']);
            $table->index(['config_group', 'can_read']);
            $table->index(['config_group', 'can_write']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_permissions');
    }
};
