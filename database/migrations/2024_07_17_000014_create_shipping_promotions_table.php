<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_promotions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->decimal('min_order_value', 15, 2);
            $table->enum('discount_type', ['percent', 'fixed', 'free']);
            $table->decimal('discount_value', 15, 2);
            $table->datetime('start_at');
            $table->datetime('end_at');
            $table->enum('status', ['active', 'inactive']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_promotions');
    }
}; 