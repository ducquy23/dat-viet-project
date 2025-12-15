@extends('layouts.app')

@section('title', 'Tìm kiếm')
@section('description', 'Tìm kiếm tin đăng bất động sản')

@section('content')
<div class="container-fluid p-0">
  <div class="row g-0 layout-row">
    <!-- LEFT FILTER -->
    <div class="col-12 col-md-3 col-lg-2 border-end p-3 bg-white sidebar-left">
      @include('components.filter-sidebar')
    </div>

    <!-- MAP -->
    <div class="col-12 col-md-6 col-lg-7">
      @include('components.map-area')
    </div>

    <!-- RIGHT PANEL -->
    <div class="col-md-4 detail-panel">
      <div class="panel-content">
        <div class="panel-header">
          <h5 class="mb-0">Kết quả tìm kiếm</h5>
          @if(isset($keyword) && $keyword)
            <p class="text-muted small mb-0">Từ khóa: <strong>{{ $keyword }}</strong></p>
          @endif
        </div>

        <div class="panel-body">
          @if($listings->count() > 0)
          <div class="listings-list">
            @foreach($listings as $listing)
            <div class="listing-item mb-3 p-3 border rounded" onclick="window.location.href='{{ route('listings.show', $listing->slug) }}'">
              <div class="d-flex gap-3">
                <img src="{{ $listing->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}"
                     alt="{{ $listing->title }}"
                     style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                <div class="flex-grow-1">
                  <h6 class="fw-bold mb-1">{{ $listing->title }}</h6>
                  <div class="text-primary fw-bold mb-1">{{ number_format($listing->price) }} triệu • {{ $listing->area }}m²</div>
                  <div class="text-muted small mb-1">
                    <i class="bi bi-geo-alt"></i> {{ $listing->address }}, {{ $listing->district?->name }}, {{ $listing->city?->name }}
                  </div>
                  <div class="d-flex gap-2">
                    <span class="badge bg-secondary">{{ $listing->category->name }}</span>
                    @if($listing->isVip())
                    <span class="badge bg-warning text-dark">VIP</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
            @endforeach

            <!-- Pagination -->
            <div class="mt-4">
              {{ $listings->links() }}
            </div>
          </div>
          @else
          <div class="text-center py-5">
            <i class="bi bi-search text-muted" style="font-size: 64px;"></i>
            <p class="text-muted mt-3">Không tìm thấy kết quả nào</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Về trang chủ</a>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

