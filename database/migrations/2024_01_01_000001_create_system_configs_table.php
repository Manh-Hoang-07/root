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
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->string('config_key', 255)->unique()->comment('Khóa cấu hình');
            $table->text('config_value')->nullable()->comment('Giá trị cấu hình');
            $table->enum('config_type', ['string', 'number', 'boolean', 'json', 'file', 'email', 'url'])->default('string')->comment('Kiểu dữ liệu');
            $table->string('config_group', 100)->comment('Nhóm cấu hình');
            $table->string('display_name', 255)->comment('Tên hiển thị');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->boolean('is_public')->default(false)->comment('Có public không');
            $table->boolean('is_required')->default(false)->comment('Bắt buộc');
            $table->json('validation_rules')->nullable()->comment('Quy tắc validation');
            $table->text('default_value')->nullable()->comment('Giá trị mặc định');
            $table->integer('sort_order')->default(0)->comment('Thứ tự sắp xếp');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Trạng thái');
            $table->timestamps();

            // Indexes
            $table->index('config_group');
            $table->index('config_key');
            $table->index('status');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_configs');
    }
};
