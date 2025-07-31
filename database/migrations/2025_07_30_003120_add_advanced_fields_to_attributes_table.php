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
        Schema::table('attributes', function (Blueprint $table) {
            $table->boolean('is_required')->default(false)->after('description');
            $table->boolean('is_unique')->default(false)->after('is_required');
            $table->boolean('is_filterable')->default(true)->after('is_unique');
            $table->boolean('is_searchable')->default(true)->after('is_filterable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn(['is_required', 'is_unique', 'is_filterable', 'is_searchable']);
        });
    }
};
