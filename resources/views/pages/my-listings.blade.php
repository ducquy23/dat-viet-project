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

      <!-- TODO: Hiển thị danh sách tin đăng -->
      <div class="card">
        <div class="card-body">
          <p class="text-center text-muted py-5 mb-0">
            Đang tải danh sách tin đăng...
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

