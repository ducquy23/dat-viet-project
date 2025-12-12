@extends('layouts.app')

@section('title', 'Tin đăng của tôi')
@section('description', 'Quản lý tin đăng của bạn')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-12">
      <!-- Header Section -->
      <div class="dashboard-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div>
            <h1 class="dashboard-title mb-2">
              <i class="bi bi-house-gear text-primary"></i>
              Quản lý tin đăng
            </h1>
            <p class="text-muted mb-0">Quản lý và theo dõi các tin đăng của bạn</p>
          </div>
          <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#postModal">
            <i class="bi bi-plus-circle-fill"></i> Đăng tin mới
          </button>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="dashboard-tabs mb-4">
        <ul class="nav nav-pills" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link {{ ($activeTab ?? 'my-listings') === 'my-listings' ? 'active' : '' }}" 
               href="{{ route('listings.my-listings', ['tab' => 'my-listings']) }}">
              <i class="bi bi-house-door-fill"></i>
              <span>Tin đã đăng</span>
              @if(($activeTab ?? 'my-listings') === 'my-listings' && isset($listings) && $listings->total() > 0)
                <span class="badge bg-light text-primary ms-2">{{ $listings->total() }}</span>
              @endif
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link {{ ($activeTab ?? '') === 'favorites' ? 'active' : '' }}" 
               href="{{ route('listings.my-listings', ['tab' => 'favorites']) }}">
              <i class="bi bi-heart-fill"></i>
              <span>Tin yêu thích</span>
              @if(($activeTab ?? '') === 'favorites' && isset($listings) && $listings->total() > 0)
                <span class="badge bg-light text-primary ms-2">{{ $listings->total() }}</span>
              @endif
            </a>
          </li>
        </ul>
      </div>

      <!-- Tab Content -->
      <div class="tab-content">
        @if(($activeTab ?? 'my-listings') === 'favorites')
          <!-- Favorites Tab -->
          @if($listings->count() > 0)
            <div class="listings-table-card">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-header">
                    <tr>
                      <th class="col-image">Ảnh</th>
                      <th class="col-title">Tiêu đề</th>
                      <th class="col-price">Giá</th>
                      <th class="col-area">Diện tích</th>
                      <th class="col-status">Trạng thái</th>
                      <th class="col-date">Ngày yêu thích</th>
                      <th class="col-actions">Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($listings as $listing)
                    <tr class="table-row">
                      <td>
                        <div class="listing-image-wrapper">
                          <img src="{{ $listing->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}"
                               alt="{{ $listing->title }}"
                               class="listing-thumbnail">
                          @if($listing->isVip())
                            <span class="vip-badge-table">
                              <i class="bi bi-star-fill"></i> VIP
                            </span>
                          @endif
                        </div>
                      </td>
                      <td>
                        <div class="listing-info">
                          <h6 class="listing-title mb-1">{{ Str::limit($listing->title, 60) }}</h6>
                          <div class="listing-address">
                            <i class="bi bi-geo-alt"></i>
                            <span>{{ Str::limit($listing->address, 50) }}</span>
                          </div>
                          <div class="listing-category mt-1">
                            <span class="badge bg-light text-dark">{{ $listing->category->name }}</span>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="price-cell">
                          <span class="price-value">{{ number_format($listing->price / 1000000) }} triệu</span>
                          @if($listing->price_per_m2)
                            <small class="price-per-m2 d-block text-muted">
                              {{ number_format($listing->price_per_m2 / 1000000, 1) }} tr/m²
                            </small>
                          @endif
                        </div>
                      </td>
                      <td>
                        <div class="area-cell">
                          <i class="bi bi-rulers text-primary"></i>
                          <span>{{ number_format($listing->area, 1) }} m²</span>
                        </div>
                      </td>
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
                        <span class="badge status-badge bg-{{ $statusColors[$listing->status] ?? 'secondary' }}">
                          {{ $statusLabels[$listing->status] ?? $listing->status }}
                        </span>
                      </td>
                      <td>
                        @php
                          $favorite = \App\Models\Favorite::where('user_id', auth('partner')->id())
                            ->where('listing_id', $listing->id)
                            ->first();
                        @endphp
                        <div class="date-cell">
                          <i class="bi bi-calendar3"></i>
                          <span>{{ $favorite ? $favorite->created_at->format('d/m/Y') : 'N/A' }}</span>
                        </div>
                      </td>
                      <td>
                        <div class="action-buttons">
                          <a href="{{ route('listings.show', $listing->slug) }}" 
                             class="btn btn-sm btn-outline-primary" 
                             title="Xem chi tiết">
                            <i class="bi bi-eye"></i>
                          </a>
                          <button class="btn btn-sm btn-outline-danger" 
                                  onclick="removeFavorite({{ $listing->id }})"
                                  title="Bỏ yêu thích">
                            <i class="bi bi-heart-fill"></i>
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
                <div class="table-pagination">
                  {{ $listings->links() }}
                </div>
              @endif
            </div>
          @else
            <div class="empty-state-card">
              <div class="empty-state-icon">
                <i class="bi bi-heart"></i>
              </div>
              <h4 class="empty-state-title">Chưa có tin yêu thích</h4>
              <p class="empty-state-text">Bạn chưa lưu tin đăng nào vào danh sách yêu thích</p>
              <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="bi bi-search"></i> Tìm kiếm tin đăng
              </a>
            </div>
          @endif
        @else
          <!-- My Listings Tab -->
          @if($listings->count() > 0)
            <div class="listings-table-card">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-header">
                    <tr>
                      <th class="col-image">Ảnh</th>
                      <th class="col-title">Tiêu đề</th>
                      <th class="col-price">Giá</th>
                      <th class="col-area">Diện tích</th>
                      <th class="col-status">Trạng thái</th>
                      <th class="col-date">Ngày đăng</th>
                      <th class="col-actions">Thao tác</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($listings as $listing)
                    <tr class="table-row">
                      <td>
                        <div class="listing-image-wrapper">
                          <img src="{{ $listing->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}"
                               alt="{{ $listing->title }}"
                               class="listing-thumbnail">
                          @if($listing->isVip())
                            <span class="vip-badge-table">
                              <i class="bi bi-star-fill"></i> VIP
                            </span>
                          @endif
                        </div>
                      </td>
                      <td>
                        <div class="listing-info">
                          <h6 class="listing-title mb-1">{{ Str::limit($listing->title, 60) }}</h6>
                          <div class="listing-address">
                            <i class="bi bi-geo-alt"></i>
                            <span>{{ Str::limit($listing->address, 50) }}</span>
                          </div>
                          <div class="listing-category mt-1">
                            <span class="badge bg-light text-dark">{{ $listing->category->name }}</span>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="price-cell">
                          <span class="price-value">{{ number_format($listing->price / 1000000) }} triệu</span>
                          @if($listing->price_per_m2)
                            <small class="price-per-m2 d-block text-muted">
                              {{ number_format($listing->price_per_m2 / 1000000, 1) }} tr/m²
                            </small>
                          @endif
                        </div>
                      </td>
                      <td>
                        <div class="area-cell">
                          <i class="bi bi-rulers text-primary"></i>
                          <span>{{ number_format($listing->area, 1) }} m²</span>
                        </div>
                      </td>
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
                        <span class="badge status-badge bg-{{ $statusColors[$listing->status] ?? 'secondary' }}">
                          {{ $statusLabels[$listing->status] ?? $listing->status }}
                        </span>
                      </td>
                      <td>
                        <div class="date-cell">
                          <i class="bi bi-calendar3"></i>
                          <span>{{ $listing->created_at->format('d/m/Y') }}</span>
                        </div>
                      </td>
                      <td>
                        <div class="action-buttons">
                          <a href="{{ route('listings.show', $listing->slug) }}" 
                             class="btn btn-sm btn-outline-primary" 
                             title="Xem chi tiết">
                            <i class="bi bi-eye"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              @if($listings->total() > 12)
                <div class="table-pagination">
                  {{ $listings->links() }}
                </div>
              @endif
            </div>
          @else
            <div class="empty-state-card">
              <div class="empty-state-icon">
                <i class="bi bi-inbox"></i>
              </div>
              <h4 class="empty-state-title">Chưa có tin đăng</h4>
              <p class="empty-state-text">Bắt đầu đăng tin để quảng bá bất động sản của bạn</p>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal">
                <i class="bi bi-plus-circle-fill"></i> Đăng tin ngay
              </button>
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
/* Dashboard Header */
.dashboard-header {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fb 100%);
    padding: 24px;
    border-radius: var(--dv-radius-lg);
    box-shadow: var(--dv-shadow-md);
    border: 1px solid #e2e8f0;
}

