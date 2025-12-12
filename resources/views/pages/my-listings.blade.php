@extends('layouts.app')

@section('title', 'Tin đăng của tôi')
@section('description', 'Quản lý tin đăng của bạn')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Quản lý tin đăng</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal">
          <i class="bi bi-plus-circle"></i> Đăng tin mới
        </button>
      </div>

      <!-- Tabs Navigation -->
      <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
          <a class="nav-link {{ ($activeTab ?? 'my-listings') === 'my-listings' ? 'active' : '' }}" 
             href="{{ route('listings.my-listings', ['tab' => 'my-listings']) }}">
            <i class="bi bi-house-door"></i> Tin đã đăng
          </a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link {{ ($activeTab ?? '') === 'favorites' ? 'active' : '' }}" 
             href="{{ route('listings.my-listings', ['tab' => 'favorites']) }}">
            <i class="bi bi-heart"></i> Tin yêu thích
          </a>
        </li>
      </ul>

      <!-- Tab Content -->
      <div class="tab-content">
        @if(($activeTab ?? 'my-listings') === 'favorites')
          <!-- Favorites Tab -->
          @if($listings->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Giá</th>
                    <th>Diện tích</th>
                    <th>Trạng thái</th>
                    <th>Ngày yêu thích</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($listings as $listing)
                  <tr>
                    <td>
                      <img src="{{ $listing->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}"
                           alt="{{ $listing->title }}"
                           style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                    </td>
                    <td>
                      <div class="fw-bold">{{ Str::limit($listing->title, 50) }}</div>
                      <div class="text-muted small">{{ $listing->address }}</div>
                    </td>
                    <td>{{ number_format($listing->price / 1000000) }} triệu</td>
                    <td>{{ number_format($listing->area, 1) }}m²</td>
                    <td>
                      @php
                        $statusColors = [
                          'pending' => 'warning',
                          'approved' => 'success',
                          'rejected' => 'danger',
                          'expired' => 'secondary',
                          'sold' => 'info',
                        ];
                        $statusLabels = [
                          'pending' => 'Chờ duyệt',
                          'approved' => 'Đã duyệt',
                          'rejected' => 'Từ chối',
                          'expired' => 'Hết hạn',
                          'sold' => 'Đã bán',
                        ];
                      @endphp
                      <span class="badge bg-{{ $statusColors[$listing->status] ?? 'secondary' }}">
                        {{ $statusLabels[$listing->status] ?? $listing->status }}
                      </span>
                    </td>
                    <td>
                      @php
                        $favorite = \App\Models\Favorite::where('user_id', auth('partner')->id())
                          ->where('listing_id', $listing->id)
                          ->first();
                      @endphp
                      {{ $favorite ? $favorite->created_at->format('d/m/Y') : 'N/A' }}
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="{{ route('listings.show', $listing->slug) }}" class="btn btn-sm btn-outline-primary">
                          <i class="bi bi-eye"></i> Xem
                        </a>
                        <button class="btn btn-sm btn-outline-danger" onclick="removeFavorite({{ $listing->id }})">
                          <i class="bi bi-heart-fill"></i> Bỏ yêu thích
                        </button>
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            @if($listings->total() > 12)
              <div class="mt-4">
                {{ $listings->links() }}
              </div>
            @endif
          @else
            <div class="card">
              <div class="card-body text-center py-5">
                <i class="bi bi-heart text-muted" style="font-size: 64px;"></i>
                <p class="text-muted mt-3">Bạn chưa có tin yêu thích nào</p>
                <a href="{{ route('home') }}" class="btn btn-primary">
                  <i class="bi bi-search"></i> Tìm kiếm tin đăng
                </a>
              </div>
            </div>
          @endif
        @else
          <!-- My Listings Tab -->
          @if($listings->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Giá</th>
                    <th>Diện tích</th>
                    <th>Trạng thái</th>
                    <th>Ngày đăng</th>
                    <th>Thao tác</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($listings as $listing)
                  <tr>
                    <td>
                      <img src="{{ $listing->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}"
                           alt="{{ $listing->title }}"
                           style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
                    </td>
                    <td>
                      <div class="fw-bold">{{ Str::limit($listing->title, 50) }}</div>
                      <div class="text-muted small">{{ $listing->address }}</div>
                    </td>
                    <td>{{ number_format($listing->price / 1000000) }} triệu</td>
                    <td>{{ number_format($listing->area, 1) }}m²</td>
                    <td>
                      @php
                        $statusColors = [
                          'pending' => 'warning',
                          'approved' => 'success',
                          'rejected' => 'danger',
                          'expired' => 'secondary',
                          'sold' => 'info',
                        ];
                        $statusLabels = [
                          'pending' => 'Chờ duyệt',
                          'approved' => 'Đã duyệt',
                          'rejected' => 'Từ chối',
                          'expired' => 'Hết hạn',
                          'sold' => 'Đã bán',
                        ];
                      @endphp
                      <span class="badge bg-{{ $statusColors[$listing->status] ?? 'secondary' }}">
                        {{ $statusLabels[$listing->status] ?? $listing->status }}
                      </span>
                    </td>
                    <td>{{ $listing->created_at->format('d/m/Y') }}</td>
                    <td>
                      <a href="{{ route('listings.show', $listing->slug) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> Xem
                      </a>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            @if($listings->total() > 12)
              <div class="mt-4">
                {{ $listings->links() }}
              </div>
            @endif
          @else
            <div class="card">
              <div class="card-body text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 64px;"></i>
                <p class="text-muted mt-3">Bạn chưa có tin đăng nào</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal">
                  <i class="bi bi-plus-circle"></i> Đăng tin ngay
                </button>
              </div>
            </div>
          @endif
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.nav-tabs {
    border-bottom: 2px solid #e2e8f0;
}

.nav-tabs .nav-link {
    color: #64748b;
    font-weight: 600;
    padding: 12px 24px;
    border: none;
    border-bottom: 3px solid transparent;
    transition: var(--dv-transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.nav-tabs .nav-link:hover {
    color: var(--dv-primary);
    border-bottom-color: rgba(51, 87, 147, 0.3);
}

.nav-tabs .nav-link.active {
    color: var(--dv-primary);
    background: transparent;
    border-bottom-color: var(--dv-primary);
}

.nav-tabs .nav-link i {
    font-size: 18px;
}
</style>
@endpush

@push('scripts')
<script>
function removeFavorite(listingId) {
    if (!confirm('Bạn có chắc muốn bỏ yêu thích tin đăng này?')) {
        return;
    }

    fetch(`/api/listings/${listingId}/favorite`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.favorited) {
            // Reload page to update list
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Có lỗi xảy ra, vui lòng thử lại',
            confirmButtonText: 'Đã hiểu'
        });
    });
}
</script>
@endpush
