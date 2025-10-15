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
            $table->string('key')->unique()->comment('Khóa cấu hình');
            $table->text('value')->nullable()->comment('Giá trị cấu hình');
            $table->enum('type', ['string', 'integer', 'boolean', 'json', 'array', 'float'])->default('string')->comment('Loại dữ liệu');
            $table->string('group')->index()->comment('Nhóm cấu hình');
            $table->text('description')->nullable()->comment('Mô tả cấu hình');
            $table->boolean('is_public')->default(false)->comment('Có thể truy cập public không');
            $table->boolean('is_encrypted')->default(false)->comment('Có mã hóa không');
            $table->json('validation_rules')->nullable()->comment('Rules validation');
            $table->text('default_value')->nullable()->comment('Giá trị mặc định');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Trạng thái hoạt động');
            $table->integer('sort_order')->default(0)->comment('Thứ tự sắp xếp');
            $table->timestamps();
            $table->unsignedBigInteger('created_user_id')->nullable();
            $table->unsignedBigInteger('updated_user_id')->nullable();

            $table->index(['group', 'status']);
            $table->index(['is_public', 'status']);
            $table->index('key');
            $table->index('type');
            $table->index('sort_order');
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
        Schema::dropIfExists('system_configs');
    }
};
