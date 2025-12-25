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
                            @if($filters['min_price'])
                                <input type="hidden" name="min_price" value="{{ $filters['min_price'] }}">
                            @endif
                            @if($filters['max_price'])
                                <input type="hidden" name="max_price" value="{{ $filters['max_price'] }}">
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

                            <!-- Khoảng giá -->
                            <div class="mb-3 price-filter-section">
                                <div class="price-filter-header d-flex align-items-center justify-content-between mb-2" style="cursor: pointer;" onclick="toggleSearchPriceFilter(this)">
                                    <label class="form-label fw-semibold mb-0" style="color: #335793;">Lọc theo giá</label>
                                    <i class="bi bi-chevron-down price-filter-icon" style="color: #000;"></i>
                                </div>
                                <div class="price-filter-content" style="display: block;">
                                    <div class="price-range-slider-wrapper position-relative mb-3" style="height: 30px; padding: 12px 0;">
                                        <div class="price-range-track" style="height: 6px; background: #e9ecef; border-radius: 3px; position: absolute; top: 50%; left: 0; right: 0; transform: translateY(-50%);">
                                            <div class="price-range-fill" style="height: 100%; background: #335793; border-radius: 3px; position: absolute; left: 0%; right: 0%;"></div>
                                        </div>
                                        <input
                                            type="range"
                                            class="form-range price-range-input price-range-min"
                                            min="50"
                                            max="50000"
                                            step="50"
                                            id="search-filter-price-min"
                                            data-price-type="min"
                                            value="{{ $filters['min_price'] ? round($filters['min_price'] / 1000000) : 50 }}"
                                            style="position: absolute; top: 50%; left: 0; width: 100%; height: 30px; margin: 0; padding: 0; transform: translateY(-50%); opacity: 0; cursor: pointer; z-index: 5;">
                                        <input type="hidden" name="min_price" id="search_min_price_hidden" value="{{ $filters['min_price'] ?? '' }}">
                                        <input
                                            type="range"
                                            class="form-range price-range-input price-range-max"
                                            min="50"
                                            max="50000"
                                            step="50"
                                            id="search-filter-price-max"
                                            data-price-type="max"
                                            value="{{ $filters['max_price'] ? round($filters['max_price'] / 1000000) : 50000 }}"
                                            style="position: absolute; top: 50%; left: 0; width: 100%; height: 30px; margin: 0; padding: 0; transform: translateY(-50%); opacity: 0; cursor: pointer; z-index: 6;">
                                        <input type="hidden" name="max_price" id="search_max_price_hidden" value="{{ $filters['max_price'] ?? '' }}">
                                        <div class="price-range-handles" style="position: absolute; top: 50%; left: 0; right: 0; height: 30px; transform: translateY(-50%); pointer-events: none; z-index: 1;">
                                            <div class="price-handle price-handle-min" style="position: absolute; width: 18px; height: 18px; background: #fff; border: 2px solid #335793; border-radius: 50%; top: 50%; left: 0%; transform: translate(-50%, -50%); box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                            <div class="price-handle price-handle-max" style="position: absolute; width: 18px; height: 18px; background: #fff; border: 2px solid #335793; border-radius: 50%; top: 50%; left: 100%; transform: translate(-50%, -50%); box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                        </div>
                                    </div>
                                    <div class="price-range-display">
                                        <span class="text-dark fw-semibold">Giá: </span>
                                        <span class="text-dark" id="search-price-range-display">đ50 triệu - Không giới hạn</span>
                                    </div>
                                </div>
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
                                    {{ $listings->links() }}
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
        // Format price helper
        function formatPrice(million) {
            if (million >= 50000) {
                return 'Không giới hạn';
            } else if (million >= 1000) {
                const ty = (million / 1000).toFixed(million % 1000 === 0 ? 0 : 1);
                return `đ${ty} tỉ`;
            } else {
                return `đ${new Intl.NumberFormat('vi-VN').format(million)} triệu`;
            }
        }

        // Update price range slider UI
        function updateSearchPriceRange(minInput, maxInput, displayEl, fillEl, minHandle, maxHandle) {
            const min = parseInt(minInput.value);
            const max = parseInt(maxInput.value);
            const minVal = parseInt(minInput.min);
            const maxVal = parseInt(maxInput.max);

            const minPercent = ((min - minVal) / (maxVal - minVal)) * 100;
            const maxPercent = ((max - minVal) / (maxVal - minVal)) * 100;

            if (fillEl) {
                fillEl.style.left = minPercent + '%';
                fillEl.style.right = (100 - maxPercent) + '%';
            }

            if (minHandle) minHandle.style.left = minPercent + '%';
            if (maxHandle) maxHandle.style.left = maxPercent + '%';

            if (displayEl) {
                const maxLabel = max >= 50000 ? 'Không giới hạn' : formatPrice(max);
                displayEl.textContent = `${formatPrice(min)} - ${maxLabel}`;
            }

            const minHidden = document.getElementById('search_min_price_hidden');
            const maxHidden = document.getElementById('search_max_price_hidden');
            if (minHidden) minHidden.value = min * 1_000_000;
            if (maxHidden) {
                if (max >= 50000) {
                    maxHidden.value = '';
                } else {
                    maxHidden.value = max * 1_000_000;
                }
            }
        }

        // Setup price range slider for search page
        function setupSearchPriceRangeSlider() {
            const minInput = document.getElementById('search-filter-price-min');
            const maxInput = document.getElementById('search-filter-price-max');
            const displayEl = document.getElementById('search-price-range-display');
            const wrapper = minInput?.closest('.price-range-slider-wrapper');
            const fillEl = wrapper?.querySelector('.price-range-fill');
            const minHandle = wrapper?.querySelector('.price-handle-min');
            const maxHandle = wrapper?.querySelector('.price-handle-max');

            if (!minInput || !maxInput) return;

            let activeInput = null;
            let isPriceDragging = false;

            // Initial update
            updateSearchPriceRange(minInput, maxInput, displayEl, fillEl, minHandle, maxHandle);

            function getActiveInput(x) {
                const rect = wrapper.getBoundingClientRect();
                const percent = ((x - rect.left) / rect.width) * 100;

                const min = parseInt(minInput.value);
                const max = parseInt(maxInput.value);
                const minVal = parseInt(minInput.min);
                const maxVal = parseInt(minInput.max);
                const minPercent = ((min - minVal) / (maxVal - minVal)) * 100;
                const maxPercent = ((max - minVal) / (maxVal - minVal)) * 100;

                const distanceToMin = Math.abs(percent - minPercent);
                const distanceToMax = Math.abs(percent - maxPercent);

                return distanceToMin <= distanceToMax ? minInput : maxInput;
            }

            wrapper.addEventListener('mousedown', function (e) {
                if (e.target === minInput || e.target === maxInput) {
                    activeInput = e.target;
                    isPriceDragging = true;
                    return;
                }

                const rect = wrapper.getBoundingClientRect();
                const percent = ((e.clientX - rect.left) / rect.width) * 100;

                activeInput = getActiveInput(e.clientX);
                isPriceDragging = true;

                const minVal = parseInt(activeInput.min);
                const maxVal = parseInt(activeInput.max);

                let value = minVal + (percent / 100) * (maxVal - minVal);
                value = Math.round(value / 50) * 50;

                if (activeInput === minInput) {
                    value = Math.min(value, parseInt(maxInput.value));
                } else {
                    value = Math.max(value, parseInt(minInput.value));
                }

                activeInput.value = value;
                activeInput.dispatchEvent(new Event('input', { bubbles: true }));
            });

            const handleMouseUp = function() {
                minInput.style.zIndex = '5';
                maxInput.style.zIndex = '6';
                activeInput = null;
                isPriceDragging = false;
            };
            document.addEventListener('mouseup', handleMouseUp);
            wrapper.addEventListener('mouseup', handleMouseUp);
            wrapper.addEventListener('mouseleave', handleMouseUp);

            minInput.addEventListener('input', function() {
                let currentMin = parseInt(this.value);
                const currentMax = parseInt(maxInput.value);
                const minVal = parseInt(this.min);
                const maxVal = parseInt(this.max);

                currentMin = Math.max(minVal, Math.min(maxVal, currentMin));
                if (currentMin > currentMax) {
                    currentMin = currentMax;
                }

                this.value = currentMin;
                updateSearchPriceRange(minInput, maxInput, displayEl, fillEl, minHandle, maxHandle);
            });

            maxInput.addEventListener('input', function() {
                const currentMin = parseInt(minInput.value);
                let currentMax = parseInt(this.value);
                const minVal = parseInt(this.min);
                const maxVal = parseInt(this.max);

                currentMax = Math.max(minVal, Math.min(maxVal, currentMax));
                if (currentMax < currentMin) {
                    currentMax = currentMin;
                }

                this.value = currentMax;
                updateSearchPriceRange(minInput, maxInput, displayEl, fillEl, minHandle, maxHandle);
            });

            let isDragging = false;
            wrapper.addEventListener('mousemove', function(e) {
                if (activeInput && (e.buttons === 1 || isDragging)) {
                    isDragging = true;
                    isPriceDragging = true;
                    const rect = wrapper.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const percent = Math.max(0, Math.min(100, (x / rect.width) * 100));
                    const minVal = parseInt(activeInput.min);
                    const maxVal = parseInt(activeInput.max);
                    let value = minVal + (percent / 100) * (maxVal - minVal);
                    value = Math.round(value / 50) * 50;

                    value = Math.max(minVal, Math.min(maxVal, value));

                    if (activeInput === minInput) {
                        const currentMax = parseInt(maxInput.value);
                        value = Math.min(value, currentMax);
                    } else {
                        const currentMin = parseInt(minInput.value);
                        value = Math.max(value, currentMin);
                    }

                    if (parseInt(activeInput.value) !== value) {
                        activeInput.value = value;
                        activeInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            });

            document.addEventListener('mouseup', function() {
                isPriceDragging = false;
            });
        }

        // Toggle price filter collapse/expand
        window.toggleSearchPriceFilter = function(header) {
            const content = header.nextElementSibling;
            const icon = header.querySelector('.price-filter-icon');
            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.classList.remove('bi-chevron-up');
                icon.classList.add('bi-chevron-down');
            } else {
                content.style.display = 'none';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-up');
            }
        };

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

            // Price Range
            const minPrice = params.get('min_price');
            const maxPrice = params.get('max_price');
            if (minPrice || maxPrice) {
                const minMillion = minPrice ? Math.round(parseInt(minPrice) / 1000000) : 50;
                const maxMillion = maxPrice ? Math.round(parseInt(maxPrice) / 1000000) : 50000;
                const maxLabel = maxMillion >= 50000 ? 'Không giới hạn' : formatPrice(maxMillion);
                filters.push({ type: 'price', label: `Giá: ${formatPrice(minMillion)} - ${maxLabel}`, key: 'price_range' });
                activeCount++;
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

            if (key === 'price_range') {
                params.delete('min_price');
                params.delete('max_price');
            } else {
                params.delete(key);
            }

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

        // Initialize price range from URL params
        function initializeSearchPriceRangeFromParams() {
            const params = new URLSearchParams(window.location.search);
            const minPriceParam = params.get('min_price');
            const maxPriceParam = params.get('max_price');

            // Parse min_price - should be in VND (đồng) from URL
            let minPrice = null;
            if (minPriceParam && minPriceParam !== '' && minPriceParam !== '0') {
                minPrice = parseInt(minPriceParam);
                // If value is less than 1 million, it might already be in millions
                // Otherwise, convert from VND to millions
                if (minPrice >= 1000000) {
                    minPrice = Math.round(minPrice / 1000000);
                }
            }

            // Parse max_price - empty/null means unlimited
            let maxPrice = null;
            if (maxPriceParam && maxPriceParam !== '' && maxPriceParam !== '0') {
                maxPrice = parseInt(maxPriceParam);
                // If value is less than 1 million, it might already be in millions
                // Otherwise, convert from VND to millions
                if (maxPrice >= 1000000) {
                    maxPrice = Math.round(maxPrice / 1000000);
                }
            }

            // Set default values
            let minPriceMillion = minPrice || 50;
            let maxPriceMillion = maxPrice || 50000;

            // Ensure min <= max
            if (minPriceMillion > maxPriceMillion && maxPrice) {
                const temp = minPriceMillion;
                minPriceMillion = maxPriceMillion;
                maxPriceMillion = temp;
            }

            const minInput = document.getElementById('search-filter-price-min');
            const maxInput = document.getElementById('search-filter-price-max');
            if (minInput && maxInput) {
                minInput.value = minPriceMillion;
                maxInput.value = maxPriceMillion;
                const displayEl = document.getElementById('search-price-range-display');
                const wrapper = minInput.closest('.price-range-slider-wrapper');
                const fillEl = wrapper?.querySelector('.price-range-fill');
                const minHandle = wrapper?.querySelector('.price-handle-min');
                const maxHandle = wrapper?.querySelector('.price-handle-max');
                updateSearchPriceRange(minInput, maxInput, displayEl, fillEl, minHandle, maxHandle);
            }
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

        // Update hidden inputs before form submit
        const searchFilterForm = document.getElementById('search-filter-form');
        if (searchFilterForm) {
            searchFilterForm.addEventListener('submit', function(e) {
                // Update price hidden inputs before submit
                const minInput = document.getElementById('search-filter-price-min');
                const maxInput = document.getElementById('search-filter-price-max');
                const minHidden = document.getElementById('search_min_price_hidden');
                const maxHidden = document.getElementById('search_max_price_hidden');

                if (minInput && minHidden) {
                    const minMillion = parseInt(minInput.value) || 50;
                    minHidden.value = minMillion * 1_000_000;
                }

                if (maxInput && maxHidden) {
                    const maxMillion = parseInt(maxInput.value) || 50000;
                    if (maxMillion >= 50000) {
                        maxHidden.value = ''; // Unlimited
                    } else {
                        maxHidden.value = maxMillion * 1_000_000;
                    }
                }
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            setupSearchPriceRangeSlider();
            updateSearchActiveFilters();
            setupSearchQuickFilterChips();
            initializeSearchPriceRangeFromParams();
        });

        // Update filters when URL changes
        window.addEventListener('popstate', function() {
            updateSearchActiveFilters();
            initializeSearchPriceRangeFromParams();
        });
    </script>
@endpush
