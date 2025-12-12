<!-- RIGHT PANEL - DETAIL -->
<div class="col-12 col-md-3 col-lg-3 border-start p-3 bg-white sidebar-right" id="detail-panel">
    @if(isset($listing) && $listing)
        <!-- Server-side rendered listing (for detail page) -->
        @include('components.detail-panel-content', ['listing' => $listing])
    @else
        <!-- Empty State - Will be populated by JavaScript -->
        <div id="detail-panel-empty" class="text-center py-5">
            <i class="bi bi-map text-muted" style="font-size: 64px;"></i>
            <p class="text-muted mt-3">Chọn một lô đất trên bản đồ để xem chi tiết</p>
        </div>
        
        <!-- Dynamic Content - Will be populated by JavaScript -->
        <div id="detail-panel-content" style="display: none;">
            <!-- Gallery -->
            <div class="gallery-main mb-2">
                <img id="lot-main-img" src="" class="img-fluid rounded lot-image" alt="" onerror="this.src='{{ asset('images/placeholder.jpg') }}'">
            </div>
            
            <div class="gallery-thumbs d-flex gap-2 mb-3" id="lot-thumbs"></div>

            <!-- Price & Address -->
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="flex-grow-1">
                    <h4 class="lot-price fw-bold mb-1" id="lot-price"></h4>
                    <p class="text-muted small mb-0 d-flex align-items-center gap-1" id="lot-address">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span></span>
                    </p>
                </div>
                <button class="btn btn-light btn-fav" id="favorite-btn" onclick="toggleFavoriteFromDetail()" title="Thêm vào yêu thích">
                    <i class="bi bi-heart"></i>
                </button>
            </div>

            <!-- Mini Map -->
            <div id="mini-map" class="mini-map mb-3 rounded-3" style="height: 200px;"></div>

            <!-- Category & Tags -->
            <div class="mb-3">
                <p class="text-muted small mb-2 d-flex align-items-center gap-1">
                    <i class="bi bi-tag-fill"></i>
                    <span>Loại đất: <b class="text-dark" id="lot-type"></b></span>
                </p>
                <div class="d-flex flex-wrap gap-2" id="lot-tags"></div>
            </div>

            <!-- Seller Info -->
            <div class="seller-card mb-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="seller-avatar">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" id="seller-name"></div>
                        <div class="text-muted small d-flex align-items-center gap-1">
                            <i class="bi bi-telephone"></i>
                            <span id="seller-phone"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="detail-box mb-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-card-text text-primary me-2"></i>
                    <h6 class="fw-bold mb-0">Thông tin chi tiết</h6>
                </div>
                <ul class="list-unstyled mb-0 small">
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Pháp lý</span>
                        <span class="fw-semibold text-dark" id="lot-legal">Đang cập nhật</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Mặt tiền</span>
                        <span class="fw-semibold text-dark" id="lot-front">Đang cập nhật</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Đường</span>
                        <span class="fw-semibold text-dark" id="lot-road">Đang cập nhật</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Chiều sâu</span>
                        <span class="fw-semibold text-dark" id="lot-depth">Đang cập nhật</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Hướng</span>
                        <span class="fw-semibold text-dark" id="lot-direction">Đang cập nhật</span>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-muted">Đơn giá</span>
                        <span class="fw-semibold text-dark" id="lot-price-per">Đang cập nhật</span>
                    </li>
                </ul>
            </div>

            <!-- Description -->
            <div class="description-box mb-3" id="lot-desc-container" style="display: none;">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    <h6 class="fw-bold mb-0">Mô tả</h6>
                </div>
                <p class="small text-muted mb-0" id="lot-desc"></p>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2 mb-2">
                <a href="#" id="btn-call" class="btn btn-primary w-50 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-telephone-fill"></i>
                    <span>Gọi điện</span>
                </a>
                <a href="#" id="btn-zalo" class="btn btn-outline-primary w-50 d-flex align-items-center justify-content-center gap-2" style="display: none;">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Chat Zalo</span>
                </a>
            </div>

            <a href="#" id="btn-view-detail" class="btn btn-outline-secondary w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-eye"></i>
                <span>Xem chi tiết đầy đủ</span>
            </a>

            <!-- Similar Listings -->
            <h5 class="fw-bold border-top pt-3 mb-3">CÁC LÔ ĐẤT TƯƠNG TỰ</h5>
            <div id="similar-list"></div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function changeMainImage(src, element) {
        const mainImg = document.getElementById('lot-main-img');
        if (mainImg) {
            mainImg.src = src;
        }
        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
        if (element) element.classList.add('active');
    }

    function toggleFavoriteFromDetail() {
        const listingId = window.currentListingId || document.getElementById('favorite-btn')?.getAttribute('data-listing-id');
        if (!listingId) {
            alert('Vui lòng đăng nhập để sử dụng tính năng này');
            return;
        }
        
        toggleFavorite(listingId);
    }

    function toggleFavorite(listingId) {
        fetch(`/api/listings/${listingId}/favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (response.status === 401) {
                alert('Vui lòng đăng nhập để sử dụng tính năng này');
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            
            const btn = document.getElementById('favorite-btn');
            if (btn) {
                const icon = btn.querySelector('i');
                if (data.favorited) {
                    btn.classList.add('active');
                    if (icon) {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                    }
                } else {
                    btn.classList.remove('active');
                    if (icon) {
                        icon.classList.remove('bi-heart-fill');
                        icon.classList.add('bi-heart');
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Initialize mini map if listing exists (server-side)
    @if(isset($listing) && $listing)
    if (document.getElementById('mini-map') && !window.miniMap) {
        window.miniMap = L.map('mini-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([{{ $listing->latitude }}, {{ $listing->longitude }}], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(window.miniMap);

        L.marker([{{ $listing->latitude }}, {{ $listing->longitude }}], {
            icon: {{ $listing->isVip() ? 'iconVip' : 'iconNormal' }}
        }).addTo(window.miniMap);
    }
    @endif
</script>
@endpush
