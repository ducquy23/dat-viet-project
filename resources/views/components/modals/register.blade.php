<!-- Modal Đăng ký - CHỈ 1-2 BƯỚC -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered auth-modal">
        <div class="modal-content">
            <div class="auth-hero">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Đất Việt" height="40">
                    <div>
                        <div class="fw-bold text-white mb-0">Đăng ký nhanh</div>
                        <small class="text-white-50">Chỉ cần 1-2 bước</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="auth-tabs">
                <button class="tab-btn" data-bs-target="#loginModal" data-bs-toggle="modal" data-bs-dismiss="modal">Đăng nhập</button>
                <button class="tab-btn active">Đăng ký</button>
            </div>

            <div class="modal-body">
                <!-- Phương thức đăng ký nhanh -->
                <div class="text-center mb-4">
                    <h6 class="mb-3">Chọn cách đăng ký</h6>
                    <div class="d-grid gap-2 mb-3">
                        <a href="" class="btn btn-outline-secondary social-btn btn-lg">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                            </svg>
                            Đăng ký với Google (1 bước)
                        </a>
                        <a href="" class="btn btn-outline-primary social-btn btn-lg">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"></path>
                            </svg>
                            Đăng ký với Facebook (1 bước)
                        </a>
                    </div>
                    <div class="auth-divider"><span>hoặc</span></div>
                </div>

                <!-- Form đăng ký bằng số điện thoại (2 bước) -->
                <form action="" method="POST" id="register-phone-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số điện thoại *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="tel" class="form-control" name="phone" id="register-phone" placeholder="09xx xxx xxx" required>
                        </div>
                        <small class="text-muted">Chúng tôi sẽ gửi mã OTP để xác minh</small>
                    </div>

                    <div class="mb-3" id="otp-section" style="display: none;">
                        <label class="form-label fw-semibold">Mã OTP *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                            <input type="text" class="form-control" name="otp" id="register-otp" placeholder="Nhập mã 6 số" maxlength="6">
                            <button class="btn btn-outline-secondary" type="button" id="btn-resend-otp">Gửi lại</button>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mb-2" id="btn-send-otp">
                        <i class="bi bi-send"></i> Gửi mã OTP
                    </button>
                    <button type="submit" class="btn btn-primary w-100 mb-2" id="btn-verify-otp" style="display: none;">
                        <i class="bi bi-check-circle"></i> Xác minh & Đăng ký
                    </button>

                    <button type="button" class="btn btn-outline-primary w-100" data-bs-target="#loginModal" data-bs-toggle="modal" data-bs-dismiss="modal">
                        Đã có tài khoản? Đăng nhập
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // OTP flow
    document.getElementById('btn-send-otp')?.addEventListener('click', function() {
        const phone = document.getElementById('register-phone').value;
        if (!phone || phone.length < 10) {
            alert('Vui lòng nhập số điện thoại hợp lệ');
            return;
        }

        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ phone })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('otp-section').style.display = 'block';
                document.getElementById('btn-send-otp').style.display = 'none';
                document.getElementById('btn-verify-otp').style.display = 'block';
                alert('Mã OTP đã được gửi đến số điện thoại của bạn');
            } else {
                alert(data.message || 'Có lỗi xảy ra');
            }
        });
    });
</script>
@endpush

