<!-- Modal Đăng nhập -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered auth-modal">
        <div class="modal-content">
            <div class="auth-hero">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Đất Việt" height="40">
                    <div>
                        <div class="fw-bold text-white mb-0">Đăng nhập</div>
                        <small class="text-white-50">Tiếp tục cùng Đất Việt</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="auth-tabs">
                <button class="tab-btn active">Đăng nhập</button>
                <button class="tab-btn" data-bs-target="#registerModal" data-bs-toggle="modal" data-bs-dismiss="modal">Đăng ký</button>
            </div>

            <div class="modal-body">
                <form action="{{ route('partner.login.submit') }}" method="POST" id="loginForm">
                    @csrf
                    <div id="loginErrors" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="nhap@example.com" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu</label>
                        <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Nhớ đăng nhập</label>
                        </div>
                        <a href="#" class="small text-primary">Quên mật khẩu?</a>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-2" id="loginSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                        <span class="btn-text">Đăng nhập</span>
                    </button>
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-target="#registerModal" data-bs-toggle="modal" data-bs-dismiss="modal">Tạo tài khoản</button>
                    <div class="auth-divider"><span>hoặc</span></div>
                    <div class="d-grid gap-2">
                        <a href="" class="btn btn-outline-secondary social-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                            </svg>Đăng nhập với Google
                        </a>
                        <a href="" class="btn btn-outline-primary social-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"></path>
                            </svg>Đăng nhập với Facebook
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Xử lý submit form login qua AJAX
    document.getElementById('loginForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('loginSubmitBtn');
        const spinner = submitBtn.querySelector('.spinner-border');
        const btnText = submitBtn.querySelector('.btn-text');
        const errorDiv = document.getElementById('loginErrors');
        const formData = new FormData(form);
        
        // Reset errors
        errorDiv.classList.add('d-none');
        errorDiv.innerHTML = '';
        
        // Disable button và hiển thị spinner
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Đang đăng nhập...';
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            return response.json().then(data => {
                throw {status: response.status, errors: data.errors || data};
            });
        })
        .then(data => {
            if (data.success) {
                // Đóng modal
                const modalElement = document.getElementById('loginModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
                
                // Reload page để cập nhật header
                window.location.reload();
            }
        })
        .catch(error => {
            // Hiển thị errors
            let errorHtml = '<ul class="mb-0">';
            if (error.errors) {
                Object.keys(error.errors).forEach(key => {
                    error.errors[key].forEach(msg => {
                        errorHtml += '<li>' + msg + '</li>';
                    });
                });
            } else if (error.message) {
                errorHtml += '<li>' + error.message + '</li>';
            } else {
                errorHtml += '<li>Có lỗi xảy ra, vui lòng thử lại</li>';
            }
            errorHtml += '</ul>';
            errorDiv.innerHTML = errorHtml;
            errorDiv.classList.remove('d-none');
            
            // Enable button lại
            submitBtn.disabled = false;
            spinner.classList.add('d-none');
            btnText.textContent = 'Đăng nhập';
        });
    });
</script>
@endpush

