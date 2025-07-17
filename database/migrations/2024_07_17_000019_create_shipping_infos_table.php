<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->string('shipping_method');
            $table->string('provider');
            $table->string('service_code');
            $table->string('provider_order_code');
            $table->decimal('shipping_fee', 15, 2);
            $table->decimal('shipping_discount', 15, 2)->nullable();
            $table->text('raw_fee_response');
            $table->string('tracking_number');
            $table->string('carrier');
            $table->enum('status', ['pending', 'shipping', 'delivered', 'cancelled']);
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('expected_delivery_time')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_infos');
    }
}; 