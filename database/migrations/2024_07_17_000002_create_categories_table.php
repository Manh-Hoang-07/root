<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('description')->nullable();
            $table->string('image');
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
}; 