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
                            @if($filters['city'])
                                <input type="hidden" name="city" value="{{ $filters['city'] }}">
                            @endif
                            @if($filters['category'])
                                <input type="hidden" name="category" value="{{ $filters['category'] }}">
                            @endif
                            @if($filters['vip'])
                                <input type="hidden" name="vip" value="{{ $filters['vip'] }}">
                            @endif
                            @if($filters['legal_status'])
                                <input type="hidden" name="legal_status" value="{{ $filters['legal_status'] }}">
                            @endif
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
                            <span class="badge bg-primary-subtle text-primary" id="search-filter-count">0 tiêu chí</span>
                        </div>

                        <!-- Active Filter Tags -->
                        <div id="search-active-filters" class="mb-3 d-flex flex-wrap gap-2" style="display: none !important;">
                            <!-- Will be populated by JavaScript -->
                        </div>

                        <!-- Quick Filter Chips -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-uppercase text-muted mb-2">Lọc nhanh</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary quick-filter-chip-search" data-filter="legal_status" data-value="Sổ đỏ">
                                    <i class="bi bi-file-earmark-check"></i> Sổ đỏ
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary quick-filter-chip-search" data-filter="vip" data-value="1">
                                    <i class="bi bi-star-fill"></i> VIP
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('search') }}" method="GET" id="search-filter-form">
                            <input type="hidden" name="q" value="{{ $keyword }}">
                            <input type="hidden" name="vip" id="search-filter-vip" value="{{ request('vip') }}">
                            <input type="hidden" name="legal_status" id="search-filter-legal-status" value="{{ request('legal_status') }}">

                            <!-- Loại đất -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Loại đất</label>
                                <select class="form-select shadow-none" name="category" id="search-filter-type">
                                    <option value="">Chọn loại đất</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ ($filters['category'] ?? $filters['category_id']) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tỉnh/Thành phố -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Tỉnh/Thành phố</label>
                                <select class="form-select shadow-none" name="city" id="search-filter-city">
                                    <option value="">Chọn Tỉnh/Thành phố</option>
                                    @foreach($cities ?? [] as $city)
                                        <option value="{{ $city->id }}" {{ ($filters['city'] ?? $filters['city_id']) == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="search-btn-apply-filters">
                                    <i class="bi bi-funnel"></i> Áp dụng bộ lọc
                                </button>
                                <a href="{{ route('search', ['q' => $keyword]) }}" class="btn btn-outline-secondary" id="search-btn-clear-filters">
                                    <i class="bi bi-x-circle"></i> Xóa tất cả
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
                                                    <span class="text-primary fw-bold fs-5">{{ formatPrice($listing->price) }}</span>
                                                    <span class="text-muted"> • </span>
                                                    <span class="text-dark fw-semibold">{{ number_format($listing->area, 1) }} m²</span>
                                                    @if($pricePerM2Formatted = formatPricePerM2($listing->price_per_m2, $listing->price, $listing->area))
                                                        <span class="text-muted small">({{ $pricePerM2Formatted }})</span>
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
                            @if($listings->total() > 12)
                                <div class="mt-4">
                                    {{ $listings->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
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

        /* Filter Tags */
        .filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, rgba(51, 87, 147, 0.1) 0%, rgba(74, 107, 168, 0.08) 100%);
            color: #335793;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid rgba(51, 87, 147, 0.2);
        }

        .filter-tag .btn-close {
            font-size: 10px;
            opacity: 0.6;
            padding: 0;
            margin-left: 4px;
        }

        .filter-tag .btn-close:hover {
            opacity: 1;
        }

        /* Quick Filter Chips */
        .quick-filter-chip-search {
            transition: all 0.2s ease;
            border: 1px solid rgba(51, 87, 147, 0.3);
            font-size: 13px;
            font-weight: 600;
        }

        .quick-filter-chip-search:hover {
            background: rgba(51, 87, 147, 0.1);
            border-color: #335793;
            transform: translateY(-1px);
        }

        .quick-filter-chip-search.active {
            background: linear-gradient(135deg, #335793 0%, #4a6ba8 100%);
            color: white;
            border-color: #335793;
        }

        /* Price Range Slider */
        .price-range-slider-wrapper {
            position: relative;
            height: 30px;
            padding: 12px 0;
        }

        .price-range-track {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            transform: translateY(-50%);
        }

        .price-range-fill {
            height: 100%;
            background: #335793;
            border-radius: 3px;
            position: absolute;
            left: 0%;
            right: 0%;
        }

        .price-handle {
            position: absolute;
            width: 18px;
            height: 18px;
            background: #fff;
            border: 2px solid #335793;
            border-radius: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            pointer-events: none;
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
        // Format price helper - Rule: < 1 tỉ hiển thị triệu, >= 1 tỉ hiển thị tỉ
        function formatPrice(million) {
            if (million >= 50000) {
                return 'Không giới hạn';
            } else if (million >= 1000) {
                // >= 1000 triệu (>= 1 tỉ) → hiển thị theo tỉ
                const ty = million / 1000;
                if (ty === Math.floor(ty)) {
                    return `đ${new Intl.NumberFormat('vi-VN').format(ty)} tỉ`;
                } else {
                    const tyRounded = Math.round(ty * 10) / 10;
                    return `đ${new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(tyRounded)} tỉ`;
                }
            } else {
                // < 1000 triệu (< 1 tỉ) → hiển thị theo triệu
                if (million === Math.floor(million)) {
                    return `đ${new Intl.NumberFormat('vi-VN').format(million)} triệu`;
                } else {
                    const priceRounded = Math.round(million * 10) / 10;
                    return `đ${new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(priceRounded)} triệu`;
                }
            }
        }


        // Update active filters
        function updateSearchActiveFilters() {
            const params = new URLSearchParams(window.location.search);
            const activeFiltersContainer = document.getElementById('search-active-filters');
            const filterCount = document.getElementById('search-filter-count');

            let activeCount = 0;
            const filters = [];

            // Category
            const categoryId = params.get('category');
            if (categoryId) {
                const categorySelect = document.getElementById('search-filter-type');
                if (categorySelect) {
                    const option = categorySelect.querySelector(`option[value="${categoryId}"]`);
                    if (option) {
                        filters.push({ type: 'category', id: categoryId, label: option.textContent, key: 'category' });
                        activeCount++;
                    }
                }
            }

            // City
            const cityId = params.get('city');
            if (cityId) {
                const citySelect = document.getElementById('search-filter-city');
                if (citySelect) {
                    const option = citySelect.querySelector(`option[value="${cityId}"]`);
                    if (option) {
                        filters.push({ type: 'city', id: cityId, label: option.textContent, key: 'city' });
                        activeCount++;
                    }
                }
            }

            // VIP
            if (params.get('vip')) {
                filters.push({ type: 'vip', label: 'Ưu tiên VIP', key: 'vip' });
                activeCount++;
            }

            // Pháp lý
            const legalStatus = params.get('legal_status');
            if (legalStatus) {
                filters.push({ type: 'legal_status', label: `Pháp lý: ${legalStatus}`, key: 'legal_status' });
                activeCount++;
            }

            // Render tags
            if (activeFiltersContainer) {
                activeFiltersContainer.innerHTML = '';
                if (filters.length > 0) {
                    activeFiltersContainer.style.display = 'flex';
                    filters.forEach(filter => {
                        const tag = document.createElement('div');
                        tag.className = 'filter-tag';
                        tag.innerHTML = `
                        <span>${filter.label}</span>
                        <button type="button" class="btn-close" onclick="removeSearchFilter('${filter.key}', '${filter.id || ''}')" aria-label="Xóa"></button>
                    `;
                        activeFiltersContainer.appendChild(tag);
                    });
                } else {
                    activeFiltersContainer.style.display = 'none';
                }
            }

            // Update count
            if (filterCount) filterCount.textContent = activeCount > 0 ? `${activeCount} tiêu chí` : '0 tiêu chí';
        }

        // Remove filter function
        window.removeSearchFilter = function(key, id) {
            const params = new URLSearchParams(window.location.search);
            const keyword = params.get('q') || '';

            params.delete(key);

            // Redirect with new params
            const newUrl = '/tim-kiem?q=' + encodeURIComponent(keyword) + (params.toString() ? '&' + params.toString() : '');
            window.location.href = newUrl;
        };

        // Quick Filter Chips
        function setupSearchQuickFilterChips() {
            document.querySelectorAll('.quick-filter-chip-search').forEach(chip => {
                chip.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.dataset?.filter;
                    const value = this.dataset?.value;
                    if (!filter || !value) return;

                    const params = new URLSearchParams(window.location.search);
                    const keyword = params.get('q') || '';

                    if (filter === 'vip') {
                        if (params.get('vip') === value) {
                            params.delete('vip');
                            this.classList.remove('active');
                        } else {
                            params.set('vip', value);
                            this.classList.add('active');
                        }
                        const vipInput = document.getElementById('search-filter-vip');
                        if (vipInput) vipInput.value = params.get('vip') || '';
                    } else if (filter === 'legal_status') {
                        if (params.get('legal_status') === value) {
                            params.delete('legal_status');
                            this.classList.remove('active');
                        } else {
                            params.set('legal_status', value);
                            this.classList.add('active');
                        }
                        const legalInput = document.getElementById('search-filter-legal-status');
                        if (legalInput) legalInput.value = params.get('legal_status') || '';
                    }

                    // Submit form
                    const form = document.getElementById('search-filter-form');
                    if (form) {
                        form.submit();
                    }
                });

                // Check if filter is active
                const params = new URLSearchParams(window.location.search);
                const f = chip.dataset?.filter;
                const v = chip.dataset?.value;
                if (f && v && params.get(f) === v) {
                    chip.classList.add('active');
                }
            });
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


        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSearchActiveFilters();
            setupSearchQuickFilterChips();
        });

        // Update filters when URL changes
        window.addEventListener('popstate', function() {
            updateSearchActiveFilters();
        });
    </script>
@endpush
