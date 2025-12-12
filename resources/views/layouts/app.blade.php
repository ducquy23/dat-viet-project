<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Đất Việt Map') - Bản đồ bất động sản địa phương</title>
    <meta name="description" content="@yield('description', 'Tìm kiếm và đăng tin bán đất trên bản đồ trực quan')">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.cdnfonts.com/css/sf-pro-display">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('styles')
</head>
<body>
    <!-- Header -->
    @include('components.header')

    <!-- Top Banner Ad -->
    @include('components.ads.top-banner')

    <!-- Main Content -->
    <main class="main-layout">
        @yield('content')
    </main>

    <!-- Bottom Ad Banner -->
    @include('components.ads.bottom-banner')

    <!-- Bottom Bar (VIP Carousel) -->
    @include('components.bottom-bar')

    <!-- Modals -->
    @include('components.modals.post-listing')
    @include('components.modals.login')
    @include('components.modals.register')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    @if(request()->routeIs('listings.my-listings'))
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    @endif

    @stack('scripts')
</body>
</html>

