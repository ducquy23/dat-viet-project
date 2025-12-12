@extends('layouts.app')

@section('title', 'Trang chủ')
@section('description', 'Tìm kiếm và đăng tin bán đất trên bản đồ trực quan')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 layout-row">
        <!-- LEFT FILTER -->
        @include('components.filter-sidebar')

        <!-- MAP -->
        @include('components.map-area')

        <!-- RIGHT PANEL -->
        @include('components.detail-panel')
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Filter form handlers
    document.getElementById('filter-price')?.addEventListener('input', function() {
        document.getElementById('price-label').textContent = 
            new Intl.NumberFormat('vi-VN').format(this.value) + '+';
    });

    document.getElementById('filter-area')?.addEventListener('input', function() {
        document.getElementById('area-label').textContent = 
            new Intl.NumberFormat('vi-VN').format(this.value) + '+';
    });

    // City change - load districts
    document.getElementById('filter-city')?.addEventListener('change', function() {
        const cityId = this.value;
        const districtSelect = document.getElementById('filter-district');
        
        if (cityId) {
            fetch(`/api/districts?city_id=${cityId}`)
                .then(response => response.json())
                .then(data => {
                    districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                    data.districts.forEach(district => {
                        districtSelect.innerHTML += 
                            `<option value="${district.id}">${district.name}</option>`;
                    });
                });
        } else {
            districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        }
    });

    // Track ad clicks
    function trackAdClick(adId) {
        fetch(`/api/ads/${adId}/click`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
    }
</script>
@endpush

