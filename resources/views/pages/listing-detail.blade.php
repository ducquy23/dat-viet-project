@extends('layouts.app')

@section('title', $listing->title ?? 'Chi tiết tin đăng')
@section('description', $listing->meta_description ?? $listing->description ?? 'Xem chi tiết tin đăng bất động sản')

@section('content')
@if(isset($listing) && $listing)
<div class="container py-4">
  <div class="row">
    <!-- Nội dung chính -->
    <div class="col-lg-8">
      <!-- Gallery ảnh -->
      @if($listing->images && $listing->images->count() > 0)
      <div class="card mb-4">
        <div class="card-body p-0">
          <div id="listingGallery" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              @foreach($listing->images as $index => $image)
              <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ $image->image_url }}" class="d-block w-100" style="height: 500px; object-fit: cover;" alt="{{ $listing->title }}">
              </div>
              @endforeach
            </div>
            @if($listing->images->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#listingGallery" data-bs-slide="prev">
              <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#listingGallery" data-bs-slide="next">
              <span class="carousel-control-next-icon"></span>
            </button>
            @endif
          </div>
        </div>
      </div>
      @endif

      <!-- Tiêu đề và giá -->
      <div class="card mb-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="flex-grow-1">
              <h1 class="h3 fw-bold mb-2">{{ $listing->title }}</h1>
              <div class="d-flex align-items-center gap-3 mb-2">
                <span class="h4 text-primary fw-bold mb-0">{{ number_format($listing->price) }} triệu đồng</span>
                @if($listing->price_per_m2)
                <span class="text-muted">({{ number_format($listing->price_per_m2, 1) }} triệu/m²)</span>
                @endif
              </div>
              <div class="d-flex align-items-center gap-2 text-muted small">
                <i class="bi bi-geo-alt-fill"></i>
                <span>{{ $listing->address }}, {{ $listing->district?->name }}, {{ $listing->city?->name }}</span>
              </div>
            </div>
            <button class="btn btn-light btn-lg {{ isset($isFavorited) && $isFavorited ? 'active' : '' }}"
                    id="favorite-btn"
                    onclick="toggleFavorite({{ $listing->id }})"
                    title="Yêu thích">
              <i class="bi bi-heart{{ isset($isFavorited) && $isFavorited ? '-fill' : '' }}"></i>
            </button>
          </div>

          <!-- Tags -->
          @if($listing->tags && is_array($listing->tags) && count($listing->tags) > 0)
          <div class="d-flex flex-wrap gap-2">
            @foreach($listing->tags as $tag)
            <span class="badge bg-primary-subtle text-primary">{{ $tag }}</span>
            @endforeach
          </div>
          @endif
        </div>
      </div>

      <!-- Thông tin chi tiết -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin chi tiết</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Diện tích</span>
                <span class="fw-semibold">{{ number_format($listing->area, 1) }} m²</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Mặt tiền</span>
                <span class="fw-semibold">{{ $listing->front_width ? number_format($listing->front_width, 1) . ' m' : 'Đang cập nhật' }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Chiều sâu</span>
                <span class="fw-semibold">{{ $listing->depth ? number_format($listing->depth, 1) . ' m' : 'Đang cập nhật' }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Hướng</span>
                <span class="fw-semibold">{{ $listing->direction ?? 'Đang cập nhật' }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Pháp lý</span>
                <span class="fw-semibold">{{ $listing->legal_status ?? 'Đang cập nhật' }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Loại đường</span>
                <span class="fw-semibold">{{ $listing->road_type ?? 'Đang cập nhật' }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Độ rộng đường</span>
                <span class="fw-semibold">{{ $listing->road_width ? number_format($listing->road_width, 1) . ' m' : 'Đang cập nhật' }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Đường ô tô</span>
                <span class="fw-semibold">
                  @if($listing->has_road_access)
                    <i class="bi bi-check-circle-fill text-success"></i> Có
                  @else
                    <i class="bi bi-x-circle text-muted"></i> Không
                  @endif
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Danh mục</span>
                <span class="fw-semibold">{{ $listing->category->name }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Lượt xem</span>
                <span class="fw-semibold">{{ number_format($listing->views_count) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Mô tả -->
      @if($listing->description)
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-card-text"></i> Mô tả</h5>
        </div>
        <div class="card-body">
          <div class="text-muted" style="white-space: pre-line;">{{ $listing->description }}</div>
        </div>
      </div>
      @endif

      <!-- Quy hoạch -->
      @if($listing->planning_info)
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-map"></i> Thông tin quy hoạch</h5>
        </div>
        <div class="card-body">
          <div class="text-muted" style="white-space: pre-line;">{{ $listing->planning_info }}</div>
        </div>
      </div>
      @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <!-- Thông tin liên hệ -->
      <div class="card mb-4 sticky-top" style="top: 20px;">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="bi bi-person-circle"></i> Thông tin liên hệ</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="fw-bold mb-1">{{ $listing->contact_name }}</div>
            <div class="text-muted small">
              <i class="bi bi-telephone"></i> {{ $listing->contact_phone }}
            </div>
            @if($listing->contact_zalo)
            <div class="text-muted small">
              <i class="bi bi-chat-dots"></i> Zalo: {{ $listing->contact_zalo }}
            </div>
            @endif
          </div>

          <div class="d-grid gap-2">
            <a href="tel:{{ $listing->contact_phone }}" class="btn btn-primary">
              <i class="bi bi-telephone-fill"></i> Gọi điện ngay
            </a>
            @if($listing->contact_zalo)
            <a href="https://zalo.me/{{ $listing->contact_zalo }}" target="_blank" class="btn btn-outline-primary">
              <i class="bi bi-chat-dots-fill"></i> Chat Zalo
            </a>
            @endif
            <button class="btn btn-outline-secondary" onclick="showContactForm({{ $listing->id }})">
              <i class="bi bi-envelope"></i> Gửi tin nhắn
            </button>
          </div>

          @if($listing->deposit_online)
          <div class="alert alert-info mt-3 mb-0 small">
            <i class="bi bi-info-circle"></i> Có thể đặt cọc online
          </div>
          @endif
        </div>
      </div>

      <!-- Bản đồ nhỏ -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Vị trí</h5>
        </div>
        <div class="card-body p-0">
          <div id="detailMap" style="height: 300px; border-radius: 0 0 0.375rem 0.375rem;"></div>
        </div>
      </div>

      <!-- Tin liên quan -->
      @if(isset($relatedListings) && $relatedListings->count() > 0)
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="bi bi-grid"></i> Tin liên quan</h5>
        </div>
        <div class="card-body">
          @foreach($relatedListings as $related)
          <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
            <a href="{{ route('listings.show', $related->slug) }}">
              <img src="{{ $related->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}"
                   alt="{{ $related->title }}"
                   style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
            </a>
            <div class="flex-grow-1">
              <a href="{{ route('listings.show', $related->slug) }}" class="text-decoration-none">
                <h6 class="mb-1">{{ Str::limit($related->title, 50) }}</h6>
              </a>
              <div class="text-primary fw-bold mb-1">{{ number_format($related->price) }} triệu</div>
              <div class="text-muted small">{{ $related->area }}m² • {{ $related->category->name }}</div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@else
<div class="container py-5">
  <div class="text-center">
    <h3>Không tìm thấy tin đăng</h3>
    <p class="text-muted">Tin đăng này không tồn tại hoặc đã bị xóa.</p>
    <a href="{{ route('home') }}" class="btn btn-primary">Về trang chủ</a>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  // Initialize detail map
  @if(isset($listing) && $listing)
  const detailMap = L.map('detailMap').setView([{{ $listing->latitude }}, {{ $listing->longitude }}], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(detailMap);

  L.marker([{{ $listing->latitude }}, {{ $listing->longitude }}])
    .addTo(detailMap)
    .bindPopup('{{ $listing->address }}');
  @endif

  // Toggle favorite
  function toggleFavorite(listingId) {
    fetch(`/api/listings/${listingId}/favorite`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(response => response.json())
    .then(data => {
      const btn = document.getElementById('favorite-btn');
      const icon = btn.querySelector('i');
      if (data.favorited) {
        btn.classList.add('active');
        icon.className = 'bi bi-heart-fill';
      } else {
        btn.classList.remove('active');
        icon.className = 'bi bi-heart';
      }
    })
    .catch(error => {
      if (error.status === 401) {
        Swal.fire({
          icon: 'warning',
          title: 'Yêu cầu đăng nhập',
          text: 'Vui lòng đăng nhập để sử dụng tính năng này',
          confirmButtonText: 'Đã hiểu'
        });
      }
    });
  }

  // Show contact form
  function showContactForm(listingId) {
    Swal.fire({
      title: 'Liên hệ với người đăng',
      html: `
        <input id="swal-name" class="swal2-input" placeholder="Tên của bạn" required>
        <input id="swal-phone" class="swal2-input" type="tel" placeholder="Số điện thoại" required>
        <textarea id="swal-message" class="swal2-textarea" placeholder="Tin nhắn (tùy chọn)"></textarea>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Gửi',
      cancelButtonText: 'Hủy',
      preConfirm: () => {
        const name = document.getElementById('swal-name').value;
        const phone = document.getElementById('swal-phone').value;
        const message = document.getElementById('swal-message').value || '';
        
        if (!name || !phone) {
          Swal.showValidationMessage('Vui lòng điền đầy đủ thông tin');
          return false;
        }
        
        return { name, phone, message };
      }
    }).then((result) => {
      if (result.isConfirmed && result.value) {
        fetch(`/api/listings/${listingId}/contact`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify(result.value)
        })
        .then(response => response.json())
        .then(data => {
          Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: data.message || 'Gửi liên hệ thành công!',
            confirmButtonText: 'Đồng ý'
          });
        })
        .catch(error => {
          Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Có lỗi xảy ra, vui lòng thử lại',
            confirmButtonText: 'Đã hiểu'
          });
        });
      }
    });
  }
</script>
@endpush
