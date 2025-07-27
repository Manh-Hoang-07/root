<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Thay đổi từ text thành longtext để hỗ trợ nội dung dài hơn từ CKEditor Ultimate
            $table->longText('description')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Rollback về text nếu cần
            $table->text('description')->nullable()->change();
        });
    }
};
