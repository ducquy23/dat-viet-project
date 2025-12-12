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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Contact info (for non-registered users)
            $table->string('visitor_name')->nullable();
            $table->string('visitor_phone')->nullable();
            $table->string('visitor_email')->nullable();
            
            // Contact type
            $table->enum('contact_type', ['call', 'zalo', 'message', 'deposit'])->default('call');
            $table->text('message')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'contacted', 'closed'])->default('pending');
            $table->text('notes')->nullable(); // Admin notes
            
            $table->timestamps();
            
            $table->index('listing_id');
            $table->index('user_id');
            $table->index('contact_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};

