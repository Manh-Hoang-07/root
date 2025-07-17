<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->enum('status', ['pending', 'confirmed', 'shipping', 'delivered', 'cancelled', 'completed']);
            $table->timestamp('changed_at');
            $table->string('changed_by');
            $table->text('note')->nullable();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
    }
}; 