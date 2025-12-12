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
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn(['views_count', 'clicks_count', 'price', 'pricing_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->integer('views_count')->default(0)->after('end_date');
            $table->integer('clicks_count')->default(0)->after('views_count');
            $table->decimal('price', 12, 2)->nullable()->after('clicks_count');
            $table->enum('pricing_type', ['cpm', 'cpc', 'fixed'])->nullable()->after('price');
        });
    }
};
