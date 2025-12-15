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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('provider')->default('payos')->after('payment_method');
            $table->string('provider_ref')->nullable()->after('provider');
            $table->text('qr_url')->nullable()->after('provider_ref');
            $table->text('checkout_url')->nullable()->after('qr_url');
            $table->timestamp('expired_at')->nullable()->after('checkout_url');
            $table->text('meta')->nullable()->after('notes');
            $table->index('expired_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['expired_at']);
            $table->dropColumn(['provider', 'provider_ref', 'qr_url', 'checkout_url', 'expired_at', 'meta']);
        });
    }
};

