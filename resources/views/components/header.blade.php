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

    <form class="d-none d-md-flex col-5" action="{{ route('search') }}" method="GET">
        <div class="input-group search-box">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search"></i>
            </span>
            <input
                class="form-control border-start-0"
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Tìm địa chỉ, từ khóa, sổ đỏ, tên chủ đất...">
        </div>
    </form>

    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-outline-secondary d-none d-md-inline-flex" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise"></i> Làm mới
        </button>

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

