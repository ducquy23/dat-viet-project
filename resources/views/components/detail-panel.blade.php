<!-- RIGHT PANEL - DETAIL -->
<div class="col-12 col-md-3 col-lg-3 border-start p-3 bg-white sidebar-right" id="detail-panel">
    <!-- RIGHT SIDEBAR AD BANNER -->
    @include('components.ads.right-panel')

    @if(isset($listing) && $listing)
        <!-- Server-side rendered listing (for detail page) -->
        @include('components.detail-panel-content', ['listing' => $listing])
    @else
        <!-- Empty State - Will be populated by JavaScript -->
        <div id="detail-panel-empty" class="empty-state">
            <i class="bi bi-map"></i>
            <p>Chọn một lô đất trên bản đồ để xem chi tiết</p>
        </div>

        <!-- Skeleton Loader -->
        <div id="detail-panel-skeleton" class="skeleton-loader detail-panel" style="display: none;">
            <div class="skeleton skeleton-image" style="height: 250px; margin-bottom: 16px;"></div>
            <div class="skeleton skeleton-title" style="width: 70%; margin-bottom: 12px;"></div>
            <div class="skeleton skeleton-text" style="width: 50%; margin-bottom: 16px;"></div>
            <div class="skeleton skeleton-text" style="margin-bottom: 8px;"></div>
            <div class="skeleton skeleton-text" style="width: 80%; margin-bottom: 8px;"></div>
            <div class="skeleton skeleton-text" style="width: 60%; margin-bottom: 16px;"></div>
            <div class="skeleton skeleton-image" style="height: 150px; margin-bottom: 16px;"></div>
            <div class="skeleton skeleton-text" style="margin-bottom: 8px;"></div>
            <div class="skeleton skeleton-text" style="width: 90%; margin-bottom: 8px;"></div>
            <div class="skeleton skeleton-text" style="width: 75%;"></div>
        </div>

        <!-- Dynamic Content - Will be populated by JavaScript -->
        <div id="detail-panel-content" style="display: none;">
            <!-- Gallery -->
            <div class="gallery-main mb-2">
                <img id="lot-main-img" src="" class="img-fluid rounded lot-image" alt="" onerror="this.src='{{ asset('images/Image-not-found.png') }}'" style="cursor: pointer;" onclick="openImageModal(this.src)">
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
                <button class="btn btn-light btn-fav d-flex align-items-center justify-content-center"
                        id="favorite-btn"
                        onclick="toggleFavoriteFromDetail()"
                        title="Thêm vào yêu thích"
                        style="width:44px; height:44px; border-radius:50%; padding:0;">
                    <i class="bi bi-heart" style="font-size:18px;"></i>
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
            // Update onclick handler for lightbox
            mainImg.onclick = function() {
                if (typeof openImageModal === 'function') {
                    openImageModal(this.src);
                }
            };
        }
        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
        if (element) element.classList.add('active');
    }

    function toggleFavoriteFromDetail() {
        if (typeof window.isAuthenticated === 'undefined' || !window.isAuthenticated) {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            return;
        }
        
        const listingId = window.currentListingId || document.getElementById('favorite-btn')?.getAttribute('data-listing-id');
        if (!listingId) {
            // Mở modal đăng nhập
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            return;
        }

        toggleFavorite(listingId);
    }

    function toggleFavorite(listingId) {
        // Kiểm tra đăng nhập trước khi gọi API
        if (typeof window.isAuthenticated === 'undefined' || !window.isAuthenticated) {
            // Chưa đăng nhập, mở modal đăng nhập và không làm gì cả
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            return;
        }
        
        fetch(`/api/listings/${listingId}/favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (response.status === 401) {
                // Mở modal đăng nhập, không hiển thị toast
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
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

    // Image Modal/Lightbox - Phóng to ảnh khi click
    window.openImageModal = function(imageSrc) {
        // Create modal if not exists
        let modal = document.getElementById('image-lightbox-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'image-lightbox-modal';
            modal.className = 'modal fade';
            modal.setAttribute('tabindex', '-1');
            modal.setAttribute('aria-hidden', 'true');
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content bg-dark border-0" style="background: rgba(0,0,0,0.9) !important;">
                        <div class="modal-header border-0 position-absolute top-0 end-0" style="z-index: 1051; background: transparent;">
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: drop-shadow(0 0 3px rgba(0,0,0,0.8)); background-color: rgba(255,255,255,0.2);"></button>
                        </div>
                        <div class="modal-body p-0 text-center d-flex align-items-center justify-content-center" style="min-height: 90vh;">
                            <img id="lightbox-image" src="" class="img-fluid" alt="Ảnh phóng to" style="max-height: 90vh; max-width: 100%; width: auto; object-fit: contain;">
                        </div>
                        <div class="modal-footer border-0 position-absolute bottom-0 start-50 translate-middle-x mb-3" style="z-index: 1051; background: transparent;">
                            <button type="button" class="btn btn-sm btn-light me-2" id="lightbox-prev" onclick="changeLightboxImage(-1)" style="display: none; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                                <i class="bi bi-chevron-left"></i> Trước
                            </button>
                            <button type="button" class="btn btn-sm btn-light" id="lightbox-next" onclick="changeLightboxImage(1)" style="display: none; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                                Sau <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        // Set image source
        const lightboxImg = document.getElementById('lightbox-image');
        if (lightboxImg) {
            lightboxImg.src = imageSrc;
        }

        // Show navigation buttons if there are multiple images
        const thumbs = document.querySelectorAll('#lot-thumbs img');
        if (thumbs.length > 1) {
            document.getElementById('lightbox-prev').style.display = 'inline-block';
            document.getElementById('lightbox-next').style.display = 'inline-block';
        } else {
            document.getElementById('lightbox-prev').style.display = 'none';
            document.getElementById('lightbox-next').style.display = 'none';
        }

        // Show modal
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();

        // Store current image index
        window.currentLightboxIndex = Array.from(thumbs).findIndex(thumb => {
            const thumbSrc = thumb.src || thumb.getAttribute('src');
            return thumbSrc === imageSrc || (thumbSrc && imageSrc && thumbSrc.includes(imageSrc.split('/').pop()));
        });
        if (window.currentLightboxIndex === -1) {
            window.currentLightboxIndex = 0;
        }
    };

    window.changeLightboxImage = function(direction) {
        const thumbs = document.querySelectorAll('#lot-thumbs img');
        if (thumbs.length === 0) {
            // Fallback: get images from main image and thumbnails
            const mainImg = document.getElementById('lot-main-img');
            if (mainImg && mainImg.src) {
                const lightboxImg = document.getElementById('lightbox-image');
                if (lightboxImg) {
                    lightboxImg.src = mainImg.src;
                }
            }
            return;
        }

        let currentIndex = window.currentLightboxIndex || 0;
        currentIndex += direction;

        if (currentIndex < 0) {
            currentIndex = thumbs.length - 1;
        } else if (currentIndex >= thumbs.length) {
            currentIndex = 0;
        }

        window.currentLightboxIndex = currentIndex;
        const thumb = thumbs[currentIndex];
        const imageSrc = thumb.src || thumb.getAttribute('src');

        const lightboxImg = document.getElementById('lightbox-image');
        if (lightboxImg && imageSrc) {
            lightboxImg.src = imageSrc;
        }

        // Update main image in detail panel
        const mainImg = document.getElementById('lot-main-img');
        if (mainImg && imageSrc) {
            mainImg.src = imageSrc;
        }

        // Update active thumbnail
        document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
        const thumbBtn = thumb.closest('.thumb');
        if (thumbBtn) {
            thumbBtn.classList.add('active');
        }
    };

    // Keyboard navigation for lightbox
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('image-lightbox-modal');
        if (modal && modal.classList.contains('show')) {
            if (e.key === 'ArrowLeft') {
                changeLightboxImage(-1);
            } else if (e.key === 'ArrowRight') {
                changeLightboxImage(1);
            } else if (e.key === 'Escape') {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
            }
        }
    });
</script>
@endpush
