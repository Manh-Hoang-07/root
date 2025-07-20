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
        Schema::create('shipping_promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('discount', 12, 2);
            $table->string('type');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_promotions');
    }
};
