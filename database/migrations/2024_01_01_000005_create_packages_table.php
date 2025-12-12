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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Thường, VIP
            $table->string('code')->unique(); // normal, vip
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0); // 0 = miễn phí
            $table->integer('duration_days')->default(30); // Số ngày hiển thị
            $table->integer('priority')->default(0); // Độ ưu tiên hiển thị
            $table->json('features')->nullable(); // ['pin_color' => 'yellow', 'show_in_carousel' => true]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};

