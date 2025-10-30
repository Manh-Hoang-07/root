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
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Tiêu đề hoặc tên slider');
            $table->string('image_path')->comment('Đường dẫn ảnh slider');
            $table->string('link')->nullable()->comment('Đường dẫn khi click vào slider');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->dateTime('start_time')->nullable()->comment('Thời gian bắt đầu hiển thị');
            $table->dateTime('end_time')->nullable()->comment('Thời gian kết thúc hiển thị');
            $table->integer('sort_order')->default(0)->comment('Thứ tự hiển thị');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
