<!-- Mobile Filter Toggle Button -->
<button class="btn btn-primary d-md-none position-fixed bottom-0 start-0 m-3 rounded-circle shadow-lg"
        id="mobile-filter-toggle"
        style="width: 56px; height: 56px; z-index: 1000;"
        data-bs-toggle="offcanvas"
        data-bs-target="#filter-offcanvas"
        aria-controls="filter-offcanvas">
    <i class="bi bi-search"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="mobile-filter-badge" style="display: none;">0</span>
</button>

<!-- LEFT FILTER SIDEBAR -->
<div class="col-12 col-md-3 col-lg-2 border-end p-3 bg-white sidebar-left d-none d-md-block">
    <div class="filter-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-funnel-fill text-primary"></i>
                <span>Bộ lọc</span>
            </h5>
            <span class="badge bg-primary-subtle text-primary" id="filter-count">0 tiêu chí</span>
        </div>

        <!-- Active Filter Tags -->
        <div id="active-filters" class="mb-3 d-flex flex-wrap gap-2" style="display: none !important;">
            <!-- Will be populated by JavaScript -->
        </div>

        <!-- Quick Filter Chips -->
        <div class="mb-3">
            <label class="form-label fw-semibold small text-uppercase text-muted mb-2">Lọc nhanh</label>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary quick-filter-chip" data-filter="legal_status" data-value="Sổ đỏ">
                    <i class="bi bi-file-earmark-check"></i> Sổ đỏ
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary quick-filter-chip" data-filter="vip" data-value="1">
                    <i class="bi bi-star-fill"></i> VIP
                </button>
            </div>
        </div>

        <form action="{{ route('listings.index') }}" method="GET" id="filter-form">
            <input type="hidden" name="vip" id="filter-vip" value="{{ request('vip') }}">
            <input type="hidden" name="legal_status" id="filter-legal-status" value="{{ request('legal_status') }}">

            <!-- Loại đất -->
            <div class="mb-3">
                <label class="form-label fw-semibold small text-uppercase text-muted">Loại đất</label>
                <select class="form-select shadow-none" name="category" id="filter-type">
                    <option value="">Chọn loại đất</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tỉnh/Thành phố -->
            <div class="mb-3">
                <label class="form-label fw-semibold small text-uppercase text-muted">Tỉnh/Thành phố</label>
                <select class="form-select shadow-none" name="city" id="filter-city">
                    <option value="">Chọn Tỉnh/Thành phố</option>
                    @foreach($cities ?? [] as $city)
                        <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Khoảng giá -->
            <div class="mb-3 price-filter-section">
                <div class="price-filter-header d-flex align-items-center justify-content-between mb-2" style="cursor: pointer;" onclick="togglePriceFilter(this)">
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
                            id="filter-price-min"
                            name="min_price_million"
                            data-price-type="min"
                            value="{{ request('min_price') ? round(request('min_price') / 1000000) : 50 }}"
                            style="position: absolute; top: 50%; left: 0; width: 100%; height: 30px; margin: 0; padding: 0; transform: translateY(-50%); opacity: 0; cursor: pointer; z-index: 5;">
                        <input type="hidden" name="min_price" id="min_price_hidden" value="{{ request('min_price', '') }}">
                        <input
                            type="range"
                            class="form-range price-range-input price-range-max"
                            min="50"
                            max="50000"
                            step="50"
                            id="filter-price-max"
                            name="max_price_million"
                            data-price-type="max"
                            value="{{ request('max_price') ? round(request('max_price') / 1000000) : 50000 }}"
                            style="position: absolute; top: 50%; left: 0; width: 100%; height: 30px; margin: 0; padding: 0; transform: translateY(-50%); opacity: 0; cursor: pointer; z-index: 6;">
                        <input type="hidden" name="max_price" id="max_price_hidden" value="{{ request('max_price', '') }}">
                        <div class="price-range-handles" style="position: absolute; top: 50%; left: 0; right: 0; height: 30px; transform: translateY(-50%); pointer-events: none; z-index: 1;">
                            <div class="price-handle price-handle-min" style="position: absolute; width: 18px; height: 18px; background: #fff; border: 2px solid #335793; border-radius: 50%; top: 50%; left: 0%; transform: translate(-50%, -50%); box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                            <div class="price-handle price-handle-max" style="position: absolute; width: 18px; height: 18px; background: #fff; border: 2px solid #335793; border-radius: 50%; top: 50%; left: 100%; transform: translate(-50%, -50%); box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                        </div>
                    </div>
                    <div class="price-range-display">
                        <span class="text-dark fw-semibold">Giá: </span>
                        <span class="text-dark" id="price-range-display">đ50 triệu - Không giới hạn</span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary" id="btn-apply-filters">
                    <i class="bi bi-funnel"></i> Áp dụng bộ lọc
                </button>
                <button type="button" class="btn btn-light text-muted" id="btn-nearby">
                    <i class="bi bi-geo-alt"></i> Tìm gần tôi
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btn-clear-filters">
                    <i class="bi bi-x-circle"></i> Xóa tất cả
                </button>
            </div>
        </form>
    </div>

    <!-- LEFT SIDEBAR AD BANNER -->
    @include('components.ads.sidebar-left')
