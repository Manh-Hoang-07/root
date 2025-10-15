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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['email', 'sms', 'otp'])->comment('Loại template');
            $table->string('code', 100)->unique()->comment('Mã template (VD: OTP_VERIFY, WELCOME_EMAIL)');
            $table->string('name', 150)->comment('Tên hiển thị template');
            $table->string('subject', 255)->nullable()->comment('Tiêu đề (dùng cho email)');
            $table->text('content')->comment('Nội dung template (có thể chứa {{otp}}, {{name}})');
            $table->json('variables')->nullable()->comment('Danh sách biến dùng trong template');
            $table->string('locale', 10)->default('vi')->comment('Ngôn ngữ');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Trạng thái hoạt động');
            $table->unsignedBigInteger('created_user_id')->nullable()->comment('Người tạo');
            $table->unsignedBigInteger('updated_user_id')->nullable()->comment('Người cập nhật');
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['code', 'locale']);
            $table->index('status');
            $table->index('type');
            $table->index('code');
            $table->index('locale');
            $table->index('name');
            $table->index('created_at');
            $table->index('created_user_id');
            $table->index('updated_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};