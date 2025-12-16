<!-- LEFT FILTER SIDEBAR -->
<div class="col-12 col-md-3 col-lg-2 border-end p-3 bg-white sidebar-left">
    <div class="filter-card">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-funnel-fill text-primary"></i>
                <span>Bộ lọc</span>
            </h5>
            <span class="badge bg-primary-subtle text-primary" id="filter-count">0 tiêu chí</span>
        </div>

        <form action="{{ route('listings.index') }}" method="GET" id="filter-form">
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

            <!-- Quận/Huyện -->
            <div class="mb-3">
                <label class="form-label fw-semibold small text-uppercase text-muted">Quận/Huyện</label>
                <select class="form-select shadow-none" name="district" id="filter-district">
                    <option value="">Chọn Quận/Huyện</option>
                    @if(request('city'))
                        @foreach($districts ?? [] as $district)
                            <option value="{{ $district->id }}" {{ request('district') == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    @endif
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
                <button type="submit" class="btn btn-primary" id="btn-apply-filters">
                    <i class="bi bi-funnel"></i> Áp dụng bộ lọc
                </button>
                <button type="button" class="btn btn-light text-muted" id="btn-nearby">
                    <i class="bi bi-geo-alt"></i> Tìm gần tôi
                </button>
            </div>
        </form>
    </div>

    <!-- LEFT SIDEBAR AD BANNER -->
    @include('components.ads.sidebar-left')
</div>