</div>

<!-- Mobile Filter Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="filter-offcanvas" aria-labelledby="filter-offcanvas-label">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold d-flex align-items-center gap-2" id="filter-offcanvas-label">
            <i class="bi bi-funnel-fill text-primary"></i>
            <span>Bộ lọc</span>
            <span class="badge bg-primary-subtle text-primary ms-auto" id="filter-count-mobile">0 tiêu chí</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-3">
        <!-- Active Filter Tags Mobile -->
        <div id="active-filters-mobile" class="mb-3 d-flex flex-wrap gap-2" style="display: none !important;">
            <!-- Will be populated by JavaScript -->
        </div>

        <!-- Quick Filter Chips Mobile -->
        <div class="mb-3">
            <label class="form-label fw-semibold small text-uppercase text-muted mb-2">Lọc nhanh</label>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary quick-filter-chip-mobile" data-filter="legal_status" data-value="Sổ đỏ">
                    <i class="bi bi-file-earmark-check"></i> Sổ đỏ
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary quick-filter-chip-mobile" data-filter="vip" data-value="1">
                    <i class="bi bi-star-fill"></i> VIP
                </button>
            </div>
        </div>

        <div class="filter-card">
            <form action="{{ route('listings.index') }}" method="GET" id="filter-form-mobile">
                <input type="hidden" name="vip" id="filter-vip-mobile" value="{{ request('vip') }}">
                <input type="hidden" name="legal_status" id="filter-legal-status-mobile" value="{{ request('legal_status') }}">

                <!-- Loại đất -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Loại đất</label>
                    <select class="form-select shadow-none" name="category" id="filter-type-mobile">
                        <option value="">Chọn loại đất</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tỉnh/Thành phố -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Tỉnh/Thành phố</label>
                    <select class="form-select shadow-none" name="city" id="filter-city-mobile">
                        <option value="">Chọn Tỉnh/Thành phố</option>
                        @foreach($cities ?? [] as $city)
                            <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Khoảng giá -->
                <div class="mb-3 price-filter-section">
                    <div class="price-filter-header d-flex align-items-center justify-content-between mb-2" style="cursor: pointer;" onclick="togglePriceFilter(this)">
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
                                id="filter-price-min-mobile"
                                name="min_price_million"
                                data-price-type="min"
                                value="{{ request('min_price') ? round(request('min_price') / 1000000) : 50 }}"
                                style="position: absolute; top: 50%; left: 0; width: 100%; height: 30px; margin: 0; padding: 0; transform: translateY(-50%); opacity: 0; cursor: pointer; z-index: 5;">
                            <input type="hidden" name="min_price" id="min_price_hidden_mobile" value="{{ request('min_price', '') }}">
                            <input
                                type="range"
                                class="form-range price-range-input price-range-max"
                                min="50"
                                max="50000"
                                step="50"
                                id="filter-price-max-mobile"
                                name="max_price_million"
                                data-price-type="max"
                                value="{{ request('max_price') ? round(request('max_price') / 1000000) : 50000 }}"
                                style="position: absolute; top: 50%; left: 0; width: 100%; height: 30px; margin: 0; padding: 0; transform: translateY(-50%); opacity: 0; cursor: pointer; z-index: 6;">
                            <input type="hidden" name="max_price" id="max_price_hidden_mobile" value="{{ request('max_price', '') }}">
                            <div class="price-range-handles" style="position: absolute; top: 50%; left: 0; right: 0; height: 30px; transform: translateY(-50%); pointer-events: none; z-index: 1;">
                                <div class="price-handle price-handle-min" style="position: absolute; width: 18px; height: 18px; background: #fff; border: 2px solid #335793; border-radius: 50%; top: 50%; left: 0%; transform: translate(-50%, -50%); box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                                <div class="price-handle price-handle-max" style="position: absolute; width: 18px; height: 18px; background: #fff; border: 2px solid #335793; border-radius: 50%; top: 50%; left: 100%; transform: translate(-50%, -50%); box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                            </div>
                        </div>
                        <div class="price-range-display">
                            <span class="text-dark fw-semibold">Giá: </span>
                            <span class="text-dark" id="price-range-display-mobile">đ50 triệu - Không giới hạn</span>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary" id="btn-apply-filters-mobile">
                        <i class="bi bi-funnel"></i> Áp dụng bộ lọc
                    </button>
                    <button type="button" class="btn btn-light text-muted" id="btn-nearby-mobile">
                        <i class="bi bi-geo-alt"></i> Tìm gần tôi
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btn-clear-filters-mobile">
                        <i class="bi bi-x-circle"></i> Xóa tất cả
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

