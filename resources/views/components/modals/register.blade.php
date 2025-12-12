<!-- Modal Đăng ký -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered auth-modal">
        <div class="modal-content">
            <div class="auth-hero">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Đất Việt" height="40">
                    <div>
                        <div class="fw-bold text-white mb-0">Đăng ký</div>
                        <small class="text-white-50">Tạo tài khoản mới</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="auth-tabs">
                <button class="tab-btn" data-bs-target="#loginModal" data-bs-toggle="modal" data-bs-dismiss="modal">Đăng nhập</button>
                <button class="tab-btn active">Đăng ký</button>
            </div>

            <div class="modal-body">
                <form action="{{ route('partner.register.submit') }}" method="POST" id="registerForm">
                    @csrf
                    <div id="registerErrors" class="alert alert-danger d-none"></div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username *</label>
                        <input type="text" 
                               class="form-control @error('username') is-invalid @enderror" 
                               name="username" 
                               id="register-username"
                               value="{{ old('username') }}" 
                               placeholder="Nhập username của bạn" 
                               required 
                               autofocus>
                        @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email *</label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               name="email" 
                               id="register-email"
                               value="{{ old('email') }}" 
                               placeholder="Enter your email" 
                               required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mật khẩu *</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" 
                                   id="register-password"
                                   placeholder="••••••••" 
                                   required
                                   minlength="8">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <small class="text-muted d-flex align-items-center gap-1 mt-1">
                            <i class="bi bi-check-circle text-success"></i>
                            Phải có ít nhất 8 ký tự
                        </small>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Điền lại mật khẩu *</label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   name="password_confirmation" 
                                   id="register-password-confirm"
                                   placeholder="••••••••" 
                                   required
                                   minlength="8">
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                <i class="bi bi-eye" id="passwordConfirmIcon"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="agree_terms" id="agreeTerms" required>
                            <label class="form-check-label small" for="agreeTerms">
                                Tôi đồng ý với <a href="#" class="text-primary">Điều khoản sử dụng</a>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-2" id="registerSubmitBtn">
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                        <span class="btn-text">Đăng ký</span>
                    </button>
                    
                    <button type="button" class="btn btn-outline-primary w-100" data-bs-target="#loginModal" data-bs-toggle="modal" data-bs-dismiss="modal">
                        Đã có tài khoản? Đăng nhập
                    </button>
                    
                    <div class="auth-divider"><span>hoặc</span></div>
                    
                    <div class="d-grid gap-2">
                        <a href="" class="btn btn-outline-secondary social-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                            </svg>
                            Đăng ký với Google
                        </a>
                        <a href="" class="btn btn-outline-primary social-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"></path>
                            </svg>
                            Đăng ký với Facebook
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('register-password');
        const icon = document.getElementById('passwordIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    document.getElementById('togglePasswordConfirm')?.addEventListener('click', function() {
        const passwordInput = document.getElementById('register-password-confirm');
        const icon = document.getElementById('passwordConfirmIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    // Handle form submit via AJAX
    document.getElementById('registerForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('registerSubmitBtn');
        const spinner = submitBtn.querySelector('.spinner-border');
        const btnText = submitBtn.querySelector('.btn-text');
        const errorDiv = document.getElementById('registerErrors');
        const formData = new FormData(form);
        
        // Reset errors
        errorDiv.classList.add('d-none');
        errorDiv.innerHTML = '';
        
        // Disable button và hiển thị spinner
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        btnText.textContent = 'Đang đăng ký...';
        
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
                const modalElement = document.getElementById('registerModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
                
                // Show success message
                alert('Đăng ký thành công! Bạn có thể đăng nhập ngay.');
                
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
            btnText.textContent = 'Đăng ký';
        });
    });

    // Set redirect after login
    window.setRedirectAfterLogin = function(modalId) {
        sessionStorage.setItem('redirectAfterLogin', modalId);
    };
</script>
@endpush
