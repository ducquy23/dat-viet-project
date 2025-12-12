@extends('layouts.app')

@section('title', $keyword ? "Tìm kiếm: {$keyword}" : 'Tìm kiếm')
@section('description', 'Tìm kiếm tin đăng bất động sản')

@section('content')
<div class="container-fluid px-0">
    <!-- Search Header -->
    <div class="bg-white border-bottom py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-8">
                    <h2 class="fw-bold mb-2">
                        @if($keyword)
                            <i class="bi bi-search text-primary"></i> Kết quả tìm kiếm: <span class="text-primary">"{{ $keyword }}"</span>
                        @else
                            <i class="bi bi-search text-primary"></i> Tìm kiếm
                        @endif
                    </h2>
                    @if($listings->total() > 0)
                        <p class="text-muted mb-0">
                            Tìm thấy <strong>{{ number_format($listings->total()) }}</strong> kết quả
                            @if($listings->currentPage() > 1)
                                (Trang {{ $listings->currentPage() }})
                            @endif
                        </p>
                    @endif
                </div>
                <div class="col-12 col-md-4 mt-3 mt-md-0">
                    <!-- Sort -->
                    <form method="GET" action="{{ route('search') }}" id="sort-form">
                        <input type="hidden" name="q" value="{{ $keyword }}">
                        @foreach($filters as $key => $value)
                            @if($value && $key !== 'sort')
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <select name="sort" class="form-select" onchange="document.getElementById('sort-form').submit();">
                            <option value="latest" {{ $filters['sort'] == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ $filters['sort'] == 'price_asc' ? 'selected' : '' }}>Giá: Thấp → Cao</option>
                            <option value="price_desc" {{ $filters['sort'] == 'price_desc' ? 'selected' : '' }}>Giá: Cao → Thấp</option>
                            <option value="area_asc" {{ $filters['sort'] == 'area_asc' ? 'selected' : '' }}>Diện tích: Nhỏ → Lớn</option>
                            <option value="area_desc" {{ $filters['sort'] == 'area_desc' ? 'selected' : '' }}>Diện tích: Lớn → Nhỏ</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-4">
        <div class="row">
            <!-- LEFT FILTER SIDEBAR -->
            <div class="col-12 col-md-3 col-lg-3 mb-4 mb-md-0">
                <div class="filter-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-funnel-fill text-primary"></i>
                            <span>Bộ lọc</span>
                        </h5>
                    </div>

                    <form action="{{ route('search') }}" method="GET" id="search-filter-form">
                        <input type="hidden" name="q" value="{{ $keyword }}">
                        
                        <!-- Loại đất -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Loại đất</label>
                            <select class="form-select shadow-none" name="category_id" id="search-filter-type">
                                <option value="">Tất cả</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ $filters['category_id'] == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tỉnh/Thành phố -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Tỉnh/Thành phố</label>
                            <select class="form-select shadow-none" name="city_id" id="search-filter-city">
                                <option value="">Tất cả</option>
                                @foreach($cities ?? [] as $city)
                                    <option value="{{ $city->id }}" {{ $filters['city_id'] == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quận/Huyện -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted">Quận/Huyện</label>
                            <select class="form-select shadow-none" name="district_id" id="search-filter-district">
                                <option value="">Tất cả</option>
                                @foreach($districts ?? [] as $district)
                                    <option value="{{ $district->id }}" {{ $filters['district_id'] == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Khoảng giá -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted d-flex align-items-center justify-content-between mb-2">
                                <span>Giá tối đa</span>
                                <span class="badge bg-primary-subtle text-primary" id="search-price-label">{{ number_format($filters['max_price'] ?? 5000) }} triệu</span>
                            </label>
                            <input 
                                type="range" 
                                class="form-range" 
                                min="300" 
                                max="5000" 
                                step="50" 
                                id="search-filter-price" 
                                name="max_price"
                                value="{{ $filters['max_price'] ?? 5000 }}">
                            <div class="d-flex justify-content-between small text-muted mt-1">
                                <span>300 triệu</span>
                                <span>5000 triệu</span>
                            </div>
                        </div>

                        <!-- Diện tích -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-uppercase text-muted d-flex align-items-center justify-content-between mb-2">
                                <span>Diện tích tối đa</span>
                                <span class="badge bg-primary-subtle text-primary" id="search-area-label">{{ number_format($filters['max_area'] ?? 1000) }} m²</span>
                            </label>
                            <input 
                                type="range" 
                                class="form-range" 
                                min="50" 
                                max="1000" 
                                step="10" 
                                id="search-filter-area" 
                                name="max_area"
                                value="{{ $filters['max_area'] ?? 1000 }}">
                            <div class="d-flex justify-content-between small text-muted mt-1">
                                <span>50 m²</span>
                                <span>1000 m²</span>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Áp dụng bộ lọc
                            </button>
                            <a href="{{ route('search', ['q' => $keyword]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Xóa bộ lọc
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RESULTS -->
            <div class="col-12 col-md-9 col-lg-9">
                @if($listings->count() > 0)
                    <div class="search-results">
                        @foreach($listings as $listing)
                            <div class="listing-card mb-4" onclick="window.location.href='{{ route('listings.show', $listing->slug) }}'">
                                <div class="row g-0">
                                    <div class="col-12 col-md-4">
                                        <div class="listing-image-wrapper">
                                            <img src="{{ $listing->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}"
                                                 alt="{{ $listing->title }}"
                                                 class="listing-image">
                                            @if($listing->isVip())
                                                <span class="badge vip-badge">
                                                    <i class="bi bi-star-fill"></i> VIP
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <div class="listing-content p-3">
                                            <h5 class="listing-title fw-bold mb-2">
                                                <a href="{{ route('listings.show', $listing->slug) }}" class="text-dark text-decoration-none">
                                                    {{ $listing->title }}
                                                </a>
                                            </h5>
                                            <div class="listing-price mb-2">
                                                <span class="text-primary fw-bold fs-5">{{ number_format($listing->price / 1000000) }} triệu</span>
                                                <span class="text-muted"> • </span>
                                                <span class="text-dark fw-semibold">{{ number_format($listing->area, 1) }} m²</span>
                                                @if($listing->price_per_m2)
                                                    <span class="text-muted small">({{ number_format($listing->price_per_m2 / 1000000, 1) }} tr/m²)</span>
                                                @endif
                                            </div>
                                            <div class="listing-location text-muted small mb-2">
                                                <i class="bi bi-geo-alt-fill"></i>
                                                {{ $listing->address }}, {{ $listing->district?->name }}, {{ $listing->city?->name }}
                                            </div>
                                            <div class="listing-info d-flex flex-wrap gap-2 mb-2">
                                                <span class="badge bg-primary-subtle text-primary">{{ $listing->category->name }}</span>
                                                @if($listing->legal_status)
                                                    <span class="badge bg-light text-dark">{{ $listing->legal_status }}</span>
                                                @endif
                                                @if($listing->has_road_access)
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="bi bi-car-front"></i> Đường ô tô
                                                    </span>
                                                @endif
                                            </div>
                                            @if($listing->description)
                                                <p class="listing-description text-muted small mb-0">
                                                    {{ Str::limit(strip_tags($listing->description), 150) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $listings->links() }}
                        </div>
                    </div>
                @else
                    <div class="empty-search-state text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 80px; opacity: 0.3;"></i>
                        <h4 class="mt-3 mb-2">Không tìm thấy kết quả nào</h4>
                        <p class="text-muted mb-4">
                            @if($keyword)
                                Không có tin đăng nào phù hợp với từ khóa "<strong>{{ $keyword }}</strong>"
                            @else
                                Vui lòng nhập từ khóa tìm kiếm
                            @endif
                        </p>
                        <div class="d-flex gap-2 justify-content-center flex-wrap">
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="bi bi-house"></i> Về trang chủ
                            </a>
                            <a href="{{ route('search') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-counterclockwise"></i> Xóa bộ lọc
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.listing-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: var(--dv-radius-lg);
    overflow: hidden;
    transition: var(--dv-transition);
    cursor: pointer;
}

.listing-card:hover {
    box-shadow: var(--dv-shadow-lg);
    transform: translateY(-2px);
    border-color: var(--dv-primary-light);
}

.listing-image-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    min-height: 200px;
    overflow: hidden;
    background: #f1f5f9;
}

.listing-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.listing-card:hover .listing-image {
    transform: scale(1.05);
}

.vip-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 12px;
    box-shadow: 0 2px 8px rgba(251, 191, 36, 0.4);
}

.listing-content {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.listing-title {
    font-size: 18px;
    line-height: 1.4;
    color: #1a202c;
}

.listing-title a:hover {
    color: var(--dv-primary) !important;
}

.listing-price {
    font-size: 16px;
}

.listing-location {
    font-size: 13px;
}

.listing-description {
    font-size: 13px;
    line-height: 1.6;
    margin-top: auto;
}

.empty-search-state {
    padding: 80px 20px;
}

@media (max-width: 768px) {
    .listing-image-wrapper {
        min-height: 180px;
    }
    
    .listing-content {
        padding: 16px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
    // Update range labels
    const searchPriceEl = document.getElementById('search-filter-price');
    const searchAreaEl = document.getElementById('search-filter-area');
    const searchPriceLabel = document.getElementById('search-price-label');
    const searchAreaLabel = document.getElementById('search-area-label');

    if (searchPriceEl && searchPriceLabel) {
        searchPriceEl.addEventListener('input', function() {
            searchPriceLabel.textContent = new Intl.NumberFormat('vi-VN').format(this.value) + ' triệu';
            const progress = ((this.value - this.min) / (this.max - this.min)) * 100;
            this.style.setProperty('--range-progress', progress + '%');
        });
        const priceProgress = ((searchPriceEl.value - searchPriceEl.min) / (searchPriceEl.max - searchPriceEl.min)) * 100;
        searchPriceEl.style.setProperty('--range-progress', priceProgress + '%');
    }

    if (searchAreaEl && searchAreaLabel) {
        searchAreaEl.addEventListener('input', function() {
            searchAreaLabel.textContent = new Intl.NumberFormat('vi-VN').format(this.value) + ' m²';
            const progress = ((this.value - this.min) / (this.max - this.min)) * 100;
            this.style.setProperty('--range-progress', progress + '%');
        });
        const areaProgress = ((searchAreaEl.value - searchAreaEl.min) / (searchAreaEl.max - searchAreaEl.min)) * 100;
        searchAreaEl.style.setProperty('--range-progress', areaProgress + '%');
    }

    // Load districts when city changes
    const searchCityEl = document.getElementById('search-filter-city');
    const searchDistrictEl = document.getElementById('search-filter-district');
    
    if (searchCityEl && searchDistrictEl) {
        searchCityEl.addEventListener('change', async function() {
            const cityId = this.value;
            
            if (cityId) {
                try {
                    searchDistrictEl.disabled = true;
                    searchDistrictEl.innerHTML = '<option value="">Đang tải...</option>';
                    
                    const response = await fetch(`/api/districts?city_id=${cityId}`);
                    const data = await response.json();
                    
                    searchDistrictEl.innerHTML = '<option value="">Tất cả</option>';
                    
                    if (data.districts && Array.isArray(data.districts)) {
                        data.districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.id;
                            option.textContent = district.name;
                            searchDistrictEl.appendChild(option);
                        });
                    }
                    
                    searchDistrictEl.disabled = false;
                } catch (error) {
                    console.error('Error loading districts:', error);
                    searchDistrictEl.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                    searchDistrictEl.disabled = false;
                }
            } else {
                searchDistrictEl.innerHTML = '<option value="">Tất cả</option>';
                searchDistrictEl.disabled = false;
            }
        });
    }
</script>
@endpush