.dashboard-title {
    font-size: 28px;
    font-weight: 800;
    color: #1a202c;
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
}

.dashboard-title i {
    font-size: 32px;
}

/* Dashboard Tabs */
.dashboard-tabs {
    background: white;
    padding: 8px;
    border-radius: var(--dv-radius-lg);
    box-shadow: var(--dv-shadow-sm);
    border: 1px solid #e2e8f0;
}

.dashboard-tabs .nav-pills {
    gap: 8px;
    border: none;
}

.dashboard-tabs .nav-link {
    color: #64748b;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: var(--dv-radius-md);
    transition: var(--dv-transition);
    display: flex;
    align-items: center;
    gap: 8px;
    border: 2px solid transparent;
    background: transparent;
}

.dashboard-tabs .nav-link:hover {
    color: var(--dv-primary);
    background: rgba(51, 87, 147, 0.05);
    border-color: rgba(51, 87, 147, 0.2);
}

.dashboard-tabs .nav-link.active {
    color: white;
    background: linear-gradient(135deg, var(--dv-primary) 0%, var(--dv-primary-dark) 100%);
    border-color: var(--dv-primary);
    box-shadow: 0 4px 12px rgba(51, 87, 147, 0.25);
}

.dashboard-tabs .nav-link i {
    font-size: 18px;
}

