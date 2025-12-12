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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('listing_id')->nullable()->constrained('listings')->onDelete('set null');
            $table->foreignId('package_id')->constrained('packages')->onDelete('restrict');
            
            // Payment info
            $table->string('transaction_id')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('VND');
            $table->enum('payment_method', ['bank_transfer', 'momo', 'vnpay', 'zalopay', 'cash'])->default('bank_transfer');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            
            // Payment details
            $table->text('payment_info')->nullable(); // JSON: bank account, transaction code, etc.
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('listing_id');
            $table->index('package_id');
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

