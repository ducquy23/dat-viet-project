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
                        <button onclick="showListingDetail({{ $listing->id }})" 
                                class="btn btn-sm btn-outline-primary" 
                                title="Xem chi tiết">
                          <i class="bi bi-eye"></i>
                        </button>
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
                          <button onclick="showListingDetail({{ $listing->id }})" 
                                  class="btn btn-sm btn-outline-primary" 
                                  title="Xem chi tiết">
                            <i class="bi bi-eye"></i>
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

<!-- Listing Detail Modal -->
<div class="modal fade" id="listingDetailModal" tabindex="-1" aria-labelledby="listingDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <h5 class="modal-title fw-bold" id="listingDetailModalLabel">
          <i class="bi bi-info-circle-fill text-primary"></i>
          Chi tiết tin đăng
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="listingDetailContent">
        <div class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Đang tải...</span>
          </div>
          <p class="mt-3 text-muted">Đang tải thông tin...</p>
        </div>
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

/* Listing Detail Modal */
.listing-detail-modal {
    padding: 0;
}

.detail-gallery {
    margin-bottom: 24px;
}

.main-image-wrapper {
    position: relative;
    width: 100%;
    height: 400px;
    border-radius: var(--dv-radius-lg);
    overflow: hidden;
    background: #f1f5f9;
    box-shadow: var(--dv-shadow-md);
}

.detail-main-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.vip-badge-modal {
    position: absolute;
    top: 16px;
    right: 16px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
    display: flex;
    align-items: center;
    gap: 6px;
    z-index: 10;
}

.thumbnail-gallery {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding: 8px 0;
}

.thumbnail-item {
    flex-shrink: 0;
    width: 100px;
    height: 100px;
    border-radius: var(--dv-radius-sm);
    overflow: hidden;
    cursor: pointer;
    border: 3px solid transparent;
    transition: var(--dv-transition);
    opacity: 0.6;
}

.thumbnail-item:hover,
.thumbnail-item.active {
    opacity: 1;
    border-color: var(--dv-primary);
    transform: scale(1.05);
}

.thumbnail-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.detail-header {
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 20px;
}

.detail-title {
    font-size: 24px;
    font-weight: 800;
    color: #1a202c;
    line-height: 1.3;
}

.detail-price-section {
    display: flex;
    align-items: baseline;
    gap: 12px;
    flex-wrap: wrap;
}

.detail-price-main {
    font-size: 28px;
    font-weight: 800;
    color: var(--dv-primary);
}

.detail-price-per {
    font-size: 18px;
    color: #64748b;
    font-weight: 600;
}

