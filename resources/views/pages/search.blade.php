@extends('layouts.app')

@section('title', 'Tìm kiếm')
@section('description', 'Tìm kiếm tin đăng bất động sản')

@section('content')
<div class="container-fluid p-0">
  <div class="row g-0 layout-row">
    <!-- LEFT FILTER -->
    @include('components.filter-sidebar')

    <!-- MAP -->
    @include('components.map-area')

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
          <!-- TODO: Hiển thị danh sách kết quả tìm kiếm -->
          <div class="listings-list">
            <p class="text-center text-muted py-5">
              Đang tải kết quả tìm kiếm...
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Xử lý filter và tìm kiếm
  // TODO: Implement search functionality
</script>
@endpush

