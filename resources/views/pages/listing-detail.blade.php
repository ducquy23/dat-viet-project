@extends('layouts.app')

@section('title', 'Chi tiết tin đăng')
@section('description', 'Xem chi tiết tin đăng bất động sản')

@section('content')
<div class="container py-4">
  <div class="row">
    <!-- Nội dung chính -->
    <div class="col-lg-8">
      <!-- TODO: Hiển thị chi tiết tin đăng -->
      <div class="card mb-4">
        <div class="card-body">
          <h1 class="card-title">Đang tải chi tiết tin đăng...</h1>
          <p class="text-muted">Chức năng đang phát triển</p>
        </div>
      </div>

      <!-- Thông tin chi tiết -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Thông tin chi tiết</h5>
        </div>
        <div class="card-body">
          <!-- TODO: Hiển thị thông tin chi tiết -->
        </div>
      </div>

      <!-- Mô tả -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Mô tả</h5>
        </div>
        <div class="card-body">
          <!-- TODO: Hiển thị mô tả -->
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <!-- Thông tin liên hệ -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Thông tin liên hệ</h5>
        </div>
        <div class="card-body">
          <!-- TODO: Hiển thị thông tin liên hệ -->
        </div>
      </div>

      <!-- Tin liên quan -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Tin liên quan</h5>
        </div>
        <div class="card-body">
          <!-- TODO: Hiển thị tin liên quan -->
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Xử lý yêu thích
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
      // TODO: Cập nhật UI
      console.log(data);
    });
  }

  // Xử lý liên hệ
  function contactListing(listingId) {
    // TODO: Hiển thị form liên hệ
  }
</script>
@endpush