.dashboard-tabs .nav-link .badge {
    font-size: 11px;
    padding: 4px 8px;
    border-radius: 12px;
}

/* Listings Table Card */
.listings-table-card {
    background: white;
    border-radius: var(--dv-radius-lg);
    box-shadow: var(--dv-shadow-md);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.table-header {
    background: linear-gradient(135deg, #f8f9fb 0%, #ffffff 100%);
    border-bottom: 2px solid #e2e8f0;
}

.table-header th {
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    padding: 16px;
    border: none;
}

.table-row {
    transition: var(--dv-transition);
    border-bottom: 1px solid #f1f5f9;
}

.table-row:hover {
    background: #f8f9fb;
    transform: translateX(4px);
}

.table-row:last-child {
    border-bottom: none;
}

.table-row td {
    padding: 16px;
    vertical-align: middle;
}

/* Listing Image */
.listing-image-wrapper {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: var(--dv-radius-md);
    overflow: hidden;
    background: #f1f5f9;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.listing-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--dv-transition);
}

.table-row:hover .listing-thumbnail {
    transform: scale(1.1);
}

.vip-badge-table {
    position: absolute;
    top: 6px;
    right: 6px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(251, 191, 36, 0.4);
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Listing Info */
.listing-info {
    min-width: 250px;
}

.listing-title {
    font-size: 15px;
    font-weight: 700;
    color: #1a202c;
    line-height: 1.4;
    margin: 0;
}

.listing-address {
    font-size: 13px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 4px;
}

.listing-address i {
    font-size: 12px;
    color: var(--dv-primary);
}

.listing-category {
    margin-top: 6px;
}

/* Price Cell */
.price-cell {
    text-align: left;
}

.price-value {
    font-size: 16px;
    font-weight: 800;
    color: var(--dv-primary);
    display: block;
}

.price-per-m2 {
    font-size: 12px;
    margin-top: 4px;
}

/* Area Cell */
.area-cell {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: #1a202c;
}

.area-cell i {
    font-size: 16px;
}

/* Status Badge */
.status-badge {
    padding: 6px 12px;
    font-weight: 600;
    font-size: 12px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Date Cell */
.date-cell {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #64748b;
}

.date-cell i {
    font-size: 14px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 8px;
}

.action-buttons .btn {
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: var(--dv-transition);
}

.action-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.action-buttons .btn-outline-primary:hover {
    background: var(--dv-primary);
    color: white;
    border-color: var(--dv-primary);
}

.action-buttons .btn-outline-danger:hover {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

/* Table Pagination */
.table-pagination {
    padding: 20px;
    border-top: 1px solid #e2e8f0;
    background: #f8f9fb;
}

/* Empty State */
.empty-state-card {
    background: white;
    border-radius: var(--dv-radius-lg);
    box-shadow: var(--dv-shadow-md);
    border: 1px solid #e2e8f0;
    padding: 80px 40px;
    text-align: center;
}

.empty-state-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 24px;
    background: linear-gradient(135deg, #f8f9fb 0%, #ffffff 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid #e2e8f0;
}

.empty-state-icon i {
    font-size: 64px;
    color: #cbd5e1;
}

.empty-state-title {
    font-size: 24px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 12px;
}

.empty-state-text {
    font-size: 16px;
    color: #64748b;
    margin-bottom: 24px;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-title {
        font-size: 22px;
    }
    
    .dashboard-tabs .nav-link {
        padding: 10px 16px;
        font-size: 14px;
    }
    
    .table-header th,
    .table-row td {
        padding: 12px 8px;
        font-size: 13px;
    }
    
    .listing-image-wrapper {
        width: 80px;
        height: 80px;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 4px;
    }
    
    .action-buttons .btn {
        width: 100%;
    }
    
    .col-image,
    .col-title,
    .col-price,
    .col-area,
    .col-status,
    .col-date,
    .col-actions {
        min-width: auto;
    }
}
</style>
@endpush

@push('scripts')
<script>
function removeFavorite(listingId) {
    Swal.fire({
        title: 'Xác nhận',
        text: 'Bạn có chắc muốn bỏ yêu thích tin đăng này?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Có, bỏ yêu thích',
        cancelButtonText: 'Hủy',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: 'Đã bỏ yêu thích tin đăng',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
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
    });
}
</script>
@endpush
