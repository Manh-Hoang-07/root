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
        Schema::create('config_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('config_key')->comment('Khóa cấu hình');
            $table->text('old_value')->nullable()->comment('Giá trị cũ');
            $table->text('new_value')->nullable()->comment('Giá trị mới');
            $table->string('action')->comment('Hành động: created, updated, deleted');
            $table->unsignedBigInteger('changed_by')->nullable()->comment('Người thay đổi');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
            $table->text('change_reason')->nullable()->comment('Lý do thay đổi');
            $table->string('ip_address')->nullable()->comment('Địa chỉ IP');
            $table->string('user_agent')->nullable()->comment('User Agent');
            $table->json('metadata')->nullable()->comment('Dữ liệu bổ sung');
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['config_key', 'created_at']);
            $table->index(['changed_by', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('config_audit_logs');
    }
};
