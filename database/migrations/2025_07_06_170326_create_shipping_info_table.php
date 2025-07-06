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
        Schema::create('shipping_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('tracking_number')->nullable();
            $table->string('shipping_company')->nullable();
            $table->string('shipping_method')->nullable();
            $table->date('estimated_delivery')->nullable();
            $table->date('actual_delivery')->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_info');
    }
};
