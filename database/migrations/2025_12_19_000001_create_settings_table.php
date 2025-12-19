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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Đất Việt Map');
            $table->string('site_slogan')->nullable();
            $table->string('hotline')->nullable();
            $table->string('zalo')->nullable();
            $table->string('support_email')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('og_image_url')->nullable();
            $table->unsignedInteger('vip_limit')->default(10);
            $table->string('vip_sort')->default('latest'); // latest | most_view
            $table->text('support_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

