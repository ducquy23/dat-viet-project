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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->foreignId('city_id')->constrained('cities')->onDelete('restrict');
            $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('restrict');
            $table->foreignId('package_id')->default(1)->constrained('packages')->onDelete('restrict');
            
            // Basic info
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            
            // Price & Area
            $table->decimal('price', 15, 2); // Giá tổng (triệu đồng)
            $table->decimal('price_per_m2', 12, 2)->nullable(); // Đơn giá /m²
            $table->decimal('area', 10, 2); // Diện tích m²
            $table->decimal('front_width', 8, 2)->nullable(); // Mặt tiền (m)
            $table->decimal('depth', 8, 2)->nullable(); // Chiều sâu (m)
            
            // Legal & Road
            $table->string('legal_status')->nullable(); // Sổ đỏ, Sổ hồng
            $table->string('road_type')->nullable(); // Ô tô, hẻm
            $table->decimal('road_width', 8, 2)->nullable(); // Độ rộng đường (m)
            $table->string('direction')->nullable(); // Hướng: Đông, Tây, Nam, Bắc
            $table->boolean('has_road_access')->default(false);
            
            // Planning
            $table->text('planning_info')->nullable(); // Quy hoạch
            $table->boolean('deposit_online')->default(false); // Có đặt cọc online
            
            // Tags
            $table->json('tags')->nullable(); // ['Sổ đỏ', 'Mặt tiền', 'Gần chợ']
            
            // Polygon coordinates for map
            $table->json('polygon_coordinates')->nullable();
            
            // Contact
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('contact_zalo')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'expired', 'sold'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Views & Interactions
            $table->integer('views_count')->default(0);
            $table->integer('favorites_count')->default(0);
            $table->integer('contacts_count')->default(0);
            
            // SEO
            $table->string('slug')->unique();
            $table->text('meta_description')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('category_id');
            $table->index('city_id');
            $table->index('district_id');
            $table->index('package_id');
            $table->index('status');
            $table->index('price');
            $table->index('area');
            $table->index('created_at');
            $table->index(['latitude', 'longitude']);
            $table->fullText(['title', 'description', 'address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};

