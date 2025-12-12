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

            @auth('partner')
            <form action="{{ route('listings.store') }}" method="POST" enctype="multipart/form-data" id="post-form">
                @csrf
                <div class="modal-body">
                    <!-- BƯỚC 1: VỊ TRÍ -->
                    <div class="post-step-content active" id="step1">
                        <div class="text-center mb-3">
                            <i class="bi bi-geo-alt-fill text-primary" style="font-size: 48px;"></i>
                            <h6 class="mt-2 mb-1">Chọn vị trí lô đất</h6>
                            <p class="text-muted small">Nhập địa chỉ, click trên bản đồ hoặc dùng vị trí hiện tại</p>
                        </div>
                        
                        <!-- Tìm kiếm địa chỉ -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                <i class="bi bi-search text-primary"></i>
                                <span>Tìm kiếm địa chỉ</span>
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="post-address-search" 
                                       placeholder="Nhập địa chỉ, ví dụ: 123 Đường Láng, Hà Nội">
                                <button type="button" class="btn btn-primary" id="btn-search-address">
                                    <i class="bi bi-search"></i> Tìm
                                </button>
                            </div>
                            <small class="text-muted">Nhập địa chỉ để tự động tìm vị trí trên bản đồ</small>
                        </div>

                        <div class="post-map-container mb-3">
                            <div id="post-map" class="rounded-3" style="height: 300px; border: 2px solid #e9ecef;"></div>
                        </div>
                        <input type="hidden" name="latitude" id="post-latitude" required>
                        <input type="hidden" name="longitude" id="post-longitude" required>
                        
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-outline-primary flex-fill" id="btn-use-current-location">
                                <i class="bi bi-crosshair"></i> Dùng vị trí hiện tại
                            </button>
                        </div>
                        <div class="alert alert-info small mb-0">
                            <i class="bi bi-info-circle"></i> Bạn có thể chỉnh sửa vị trí sau bằng cách kéo marker trên bản đồ
                        </div>
                    </div>

                    <!-- BƯỚC 2: THÔNG TIN -->
                    <div class="post-step-content" id="step2">
                        <div class="row g-3">
                            <!-- Danh mục và Tỉnh/Thành phố -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-tag-fill text-primary"></i>
                                    <span>Loại đất *</span>
                                </label>
                                <select class="form-select form-control-lg" name="category_id" id="post-category" required>
                                    <option value="">Chọn loại đất</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-geo-alt-fill text-primary"></i>
                                    <span>Tỉnh/Thành phố *</span>
                                </label>
                                <select class="form-select form-control-lg" name="city_id" id="post-city" required>
                                    <option value="">Chọn Tỉnh/Thành phố</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-pin-map-fill text-primary"></i>
                                    <span>Quận/Huyện</span>
                                </label>
                                <select class="form-select form-control-lg" name="district_id" id="post-district">
                                    <option value="">Chọn Quận/Huyện</option>
                                </select>
                            </div>

                            <!-- Tiêu đề và Địa chỉ -->
                            <div class="col-12">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-card-heading text-primary"></i>
                                    <span>Tiêu đề tin đăng *</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" name="title" id="post-title" 
                                       placeholder="Ví dụ: Bán lô đất mặt tiền đường Láng, 200m²" required maxlength="255">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-geo-alt text-primary"></i>
                                    <span>Địa chỉ chi tiết *</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" name="address" id="post-address" 
                                       placeholder="Ví dụ: 123 Đường Láng, Phường Láng Thượng" required maxlength="255">
                                <small class="text-muted">Địa chỉ sẽ được tự động điền từ tìm kiếm ở bước 1</small>
                            </div>

                            <!-- Mô tả -->
                            <div class="col-12">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-card-text text-primary"></i>
                                    <span>Mô tả chi tiết</span>
                                </label>
                                <textarea class="form-control" name="description" id="post-description" rows="4" 
                                          placeholder="Mô tả về lô đất: vị trí, tiện ích xung quanh, pháp lý..."></textarea>
                            </div>

                            <!-- Giá và Diện tích -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-currency-dollar text-primary"></i>
                                    <span>Giá bán (triệu đồng) *</span>
                                </label>
                                <input type="number" class="form-control form-control-lg" name="price" id="post-price" 
                                       placeholder="Ví dụ: 1500" step="0.01" min="0" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-rulers text-primary"></i>
                                    <span>Diện tích (m²) *</span>
                                </label>
                                <input type="number" class="form-control form-control-lg" name="area" id="post-area" 
                                       placeholder="Ví dụ: 200" step="0.01" min="0" required>
                            </div>

                            <!-- Mặt tiền và Chiều sâu -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-arrows-expand text-primary"></i>
                                    <span>Mặt tiền (m)</span>
                                </label>
                                <input type="number" class="form-control form-control-lg" name="front_width" id="post-front-width" 
                                       placeholder="Ví dụ: 5" step="0.01" min="0">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-arrows-collapse text-primary"></i>
                                    <span>Chiều sâu (m)</span>
                                </label>
                                <input type="number" class="form-control form-control-lg" name="depth" id="post-depth" 
                                       placeholder="Ví dụ: 40" step="0.01" min="0">
                            </div>

                            <!-- Pháp lý và Đường -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-check text-primary"></i>
                                    <span>Tình trạng pháp lý</span>
                                </label>
                                <select class="form-select form-control-lg" name="legal_status" id="post-legal-status">
                                    <option value="">Chọn tình trạng pháp lý</option>
                                    <option value="Sổ đỏ">Sổ đỏ</option>
                                    <option value="Sổ hồng">Sổ hồng</option>
                                    <option value="Đang làm sổ">Đang làm sổ</option>
                                    <option value="Giấy tờ khác">Giấy tờ khác</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-road text-primary"></i>
                                    <span>Loại đường</span>
                                </label>
                                <select class="form-select form-control-lg" name="road_type" id="post-road-type">
                                    <option value="">Chọn loại đường</option>
                                    <option value="Ô tô">Ô tô</option>
                                    <option value="Hẻm">Hẻm</option>
                                    <option value="Đường đất">Đường đất</option>
                                </select>
                            </div>

                            <!-- Độ rộng đường và Hướng -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-arrows-angle-expand text-primary"></i>
                                    <span>Độ rộng đường (m)</span>
                                </label>
                                <input type="number" class="form-control form-control-lg" name="road_width" id="post-road-width" 
                                       placeholder="Ví dụ: 6" step="0.01" min="0">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-compass text-primary"></i>
                                    <span>Hướng</span>
                                </label>
                                <select class="form-select form-control-lg" name="direction" id="post-direction">
                                    <option value="">Chọn hướng</option>
                                    <option value="Đông">Đông</option>
                                    <option value="Tây">Tây</option>
                                    <option value="Nam">Nam</option>
                                    <option value="Bắc">Bắc</option>
                                    <option value="Đông Nam">Đông Nam</option>
                                    <option value="Đông Bắc">Đông Bắc</option>
                                    <option value="Tây Nam">Tây Nam</option>
                                    <option value="Tây Bắc">Tây Bắc</option>
                                </select>
                            </div>

                            <!-- Đường ô tô vào -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="has_road_access" id="post-has-road" value="1">
                                    <label class="form-check-label" for="post-has-road">
                                        <i class="bi bi-check-circle text-primary"></i> Có đường ô tô vào
                                    </label>
                                </div>
                            </div>

                            <!-- Thông tin liên hệ -->
                            <div class="col-12">
                                <hr class="my-3">
                                <h6 class="fw-bold mb-3">Thông tin liên hệ</h6>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-person text-primary"></i>
                                    <span>Tên người liên hệ *</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" name="contact_name" id="post-contact-name" 
                                       value="{{ auth('partner')->user()?->name }}" placeholder="Tên của bạn" required maxlength="255">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-telephone text-primary"></i>
                                    <span>Số điện thoại *</span>
                                </label>
                                <input type="tel" class="form-control form-control-lg" name="contact_phone" id="post-phone"
                                       value="{{ auth('partner')->user()?->phone }}" placeholder="09xx xxx xxx" required maxlength="20">
                                <small class="text-muted">Để người mua liên hệ với bạn</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold d-flex align-items-center gap-2">
                                    <i class="bi bi-chat-dots text-primary"></i>
                                    <span>Zalo (tùy chọn)</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" name="contact_zalo" id="post-zalo" 
                                       placeholder="Số điện thoại Zalo" maxlength="255">
                            </div>

                            <!-- Ảnh -->
                            <div class="col-12">
                                <hr class="my-3">
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
            @else
            <div class="modal-body text-center py-5">
                <i class="bi bi-lock-fill text-muted" style="font-size: 64px;"></i>
                <h5 class="mt-3 mb-2">Vui lòng đăng nhập</h5>
                <p class="text-muted mb-4">Bạn cần đăng nhập để đăng tin rao bán</p>
                <button type="button" class="btn btn-primary" data-bs-target="#loginModal" data-bs-toggle="modal" data-bs-dismiss="modal">
                    <i class="bi bi-box-arrow-in-right"></i> Đăng nhập ngay
                </button>
                <div class="mt-3">
                    <small class="text-muted">Chưa có tài khoản? </small>
                    <button type="button" class="btn btn-link p-0 small" data-bs-target="#registerModal" data-bs-toggle="modal" data-bs-dismiss="modal">
                        Đăng ký ngay
                    </button>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
            @endauth
        </div>
    </div>
</div>



