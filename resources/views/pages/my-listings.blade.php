@extends('layouts.app')

@section('title', 'Tin đăng của tôi')
@section('description', 'Quản lý tin đăng của bạn')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Tin đăng của tôi</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal">
          <i class="bi bi-plus-circle"></i> Đăng tin mới
        </button>
      </div>

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
                <img src="{{ $listing->primaryImage?->image_url ?? asset('images/placeholder.jpg') }}" 
                     alt="{{ $listing->title }}" 
                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;">
              </td>
              <td>
                <div class="fw-bold">{{ Str::limit($listing->title, 50) }}</div>
                <div class="text-muted small">{{ $listing->address }}</div>
              </td>
              <td>{{ number_format($listing->price) }} triệu</td>
              <td>{{ $listing->area }}m²</td>
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
      <div class="mt-4">
        {{ $listings->links() }}
      </div>
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
    </div>
  </div>
</div>
@endsection

