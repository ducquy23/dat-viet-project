<!-- Modal đăng tin - 3 BƯỚC -->
<div class="modal fade" id="postModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content post-modal-content">
            <div class="modal-header border-0 pb-0">
                <div class="w-100">
                    <h5 class="modal-title mb-3">Đăng tin rao bán - Chỉ 3 bước</h5>
                    <!-- Progress Steps -->
                    <div class="post-steps mb-4">
                        <div class="step-item active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-label">Vị trí</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-item" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-label">Thông tin</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step-item" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-label">Hoàn tất</div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('listings.store') }}" method="POST" enctype="multipart/form-data" id="post-form">
                @csrf
                <div class="modal-body">
                    <!-- BƯỚC 1: VỊ TRÍ -->
                    <div class="post-step-content active" id="step1">
                        <div class="text-center mb-3">
                            <i class="bi bi-geo-alt-fill text-primary" style="font-size: 48px;"></i>
                            <h6 class="mt-2 mb-1">Chọn vị trí lô đất</h6>
                            <p class="text-muted small">Click trên bản đồ hoặc dùng vị trí hiện tại</p>
                        </div>
                        <div class="post-map-container mb-3">
                            <div id="post-map" class="rounded-3" style="height: 300px; border: 2px solid #e9ecef;"></div>
                        </div>
                        <input type="hidden" name="latitude" id="post-latitude" required>
                        <input type="hidden" name="longitude" id="post-longitude" required>
                        <button type="button" class="btn btn-outline-primary w-100 mb-2" id="btn-use-current-location">
                            <i class="bi bi-crosshair"></i> Dùng vị trí hiện tại
                        </button>
                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle"></i> Bạn có thể chỉnh sửa vị trí sau
                        </div>
                    </div>

                    <!-- BƯỚC 2: THÔNG TIN -->
                    <div class="post-step-content" id="step2">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-currency-dollar text-primary"></i>
                                    <span>Giá bán (triệu đồng) *</span>
                                </label>
                                <input type="number" class="form-control form-control-lg" name="price" id="post-price" placeholder="Ví dụ: 1500" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-rulers text-primary"></i>
                                    <span>Diện tích (m²) *</span>
                                </label>
                                <input type="number" class="form-control form-control-lg" name="area" id="post-area" placeholder="Ví dụ: 200" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-telephone text-primary"></i>
                                    <span>Số điện thoại *</span>
                                </label>
                                <input type="tel" class="form-control form-control-lg" name="contact_phone" id="post-phone"
                                       value="{{ auth('partner')->user()?->phone }}" placeholder="09xx xxx xxx" required>
                                <small class="text-muted">Để người mua liên hệ với bạn</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-image text-primary"></i>
                                    <span>Ảnh lô đất (tùy chọn)</span>
                                </label>
                                <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
                                <small class="text-muted">Tối đa 5 ảnh, mỗi ảnh dưới 5MB</small>
                            </div>
                        </div>
                    </div>

                    <!-- BƯỚC 3: HOÀN TẤT -->
                    <div class="post-step-content" id="step3">
                        <div class="text-center mb-4">
                            <div class="success-icon mb-3">
                                <i class="bi bi-check-circle-fill text-success" style="font-size: 64px;"></i>
                            </div>
                            <h5 class="mb-2">Chọn gói đăng tin</h5>
                            <p class="text-muted">Chọn gói phù hợp để tin của bạn được hiển thị tốt nhất</p>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="package-card" data-package="normal">
                                    <div class="package-header">
                                        <i class="bi bi-circle"></i>
                                        <h6 class="mb-0">Gói Thường</h6>
                                    </div>
                                    <div class="package-price">Miễn phí</div>
                                    <ul class="package-features">
                                        <li><i class="bi bi-check"></i> Hiển thị cơ bản</li>
                                        <li><i class="bi bi-check"></i> Pin màu xanh</li>
                                    </ul>
                                </div>
                                <input type="radio" name="package_id" value="1" class="d-none" id="package-normal">
                            </div>
                            <div class="col-6">
                                <div class="package-card package-vip active" data-package="vip">
                                    <div class="package-header">
                                        <i class="bi bi-star-fill"></i>
                                        <h6 class="mb-0">Gói VIP</h6>
                                    </div>
                                    <div class="package-price">50.000đ</div>
                                    <ul class="package-features">
                                        <li><i class="bi bi-check"></i> Pin màu vàng nổi bật</li>
                                        <li><i class="bi bi-check"></i> Ưu tiên hiển thị</li>
                                        <li><i class="bi bi-check"></i> Hiển thị trong carousel</li>
                                    </ul>
                                </div>
                                <input type="radio" name="package_id" value="2" class="d-none" id="package-vip" checked>
                            </div>
                        </div>

                        <div class="alert alert-success small mb-0">
                            <i class="bi bi-info-circle"></i> Tin của bạn sẽ được duyệt trong vòng 24h
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" id="btn-prev-step" style="display: none;">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </button>
                    <div class="flex-grow-1"></div>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="btn-next-step">
                        Tiếp theo <i class="bi bi-arrow-right"></i>
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-post" style="display: none;">
                        <i class="bi bi-check-lg"></i> Đăng tin ngay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