.detail-location {
    font-size: 16px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-meta {
    font-size: 14px;
}

.status-badge-modal {
    padding: 6px 12px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-info-grid {
    background: #f8f9fb;
    padding: 20px;
    border-radius: var(--dv-radius-lg);
}

.info-item-card {
    background: white;
    padding: 16px;
    border-radius: var(--dv-radius-md);
    border: 1px solid #e2e8f0;
    transition: var(--dv-transition);
    height: 100%;
}

.info-item-card:hover {
    border-color: var(--dv-primary-light);
    box-shadow: var(--dv-shadow-sm);
    transform: translateY(-2px);
}

.info-item-label {
    font-size: 12px;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.info-item-value {
    font-size: 16px;
    font-weight: 700;
    color: #1a202c;
}

.detail-section-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.detail-description-text,
.detail-planning-text {
    font-size: 15px;
    line-height: 1.8;
    color: #475569;
    white-space: pre-line;
    background: #f8f9fb;
    padding: 20px;
    border-radius: var(--dv-radius-md);
    border: 1px solid #e2e8f0;
}

.detail-contact {
    background: linear-gradient(135deg, #f8f9fb 0%, #ffffff 100%);
    padding: 24px;
    border-radius: var(--dv-radius-lg);
    border: 1px solid #e2e8f0;
}

.contact-info-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 15px;
    color: #475569;
}

.contact-item i {
    font-size: 18px;
    color: var(--dv-primary);
    width: 24px;
}

.contact-item a {
    color: var(--dv-primary);
    font-weight: 600;
    text-decoration: none;
}

.contact-item a:hover {
    text-decoration: underline;
}

.contact-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.contact-actions .btn {
    flex: 1;
    min-width: 150px;
}

/* Modal Customization */
#listingDetailModal .modal-content {
    border-radius: var(--dv-radius-lg);
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

#listingDetailModal .modal-header {
    background: linear-gradient(135deg, #f8f9fb 0%, #ffffff 100%);
    border-bottom: 2px solid #e2e8f0;
    padding: 20px 24px;
}

#listingDetailModal .modal-body {
    padding: 24px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

#listingDetailModal .modal-title {
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
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
    
    .main-image-wrapper {
        height: 250px;
    }
    
    .detail-title {
        font-size: 20px;
    }
    
    .detail-price-main {
        font-size: 24px;
    }
    
    .contact-actions .btn {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
let listingDetailModal = null;

// Initialize modal
document.addEventListener('DOMContentLoaded', function() {
  const modalElement = document.getElementById('listingDetailModal');
  if (modalElement) {
    listingDetailModal = new bootstrap.Modal(modalElement);
  }
});

function showListingDetail(listingId) {
  const modalContent = document.getElementById('listingDetailContent');
  
  // Show loading state
  modalContent.innerHTML = `
    <div class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
      <p class="mt-3 text-muted">Đang tải thông tin...</p>
    </div>
  `;
  
  // Open modal
  if (listingDetailModal) {
    listingDetailModal.show();
  }
  
  // Load listing data
  fetch(`/api/listings/${listingId}`)
    .then(response => response.json())
    .then(data => {
      if (data.listing) {
        renderListingDetail(data.listing);
      } else {
        modalContent.innerHTML = `
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i>
            Không thể tải thông tin tin đăng
          </div>
        `;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      modalContent.innerHTML = `
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle"></i>
          Có lỗi xảy ra khi tải thông tin
        </div>
      `;
    });
}

function renderListingDetail(listing) {
  const modalContent = document.getElementById('listingDetailContent');
  
  // Format images
  let images = [];
  if (listing.images && listing.images.length > 0) {
    images = listing.images.map(img => {
      // Use image_url accessor if available, otherwise construct from image_path
      return img.image_url || (img.image_path && img.image_path.startsWith('http') 
        ? img.image_path 
        : `/storage/${img.image_path}`);
    });
  } else if (listing.primary_image) {
    images = [listing.primary_image.image_url || (listing.primary_image.startsWith('http') 
      ? listing.primary_image 
      : `/storage/${listing.primary_image}`)];
  } else {
    images = ['/images/Image-not-found.png'];
  }
  
  const mainImage = images[0] || '/images/Image-not-found.png';
  
  // Format price
  const price = listing.price ? (listing.price / 1000000).toLocaleString('vi-VN') : '0';
  const pricePerM2 = listing.price_per_m2 ? (listing.price_per_m2 / 1000000).toFixed(1) : null;
  
  // Format date
  const createdDate = listing.created_at ? new Date(listing.created_at).toLocaleDateString('vi-VN') : 'N/A';
  
  // Status badge
  const statusColors = {
    'pending': 'warning',
    'approved': 'success',
    'rejected': 'danger',
    'expired': 'secondary',
    'sold': 'info',
  };
  const statusLabels = {
    'pending': 'Chờ duyệt',
    'approved': 'Đã duyệt',
    'rejected': 'Từ chối',
    'expired': 'Hết hạn',
    'sold': 'Đã bán',
  };
  const statusColor = statusColors[listing.status] || 'secondary';
  const statusLabel = statusLabels[listing.status] || listing.status;
  
  // Check if VIP
  const isVip = listing.package && listing.package.code === 'vip';
  
  modalContent.innerHTML = `
    <div class="listing-detail-modal">
      <!-- Gallery -->
      <div class="detail-gallery mb-4">
        <div class="main-image-wrapper">
          <img src="${mainImage}" alt="${listing.title || 'N/A'}" id="detailMainImage" class="detail-main-image">
          ${isVip ? '<span class="vip-badge-modal"><i class="bi bi-star-fill"></i> VIP</span>' : ''}
        </div>
        ${images.length > 1 ? `
          <div class="thumbnail-gallery mt-3">
            ${images.slice(0, 5).map((img, idx) => `
              <div class="thumbnail-item ${idx === 0 ? 'active' : ''}" onclick="changeDetailImage('${img}', ${idx})">
                <img src="${img}" alt="Thumbnail ${idx + 1}">
              </div>
            `).join('')}
          </div>
        ` : ''}
      </div>
      
      <!-- Header Info -->
      <div class="detail-header mb-4">
        <h3 class="detail-title mb-3">${listing.title || 'N/A'}</h3>
        <div class="detail-price-section mb-3">
          <span class="detail-price-main">${price} triệu đồng</span>
          ${pricePerM2 ? `<span class="detail-price-per">(${pricePerM2} triệu/m²)</span>` : ''}
        </div>
        <div class="detail-location mb-3">
          <i class="bi bi-geo-alt-fill text-primary"></i>
          <span>${listing.address || 'N/A'}, ${listing.district?.name || ''}, ${listing.city?.name || ''}</span>
        </div>
        <div class="detail-meta d-flex flex-wrap gap-3 align-items-center">
          <span class="badge bg-${statusColor} status-badge-modal">${statusLabel}</span>
          <span class="text-muted"><i class="bi bi-calendar3"></i> Đăng: ${createdDate}</span>
          <span class="text-muted"><i class="bi bi-eye"></i> ${listing.views_count || 0} lượt xem</span>
          <span class="badge bg-light text-dark">${listing.category?.name || 'N/A'}</span>
        </div>
      </div>
      
      <!-- Details Grid -->
      <div class="detail-info-grid mb-4">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="info-item-card">
              <div class="info-item-label">
                <i class="bi bi-rulers"></i>
                <span>Diện tích</span>
              </div>
              <div class="info-item-value">${listing.area ? parseFloat(listing.area).toLocaleString('vi-VN') : 'N/A'} m²</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-item-card">
              <div class="info-item-label">
                <i class="bi bi-arrows-expand"></i>
                <span>Mặt tiền</span>
              </div>
              <div class="info-item-value">${listing.front_width ? parseFloat(listing.front_width).toLocaleString('vi-VN') + ' m' : 'Chưa cập nhật'}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-item-card">
              <div class="info-item-label">
                <i class="bi bi-arrows-angle-contract"></i>
                <span>Chiều sâu</span>
              </div>
              <div class="info-item-value">${listing.depth ? parseFloat(listing.depth).toLocaleString('vi-VN') + ' m' : 'Chưa cập nhật'}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-item-card">
              <div class="info-item-label">
                <i class="bi bi-compass"></i>
                <span>Hướng</span>
              </div>
              <div class="info-item-value">${listing.direction || 'Chưa cập nhật'}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-item-card">
              <div class="info-item-label">
                <i class="bi bi-file-earmark-check"></i>
                <span>Tình trạng pháp lý</span>
              </div>
              <div class="info-item-value">${listing.legal_status || 'Chưa cập nhật'}</div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="info-item-card">
              <div class="info-item-label">
                <i class="bi bi-road"></i>
                <span>Loại đường</span>
              </div>
              <div class="info-item-value">${listing.road_type || 'Chưa cập nhật'}</div>
            </div>
          </div>
          ${listing.road_width ? `
          <div class="col-md-6">
            <div class="info-item-card">
              <div class="info-item-label">
                <i class="bi bi-arrows-angle-expand"></i>
                <span>Độ rộng đường</span>
              </div>
              <div class="info-item-value">${parseFloat(listing.road_width).toLocaleString('vi-VN')} m</div>
            </div>
          </div>
          ` : ''}
          <div class="col-md-6">
            <div class="info-item-card">
              <div class="info-item-label">
                <i class="bi bi-car-front"></i>
                <span>Đường ô tô vào</span>
              </div>
              <div class="info-item-value">
                ${listing.has_road_access 
                  ? '<span class="badge bg-success-subtle text-success"><i class="bi bi-check-circle-fill"></i> Có</span>'
                  : '<span class="text-muted">Không</span>'}
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Description -->
      ${listing.description ? `
      <div class="detail-description mb-4">
        <h5 class="detail-section-title">
          <i class="bi bi-card-text-fill text-primary"></i>
          Mô tả chi tiết
        </h5>
        <div class="detail-description-text">${listing.description.replace(/\n/g, '<br>')}</div>
      </div>
      ` : ''}
      
      <!-- Planning Info -->
      ${listing.planning_info ? `
      <div class="detail-planning mb-4">
        <h5 class="detail-section-title">
          <i class="bi bi-map-fill text-primary"></i>
          Thông tin quy hoạch
        </h5>
        <div class="detail-planning-text">${listing.planning_info.replace(/\n/g, '<br>')}</div>
      </div>
      ` : ''}
      
      <!-- Contact Info -->
      <div class="detail-contact">
        <h5 class="detail-section-title">
          <i class="bi bi-person-circle-fill text-primary"></i>
          Thông tin liên hệ
        </h5>
        <div class="contact-info-grid">
          <div class="contact-item">
            <i class="bi bi-person"></i>
            <span><strong>${listing.contact_name || 'N/A'}</strong></span>
          </div>
          <div class="contact-item">
            <i class="bi bi-telephone-fill"></i>
            <a href="tel:${listing.contact_phone || ''}">${listing.contact_phone || 'N/A'}</a>
          </div>
          ${listing.contact_zalo ? `
          <div class="contact-item">
            <i class="bi bi-chat-dots-fill"></i>
            <span>Zalo: ${listing.contact_zalo}</span>
          </div>
          ` : ''}
        </div>
        <div class="contact-actions mt-3">
          <a href="tel:${listing.contact_phone || ''}" class="btn btn-primary">
            <i class="bi bi-telephone-fill"></i> Gọi điện
          </a>
          ${listing.contact_zalo ? `
          <a href="https://zalo.me/${listing.contact_zalo}" target="_blank" class="btn btn-outline-primary">
            <i class="bi bi-chat-dots-fill"></i> Chat Zalo
          </a>
          ` : ''}
          <a href="/tin-dang/${listing.slug}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-box-arrow-up-right"></i> Xem trang chi tiết
          </a>
        </div>
      </div>
    </div>
  `;
  
  // Initialize image gallery
  if (images.length > 1) {
    initDetailImageGallery(images);
  }
}

function changeDetailImage(imageUrl, index) {
  document.getElementById('detailMainImage').src = imageUrl;
  document.querySelectorAll('.thumbnail-item').forEach((item, idx) => {
    item.classList.toggle('active', idx === index);
  });
}

function initDetailImageGallery(images) {
  // Already handled in renderListingDetail
}

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
