<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tạo bảng sessions để lưu trữ session data khi sử dụng database driver
     */
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            // Session ID - primary key
            $table->string('id')->primary();

            // User ID nếu user đã đăng nhập
            $table->foreignId('user_id')->nullable()->index();

            // Thông tin IP và User Agent
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Dữ liệu session được serialize
            $table->longText('payload');

            // Timestamp của lần hoạt động cuối cùng
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
