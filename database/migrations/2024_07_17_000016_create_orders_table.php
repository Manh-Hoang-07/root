<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['pending', 'confirmed', 'shipping', 'delivered', 'cancelled', 'completed']);
            $table->decimal('total_price', 15, 2);
            $table->decimal('shipping_fee', 15, 2);
            $table->decimal('shipping_discount', 15, 2)->nullable();
            $table->decimal('promotion_discount', 15, 2);
            $table->decimal('final_price', 15, 2);
            $table->unsignedBigInteger('shipping_address_id');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shipping_address_id')->references('id')->on('addresses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}; 