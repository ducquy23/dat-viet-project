@extends('layouts.app')

@section('title', $listing->title ?? 'Chi tiết tin đăng')
@section('description', $listing->meta_description ?? Str::limit($listing->description ?? '', 160))

{{-- SEO cho Google + Open Graph cho share Zalo/Facebook --}}
@section('canonical', route('listings.show', $listing->slug))
@section('og_type', 'article')
@section('og_title', $listing->title ?? 'Chi tiết tin đăng')
@section('og_description', $listing->meta_description ?? Str::limit($listing->description ?? '', 160))
@section('og_url', route('listings.show', $listing->slug))
@section('og_image', $listing->images->first()->image_url ?? asset('images/default-og.png'))

@section('content')
@if(isset($listing) && $listing)
<div class="container-fluid px-0 detail-shell">
    <!-- Breadcrumb -->
    <div class="bg-light border-bottom py-2">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('categories.show', $listing->category->slug ?? '#') }}" class="text-decoration-none">{{ $listing->category->name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($listing->title, 50) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <!-- Nội dung chính -->
            <div class="col-lg-8">
                <!-- Gallery ảnh -->
                @if($listing->images && $listing->images->count() > 0)
                <div class="listing-gallery mb-4">
                    <div id="mainGallery" class="main-gallery mb-3">
                        <img id="mainImage" 
                             src="{{ $listing->images->first()->image_url }}" 
                             alt="{{ $listing->title }}"
                             class="main-image">
                        @if($listing->isVip())
                            <span class="badge vip-badge">
                                <i class="bi bi-star-fill"></i> VIP
                            </span>
                        @endif
                        <button class="gallery-prev" onclick="changeImage(-1)">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="gallery-next" onclick="changeImage(1)">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                    
                    @if($listing->images->count() > 1)
                    <div class="gallery-thumbnails">
                        @foreach($listing->images as $index => $image)
                        <div class="thumbnail-item {{ $index === 0 ? 'active' : '' }}" onclick="showImage({{ $index }})">
                            <img src="{{ $image->image_url }}" alt="Thumbnail {{ $index + 1 }}">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif

                <!-- Tiêu đề và giá -->
                <div class="listing-header mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            <h1 class="listing-title mb-3">{{ $listing->title }}</h1>
                            <div class="listing-price-section mb-3">
                                <div class="d-flex align-items-baseline gap-3 flex-wrap">
                                    <span class="price-main">{{ formatPrice($listing->price) }}@if($listing->price && $listing->price > 0) đồng@endif</span>
                                    @if($pricePerM2Formatted = formatPricePerM2($listing->price_per_m2, $listing->price, $listing->area))
                                        <span class="price-per-m2">({{ str_replace(' tr/m²', ' triệu/m²', $pricePerM2Formatted) }})</span>
                                    @endif
                                </div>
                                <div class="price-breakdown mt-2">
                                    <span class="badge bg-primary-subtle text-primary me-2">{{ number_format($listing->area, 1) }} m²</span>
                                    @if($pricePerM2Formatted = formatPricePerM2($listing->price_per_m2, $listing->price, $listing->area))
                                        <span class="text-muted small">Đơn giá: {{ str_replace(' tr/m²', ' triệu/m²', $pricePerM2Formatted) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="listing-location mb-3">
                                <i class="bi bi-geo-alt-fill text-primary"></i>
                                <span>{{ $listing->address }}, {{ $listing->district?->name }}, {{ $listing->city?->name }}</span>
                            </div>
                            <div class="listing-meta d-flex flex-wrap gap-3 align-items-center">
                                <div class="meta-item">
                                    <i class="bi bi-eye"></i>
                                    <span>{{ number_format($listing->views_count ?? 0) }} lượt xem</span>
                                </div>
                                <div class="meta-item">
                                    <i class="bi bi-calendar"></i>
                                    <span>Đăng {{ $listing->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="badge bg-secondary">{{ $listing->category->name }}</span>
                                </div>
                            </div>
                        </div>
                        @auth('partner')
                        <button class="btn btn-fav-detail {{ isset($isFavorited) && $isFavorited ? 'active' : '' }}"
                                id="favorite-btn"
                                onclick="toggleFavorite({{ $listing->id }})"
                                title="Yêu thích">
                            <i class="bi bi-heart{{ isset($isFavorited) && $isFavorited ? '-fill' : '' }}"></i>
                        </button>
                        @endauth
                    </div>

                    <!-- Tags -->
                    @if($listing->tags && is_array($listing->tags) && count($listing->tags) > 0)
                    <div class="listing-tags">
                        @foreach($listing->tags as $tag)
                        <span class="badge bg-light text-dark border">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Thông tin chi tiết -->
                <div class="info-card mb-4">
                    <div class="info-card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-info-circle-fill text-primary"></i>
                            Thông tin chi tiết
                        </h5>
                    </div>
                    <div class="info-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-rulers"></i>
                                        <span>Diện tích</span>
                                    </div>
                                    <div class="info-value">{{ number_format($listing->area, 1) }} m²</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-arrows-expand"></i>
                                        <span>Mặt tiền</span>
                                    </div>
                                    <div class="info-value">{{ $listing->front_width ? number_format($listing->front_width, 1) . ' m' : 'Chưa cập nhật' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-arrows-angle-contract"></i>
                                        <span>Chiều sâu</span>
                                    </div>
                                    <div class="info-value">{{ $listing->depth ? number_format($listing->depth, 1) . ' m' : 'Chưa cập nhật' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-compass"></i>
                                        <span>Hướng</span>
                                    </div>
                                    <div class="info-value">{{ $listing->direction ?? 'Chưa cập nhật' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-file-earmark-check"></i>
                                        <span>Tình trạng pháp lý</span>
                                    </div>
                                    <div class="info-value">{{ $listing->legal_status ?? 'Chưa cập nhật' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-road"></i>
                                        <span>Loại đường</span>
                                    </div>
                                    <div class="info-value">{{ $listing->road_type ?? 'Chưa cập nhật' }}</div>
                                </div>
                            </div>
                            @if($listing->road_width)
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-arrows-angle-expand"></i>
                                        <span>Độ rộng đường</span>
                                    </div>
                                    <div class="info-value">{{ number_format($listing->road_width, 1) }} m</div>
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-car-front"></i>
                                        <span>Đường ô tô vào</span>
                                    </div>
                                    <div class="info-value">
                                        @if($listing->has_road_access)
                                            <span class="badge bg-success-subtle text-success">
                                                <i class="bi bi-check-circle-fill"></i> Có
                                            </span>
                                        @else
                                            <span class="text-muted">Không</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-tag"></i>
                                        <span>Danh mục</span>
                                    </div>
                                    <div class="info-value">
                                        <span class="badge bg-primary-subtle text-primary">{{ $listing->category->name }}</span>
                                    </div>
                                </div>
                            </div>
                            @if($pricePerM2Formatted = formatPricePerM2($listing->price_per_m2, $listing->price, $listing->area))
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">
                                        <i class="bi bi-calculator"></i>
                                        <span>Đơn giá /m²</span>
                                    </div>
                                    <div class="info-value">
                                        {{ str_replace(' tr/m²', ' triệu/m²', $pricePerM2Formatted) }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Mô tả -->
                @if($listing->description)
                <div class="info-card mb-4">
                    <div class="info-card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-card-text-fill text-primary"></i>
                            Mô tả chi tiết
                        </h5>
                    </div>
                    <div class="info-card-body">
                        <div class="listing-description">{{ nl2br(e($listing->description)) }}</div>
                    </div>
                </div>
                @endif

                <!-- Thông tin quy hoạch -->
                @if($listing->planning_info)
                <div class="info-card mb-4">
                    <div class="info-card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-map-fill text-primary"></i>
                            Thông tin quy hoạch
                        </h5>
                    </div>
                    <div class="info-card-body">
                        <div class="listing-description">{{ nl2br(e($listing->planning_info)) }}</div>
                    </div>
                </div>
                @endif

                <!-- Bản đồ -->
                <div class="info-card mb-4">
                    <div class="info-card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-geo-alt-fill text-primary"></i>
                            Vị trí trên bản đồ
                        </h5>
                    </div>
                    <div class="info-card-body p-0">
                        <div id="detailMap" class="detail-map"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Thông tin liên hệ -->
                <div class="contact-card mb-4">
                    <div class="contact-card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-person-circle-fill"></i>
                            Thông tin liên hệ
                        </h5>
                    </div>
                    <div class="contact-card-body">
                        <div class="contact-info mb-4">
                            <div class="contact-name mb-2">
                                <i class="bi bi-person"></i>
                                <strong>{{ $listing->contact_name }}</strong>
                            </div>
                            <div class="contact-phone mb-2">
                                <i class="bi bi-telephone-fill"></i>
                                <a href="tel:{{ $listing->contact_phone }}" class="text-decoration-none">{{ $listing->contact_phone }}</a>
                            </div>
                            @if($listing->contact_zalo)
                            <div class="contact-zalo">
                                <i class="bi bi-chat-dots-fill"></i>
                                <span>Zalo: {{ $listing->contact_zalo }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <a href="tel:{{ $listing->contact_phone }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-telephone-fill"></i> Gọi điện ngay
                            </a>
                            @if($listing->contact_zalo)
                            <a href="https://zalo.me/{{ $listing->contact_zalo }}" target="_blank" class="btn btn-outline-primary btn-lg">
                                <i class="bi bi-chat-dots-fill"></i> Chat Zalo
                            </a>
                            @endif
                            <button class="btn btn-outline-secondary btn-lg" onclick="showContactForm({{ $listing->id }})">
                                <i class="bi bi-envelope"></i> Gửi tin nhắn
                            </button>
                        </div>

                        @if($listing->deposit_online)
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-info-circle-fill"></i>
                            <strong>Có thể đặt cọc online</strong>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Tin liên quan -->
                @if(isset($relatedListings) && $relatedListings->count() > 0)
                <div class="related-listings-card">
                    <div class="card-header-custom">
                        <h5 class="mb-0">
                            <i class="bi bi-grid-3x3-gap-fill text-primary"></i>
                            Tin đăng liên quan
                        </h5>
                    </div>
                    <div class="related-listings-body">
                        @foreach($relatedListings as $related)
                        <a href="{{ route('listings.show', $related->slug) }}" class="related-item">
                            <div class="related-image">
                                <img src="{{ $related->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}"
                                     alt="{{ $related->title }}">
                                @if($related->isVip())
                                    <span class="badge vip-badge-small">VIP</span>
                                @endif
                            </div>
                            <div class="related-content">
                                <h6 class="related-title">{{ Str::limit($related->title, 60) }}</h6>
                                <div class="related-price">
                                    {{ formatPrice($related->price) }}
                                </div>
                                <div class="related-meta">
                                    <span>{{ number_format($related->area, 1) }} m²</span>
                                    <span>•</span>
                                    <span>{{ $related->category->name }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<div class="container py-5">
    <div class="empty-state text-center">
        <i class="bi bi-exclamation-triangle text-muted" style="font-size: 80px; opacity: 0.3;"></i>
        <h3 class="mt-3 mb-2">Không tìm thấy tin đăng</h3>
        <p class="text-muted mb-4">Tin đăng này không tồn tại hoặc đã bị xóa.</p>
        <a href="{{ route('home') }}" class="btn btn-primary">
            <i class="bi bi-house"></i> Về trang chủ
        </a>
    </div>
</div>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<style>
/* Gallery */
.detail-shell {
    min-height: 100vh;
    background: radial-gradient(circle at 20% 20%, rgba(219, 234, 254, 0.6), transparent 35%),
                radial-gradient(circle at 80% 0%, rgba(240, 249, 255, 0.5), transparent 30%),
                linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
    padding-bottom: 48px;
}

.detail-shell .container {
    max-width: 1200px;
}

.listing-gallery {
    border-radius: var(--dv-radius-lg);
    overflow: hidden;
    background: white;
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.main-gallery {
    position: relative;
    width: 100%;
    height: 500px;
    border-radius: var(--dv-radius-lg);
    overflow: hidden;
    background: linear-gradient(135deg, #e0f2fe 0%, #f8fafc 100%);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.4);
}

.main-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: zoom-in;
}

.gallery-prev,
.gallery-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.9);
    border: none;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--dv-transition);
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.gallery-prev {
    left: 16px;
}

.gallery-next {
    right: 16px;
}

.gallery-prev:hover,
.gallery-next:hover {
    background: white;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.gallery-thumbnails {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding: 12px 8px;
    background: linear-gradient(180deg, rgba(248, 250, 252, 0.9), rgba(255, 255, 255, 0.9));
    border-top: 1px solid rgba(226, 232, 240, 0.8);
}

.thumbnail-item {
    flex-shrink: 0;
    width: 100px;
    height: 100px;
    border-radius: var(--dv-radius-sm);
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: var(--dv-transition);
    opacity: 0.75;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
}

.thumbnail-item:hover,
.thumbnail-item.active {
    opacity: 1;
    border-color: var(--dv-primary);
    transform: scale(1.05);
}

.thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.vip-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
    z-index: 10;
}

/* Listing Header */
.listing-header {
    background: rgba(255, 255, 255, 0.9);
    padding: 24px;
    border-radius: 28px;
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.1);
    border: 1px solid rgba(226, 232, 240, 0.8);
    backdrop-filter: blur(8px);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.listing-header:hover {
    transform: translateY(-2px);
    box-shadow: 0 22px 60px rgba(15, 23, 42, 0.14);
}

.listing-title {
    font-size: 28px;
    font-weight: 800;
    line-height: 1.3;
    color: #1a202c;
    margin-bottom: 16px;
}

.price-main {
    font-size: 32px;
    font-weight: 800;
    color: var(--dv-primary);
    line-height: 1;
}

.price-per-m2 {
    font-size: 18px;
    color: #64748b;
    font-weight: 600;
}

.listing-location {
    font-size: 16px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.listing-meta {
    font-size: 14px;
    color: #94a3b8;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-fav-detail {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: #64748b;
    transition: var(--dv-transition);
}

.btn-fav-detail:hover {
    border-color: #e03131;
    background: #fff5f5;
    color: #e03131;
    transform: scale(1.1);
}

.btn-fav-detail.active {
    border-color: #e03131;
    background: #fff5f5;
    color: #e03131;
}

/* Info Cards */
.info-card {
    background: rgba(255, 255, 255, 0.92);
    border-radius: 24px;
    box-shadow: 0 16px 50px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.9);
    backdrop-filter: blur(10px);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
}

.info-card-header {
    background: linear-gradient(135deg, #f8f9fb 0%, #ffffff 100%);
    padding: 20px 24px;
    border-bottom: 2px solid #e2e8f0;
}

.info-card-header h5 {
    font-size: 18px;
    font-weight: 700;
    color: #1a202c;
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-card-body {
    padding: 24px;
}

.info-item {
    padding: 16px;
    background: #f8f9fb;
    border-radius: var(--dv-radius-sm);
    transition: var(--dv-transition);
}

.info-item:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}

.info-label {
    font-size: 13px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-value {
    font-size: 16px;
    font-weight: 700;
    color: #1a202c;
}

.listing-description {
    font-size: 15px;
    line-height: 1.8;
    color: #475569;
    white-space: pre-line;
}

/* Contact Card */
.contact-card {
    background: rgba(255, 255, 255, 0.94);
    border-radius: 24px;
    box-shadow: 0 18px 60px rgba(15, 23, 42, 0.12);
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.9);
    backdrop-filter: blur(12px);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.contact-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 22px 70px rgba(15, 23, 42, 0.16);
}

@media (min-width: 992px) {
    .contact-card {
        position: sticky;
        top: 90px;
        z-index: 5;
        max-height: calc(100vh - 110px);
        overflow-y: auto;
    }
    
    .contact-card::-webkit-scrollbar {
        width: 6px;
    }
    
    .contact-card::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .contact-card::-webkit-scrollbar-thumb {
        background: rgba(51, 87, 147, 0.3);
        border-radius: 10px;
    }
    
    .contact-card::-webkit-scrollbar-thumb:hover {
        background: rgba(51, 87, 147, 0.5);
    }
}

.contact-card-header {
    background: linear-gradient(135deg, var(--dv-primary) 0%, var(--dv-primary-dark) 100%);
    color: white;
    padding: 20px 24px;
}

.contact-card-header h5 {
    font-size: 18px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.contact-card-body {
    padding: 24px;
}

.contact-info {
    padding-bottom: 20px;
    border-bottom: 1px solid #e2e8f0;
}

.contact-name,
.contact-phone,
.contact-zalo {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    font-size: 15px;
    color: #475569;
}

.contact-name {
    font-size: 18px;
    color: #1a202c;
}

.contact-phone a {
    color: var(--dv-primary);
    font-weight: 600;
}

.contact-phone a:hover {
    text-decoration: underline;
}

.detail-shell .btn {
    border-radius: 14px;
    font-weight: 700;
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.18);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.detail-shell .btn:active {
    transform: translateY(1px);
}

.detail-shell .btn.btn-outline-secondary {
    border-width: 2px;
}

.detail-shell .btn.btn-outline-primary {
    border-width: 2px;
    box-shadow: 0 10px 30px rgba(37, 99, 235, 0.08);
}

.detail-shell .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 40px rgba(37, 99, 235, 0.22);
}

/* Detail Map */
.detail-map {
    width: 100%;
    height: 400px;
    border-radius: 0 0 var(--dv-radius-lg) var(--dv-radius-lg);
}

/* Related Listings */
.related-listings-card {
    background: rgba(255, 255, 255, 0.92);
    border-radius: 22px;
    box-shadow: 0 14px 40px rgba(15, 23, 42, 0.08);
    overflow: hidden;
    border: 1px solid rgba(226, 232, 240, 0.85);
    backdrop-filter: blur(10px);
}

.card-header-custom {
    background: linear-gradient(135deg, #f8f9fb 0%, #ffffff 100%);
    padding: 20px 24px;
    border-bottom: 2px solid #e2e8f0;
}

.card-header-custom h5 {
    font-size: 18px;
    font-weight: 700;
    color: #1a202c;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
}

.related-listings-body {
    padding: 16px;
}

.related-item {
    display: flex;
    gap: 12px;
    padding: 12px;
    border-radius: var(--dv-radius-sm);
    text-decoration: none;
    color: inherit;
    transition: var(--dv-transition);
    margin-bottom: 12px;
    border: 1px solid #e2e8f0;
}

.related-item:hover {
    background: #f8f9fb;
    border-color: var(--dv-primary-light);
    transform: translateX(4px);
    text-decoration: none;
    color: inherit;
}

.related-item:last-child {
    margin-bottom: 0;
}

.related-image {
    position: relative;
    width: 100px;
    height: 100px;
    flex-shrink: 0;
    border-radius: var(--dv-radius-sm);
    overflow: hidden;
    background: #f1f5f9;
}

.related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.vip-badge-small {
    position: absolute;
    top: 6px;
    right: 6px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 700;
}

.related-content {
    flex: 1;
    min-width: 0;
}

.related-title {
    font-size: 14px;
    font-weight: 600;
    color: #1a202c;
    margin-bottom: 6px;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.related-price {
    font-size: 16px;
    font-weight: 700;
    color: var(--dv-primary);
    margin-bottom: 4px;
}

.related-meta {
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Breadcrumb */
.breadcrumb {
    margin: 0;
    background: transparent;
    padding: 0;
}

.breadcrumb-item a {
    color: var(--dv-primary);
}

.breadcrumb-item.active {
    color: #64748b;
}

/* Responsive */
@media (max-width: 768px) {
    .main-gallery {
        height: 300px;
    }
    
    .listing-title {
        font-size: 22px;
    }
    
    .price-main {
        font-size: 24px;
    }
    
    .info-card-body,
    .contact-card-body {
        padding: 16px;
    }
    
    .detail-map {
        height: 300px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  @if(isset($listing) && $listing)
  let currentImageIndex = 0;
  const images = @json($listing->images->pluck('image_url')->toArray());

  function showImage(index) {
    if (index < 0 || index >= images.length) return;
    currentImageIndex = index;
    document.getElementById('mainImage').src = images[index];
    
    // Update active thumbnail
    document.querySelectorAll('.thumbnail-item').forEach((item, i) => {
      item.classList.toggle('active', i === index);
    });
  }

  function changeImage(direction) {
    const newIndex = currentImageIndex + direction;
    if (newIndex < 0) {
      showImage(images.length - 1);
    } else if (newIndex >= images.length) {
      showImage(0);
    } else {
      showImage(newIndex);
    }
  }

  // Initialize detail map
  const detailMap = L.map('detailMap').setView([{{ $listing->latitude }}, {{ $listing->longitude }}], 16);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(detailMap);

  const marker = L.marker([{{ $listing->latitude }}, {{ $listing->longitude }}], {
    icon: L.divIcon({
      className: '',
      html: '<div class="lot-marker"><div class="lot-rect"></div><div class="lot-pin"></div></div>',
      iconSize: [30, 40],
      iconAnchor: [15, 36]
    })
  }).addTo(detailMap);

  marker.bindPopup(`
    <div style="font-weight: 600; margin-bottom: 4px;">{{ $listing->title }}</div>
    <div style="font-size: 12px; color: #64748b;">{{ $listing->address }}</div>
  `).openPopup();

  // Image zoom on click
  document.getElementById('mainImage')?.addEventListener('click', function() {
    Swal.fire({
      imageUrl: this.src,
      imageAlt: '{{ $listing->title }}',
      showCloseButton: true,
      showConfirmButton: false,
      width: '90%',
      padding: 0
    });
  });
  @endif

  // Toggle favorite
  function toggleFavorite(listingId) {
    @auth('partner')
    fetch(`/api/listings/${listingId}/favorite`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(response => response.json())
    .then(data => {
      const btn = document.getElementById('favorite-btn');
      const icon = btn.querySelector('i');
      if (data.favorited) {
        btn.classList.add('active');
        icon.className = 'bi bi-heart-fill';
      } else {
        btn.classList.remove('active');
        icon.className = 'bi bi-heart';
      }
    })
    .catch(error => {
      console.error('Error:', error);
    });
    @endauth
    @if(!Auth::guard('partner')->check())
    Swal.fire({
      icon: 'warning',
      title: 'Yêu cầu đăng nhập',
      text: 'Vui lòng đăng nhập để sử dụng tính năng này',
      confirmButtonText: 'Đã hiểu'
    });
    @endif
  }

  function showContactForm(listingId) {
    Swal.fire({
      title: 'Liên hệ với người đăng',
      html: `
        <input id="swal-name" class="swal2-input" placeholder="Tên của bạn" required>
        <input id="swal-phone" class="swal2-input" type="tel" placeholder="Số điện thoại" required>
        <textarea id="swal-message" class="swal2-textarea" placeholder="Tin nhắn (tùy chọn)"></textarea>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Gửi',
      cancelButtonText: 'Hủy',
      preConfirm: () => {
        const name = document.getElementById('swal-name').value;
        const phone = document.getElementById('swal-phone').value;
        const message = document.getElementById('swal-message').value || '';

        if (!name || !phone) {
          Swal.showValidationMessage('Vui lòng điền đầy đủ thông tin');
          return false;
        }

        return { name, phone, message };
      }
    }).then((result) => {
      if (result.isConfirmed && result.value) {
        fetch(`/api/listings/${listingId}/contact`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(result.value)
        })
        .then(response => response.json())
        .then(data => {
          Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: data.message || 'Gửi liên hệ thành công!',
            confirmButtonText: 'Đồng ý'
          });
        })
        .catch(error => {
          Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Có lỗi xảy ra, vui lòng thử lại',
            confirmButtonText: 'Đã hiểu'
          });
        });
      }
    });
  }
</script>
@endpush
