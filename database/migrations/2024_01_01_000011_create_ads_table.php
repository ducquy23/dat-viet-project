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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_text')->nullable();
            
            // Position: top, sidebar_left, sidebar_right, bottom
            $table->enum('position', ['top', 'sidebar_left', 'sidebar_right', 'bottom'])->default('top');
            
            // Display settings
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            
            // Stats
            $table->integer('views_count')->default(0);
            $table->integer('clicks_count')->default(0);
            
            // Pricing (if needed)
            $table->decimal('price', 12, 2)->nullable();
            $table->enum('pricing_type', ['cpm', 'cpc', 'fixed'])->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('position');
            $table->index('is_active');
            $table->index('sort_order');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};

