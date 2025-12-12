@extends('layouts.app')

@section('title', 'Danh sách tin đăng')
@section('description', 'Xem tin đăng theo danh mục')

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
          <h5 class="mb-0">
            Danh sách tin đăng
          </h5>
        </div>

        <div class="panel-body">
          <div class="listings-list">
            <p class="text-center text-muted py-5">
              Đang tải danh sách tin đăng...
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

