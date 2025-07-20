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
        Schema::create('shipping_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->unsignedBigInteger('provider_id');
            $table->decimal('base_price', 12, 2)->nullable();
            $table->decimal('weight_fee', 12, 2)->nullable();
            $table->integer('estimated_days')->nullable();
            $table->string('status')->default('active');
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('provider_id')->references('id')->on('shipping_api_configs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_services');
    }
};
