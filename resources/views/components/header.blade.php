<!-- HEADER -->
<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 sticky-top top-bar">
    <div class="d-flex align-items-center gap-3">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Đất Việt" height="48">
            <div>
                <div class="fw-bold text-primary mb-0">Đất Việt</div>
                <small class="text-muted">Bản đồ bất động sản địa phương</small>
            </div>
        </a>
    </div>

    <form class="d-none d-md-flex col-5 position-relative" action="{{ route('search') }}" method="GET" id="header-search-form">
        <div class="input-group search-box position-relative">
            <span class="input-group-text bg-white border-end-0 search-icon-wrapper" style="padding: 12px 16px; cursor: pointer;" onclick="document.getElementById('header-search-form').submit();">
                <i class="bi bi-search search-icon-animated" style="color: #335793;"></i>
            </span>
            <input
                class="form-control border-start-0"
                type="text"
                name="q"
                id="header-search-input"
                value="{{ request('q') }}"
                placeholder="Tìm địa chỉ, từ khóa, sổ đỏ, tên chủ đất..."
                style="padding: 12px 16px; font-size: 14px;"
                aria-label="Tìm kiếm"
                autocomplete="off">
            @if(request('q'))
                <button type="button" class="btn btn-link text-muted p-0 position-absolute end-0 top-50 translate-middle-y me-2"
                        onclick="document.getElementById('header-search-input').value=''; document.getElementById('header-search-form').submit();"
                        style="text-decoration: none; z-index: 10;">
                    <i class="bi bi-x-circle"></i>
                </button>
            @endif
            <button type="submit" class="btn btn-primary" style="border-radius: 0 8px 8px 0; padding: 12px 20px;">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    @push('styles')
        <style>
            /* Search Icon Animation */
            .search-icon-wrapper {
                position: relative;
            }

            .search-icon-animated {
                font-size: 18px;
                color: #335793 !important;
                animation: searchPulse 2s ease-in-out infinite;
                transition: all 0.3s ease;
                display: inline-block;
            }

            .search-icon-wrapper:hover .search-icon-animated {
                color: #1e3a5f !important;
                transform: scale(1.15);
                animation: searchBounce 0.6s ease-in-out;
            }

            @keyframes searchPulse {
                0%, 100% {
                    opacity: 1;
                    transform: scale(1);
                }
                50% {
                    opacity: 0.8;
                    transform: scale(1.05);
                }
            }

            @keyframes searchBounce {
                0%, 100% {
                    transform: scale(1.15);
                }
                50% {
                    transform: scale(1.3);
                }
            }

            /* Search box focus effect */
            .search-box:focus-within .search-icon-animated {
                color: #1e3a5f !important;
                animation: searchBounce 0.6s ease-in-out;
            }
        </style>
    @endpush


    <div class="d-flex align-items-center gap-2">
        @auth('partner')
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> {{ Auth::guard('partner')->user()->name ?? Auth::guard('partner')->user()->phone }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('listings.my-listings') }}">
                            <i class="bi bi-house-door"></i> Tin của tôi
                        </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('partner.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right"></i> Đăng xuất
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                Đăng nhập
            </button>
        @endauth

        @auth('partner')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal">
                <i class="bi bi-plus-circle"></i> Đăng tin rao bán
            </button>
        @else
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal" onclick="setRedirectAfterLogin('postModal')">
                <i class="bi bi-plus-circle"></i> Đăng tin rao bán
            </button>
        @endauth
    </div>
</nav>

