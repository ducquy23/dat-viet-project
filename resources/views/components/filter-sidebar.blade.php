<!-- Mobile Filter Toggle Button -->
<button class="btn btn-primary d-md-none position-fixed bottom-0 start-0 m-3 rounded-circle shadow-lg" 
        id="mobile-filter-toggle" 
        style="width: 56px; height: 56px; z-index: 1000;"
        data-bs-toggle="offcanvas" 
        data-bs-target="#filter-offcanvas"
        aria-controls="filter-offcanvas">
    <i class="bi bi-funnel-fill"></i>
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
                <button type="button" class="btn btn-sm btn-outline-primary quick-filter-chip" data-filter="has_road" data-value="1">
                    <i class="bi bi-car-front"></i> Đường ô tô
                </button>
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
            <div class="mb-3">
                <label class="form-label fw-semibold small text-uppercase text-muted d-flex align-items-center justify-content-between mb-2">
                    <span>Giá tối đa</span>
                    <span class="badge bg-primary-subtle text-primary" id="price-label">{{ number_format(request('max_price', 5000)) }} triệu</span>
                </label>
                <div class="small text-muted mb-2">
                    <i class="bi bi-info-circle"></i> Tìm các lô đất có giá ≤ giá tối đa (đơn vị: triệu đồng)
                </div>
                <input 
                    type="range" 
                    class="form-range" 
                    min="300" 
                    max="5000" 
                    step="50" 
                    id="filter-price" 
                    name="max_price"
                    value="{{ request('max_price', 5000) }}"
                    aria-label="Giá tối đa">
                <div class="d-flex justify-content-between small text-muted mt-1">
                    <span>300 triệu</span>
                    <span>5000 triệu</span>
                </div>
            </div>

            <!-- Giá tối thiểu -->
            <div class="mb-3">
                <label class="form-label fw-semibold small text-uppercase text-muted">Giá tối thiểu (triệu đồng)</label>
                <input type="number" class="form-control shadow-none" name="min_price" min="0" step="10" placeholder="VD: 500"
                       value="{{ request('min_price') }}">
            </div>

            <!-- Diện tích -->
            <div class="mb-4">
                <label class="form-label fw-semibold small text-uppercase text-muted d-flex align-items-center justify-content-between mb-2">
                    <span>Diện tích tối đa</span>
                    <span class="badge bg-primary-subtle text-primary" id="area-label">{{ number_format(request('max_area', 1000)) }} m²</span>
                </label>
                <div class="small text-muted mb-2">
                    <i class="bi bi-info-circle"></i> Tìm các lô đất có diện tích ≤ diện tích tối đa (đơn vị: mét vuông)
                </div>
                <input 
                    type="range" 
                    class="form-range" 
                    min="50" 
                    max="1000" 
                    step="10" 
                    id="filter-area" 
                    name="max_area"
                    value="{{ request('max_area', 1000) }}"
                    aria-label="Diện tích tối đa">
                <div class="d-flex justify-content-between small text-muted mt-1">
                    <span>50 m²</span>
                    <span>1000 m²</span>
                </div>
            </div>

            <!-- Diện tích tối thiểu -->
            <div class="mb-4">
                <label class="form-label fw-semibold small text-uppercase text-muted">Diện tích tối thiểu (m²)</label>
                <input type="number" class="form-control shadow-none" name="min_area" min="0" step="10" placeholder="VD: 60"
                       value="{{ request('min_area') }}">
            </div>

            <!-- Đường ô tô -->
            <div class="form-check mb-4">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    value="1" 
                    id="filter-road" 
                    name="has_road"
                    {{ request('has_road') ? 'checked' : '' }}>
                <label class="form-check-label text-muted" for="filter-road">
                    Đường ô tô vào
                </label>
            </div>

            <!-- Buttons -->
            <div class="d-grid gap-2">
                <div class="mb-2">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Sắp xếp</label>
                    <select class="form-select shadow-none" name="sort" id="filter-sort">
                        <option value="">Mới nhất</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        <option value="area_asc" {{ request('sort') === 'area_asc' ? 'selected' : '' }}>Diện tích tăng dần</option>
                        <option value="area_desc" {{ request('sort') === 'area_desc' ? 'selected' : '' }}>Diện tích giảm dần</option>
                        <option value="vip_first" {{ request('sort') === 'vip_first' ? 'selected' : '' }}>Ưu tiên VIP</option>
                    </select>
                </div>
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
                <button type="button" class="btn btn-sm btn-outline-primary quick-filter-chip-mobile" data-filter="has_road" data-value="1">
                    <i class="bi bi-car-front"></i> Đường ô tô
                </button>
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
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-uppercase text-muted d-flex align-items-center justify-content-between mb-2">
                        <span>Giá tối đa</span>
                        <span class="badge bg-primary-subtle text-primary" id="price-label-mobile">{{ number_format(request('max_price', 5000)) }} triệu</span>
                    </label>
                    <div class="small text-muted mb-2">
                        <i class="bi bi-info-circle"></i> Tìm các lô đất có giá ≤ giá tối đa (đơn vị: triệu đồng)
                    </div>
                    <input 
                        type="range" 
                        class="form-range" 
                        min="300" 
                        max="5000" 
                        step="50" 
                        id="filter-price-mobile" 
                        name="max_price"
                        value="{{ request('max_price', 5000) }}"
                        aria-label="Giá tối đa">
                    <div class="d-flex justify-content-between small text-muted mt-1">
                        <span>300 triệu</span>
                        <span>5000 triệu</span>
                    </div>
                </div>

                <!-- Giá tối thiểu -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Giá tối thiểu (triệu đồng)</label>
                    <input type="number" class="form-control shadow-none" name="min_price" min="0" step="10" placeholder="VD: 500"
                           value="{{ request('min_price') }}">
                </div>

                <!-- Diện tích -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-uppercase text-muted d-flex align-items-center justify-content-between mb-2">
                        <span>Diện tích tối đa</span>
                        <span class="badge bg-primary-subtle text-primary" id="area-label-mobile">{{ number_format(request('max_area', 1000)) }} m²</span>
                    </label>
                    <div class="small text-muted mb-2">
                        <i class="bi bi-info-circle"></i> Tìm các lô đất có diện tích ≤ diện tích tối đa (đơn vị: mét vuông)
                    </div>
                    <input 
                        type="range" 
                        class="form-range" 
                        min="50" 
                        max="1000" 
                        step="10" 
                        id="filter-area-mobile" 
                        name="max_area"
                        value="{{ request('max_area', 1000) }}"
                        aria-label="Diện tích tối đa">
                    <div class="d-flex justify-content-between small text-muted mt-1">
                        <span>50 m²</span>
                        <span>1000 m²</span>
                    </div>
                </div>

                <!-- Diện tích tối thiểu -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small text-uppercase text-muted">Diện tích tối thiểu (m²)</label>
                    <input type="number" class="form-control shadow-none" name="min_area" min="0" step="10" placeholder="VD: 60"
                           value="{{ request('min_area') }}">
                </div>

                <!-- Đường ô tô -->
                <div class="form-check mb-4">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        value="1" 
                        id="filter-road-mobile" 
                        name="has_road"
                        {{ request('has_road') ? 'checked' : '' }}>
                    <label class="form-check-label text-muted" for="filter-road-mobile">
                        Đường ô tô vào
                    </label>
                </div>

                <!-- Buttons -->
                <div class="d-grid gap-2">
                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-uppercase text-muted">Sắp xếp</label>
                        <select class="form-select shadow-none" name="sort" id="filter-sort-mobile">
                            <option value="">Mới nhất</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                            <option value="area_asc" {{ request('sort') === 'area_asc' ? 'selected' : '' }}>Diện tích tăng dần</option>
                            <option value="area_desc" {{ request('sort') === 'area_desc' ? 'selected' : '' }}>Diện tích giảm dần</option>
                            <option value="vip_first" {{ request('sort') === 'vip_first' ? 'selected' : '' }}>Ưu tiên VIP</option>
                        </select>
                    </div>
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

