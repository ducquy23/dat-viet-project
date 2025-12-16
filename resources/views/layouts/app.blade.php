<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Đất Việt Map') - Bản đồ bất động sản địa phương</title>
    <meta name="description" content="@yield('description', 'Tìm kiếm và đăng tin bán đất trên bản đồ trực quan')">

    {{-- Canonical URL cho SEO --}}
    <link rel="canonical" href="@yield('canonical', url()->current())">

    {{-- Open Graph cho Facebook / Zalo / mạng xã hội --}}
    <meta property="og:locale" content="vi_VN">
    <meta property="og:site_name" content="Đất Việt Map">
    <meta property="og:type" content="@yield('og_type', 'article')">
    <meta property="og:title" content="@yield('og_title','Đất Việt Map')">
    <meta property="og:description" content="@yield('og_description','Tìm kiếm và đăng tin bán đất trên bản đồ trực quan')">
    <meta property="og:url" content="@yield('og_url', url()->current())">
    <meta property="og:image" content="@yield('og_image', asset('images/logo.png'))">

    {{-- Thẻ Twitter card (Google cũng dùng khi hiển thị snippet mở rộng) --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title','Đất Việt Map')">
    <meta name="twitter:description" content="@yield('og_description', 'Tìm kiếm và đăng tin bán đất trên bản đồ trực quan')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/logo.png'))">

    {{-- Favicon / App icons --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

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
    @if(!request()->routeIs('listings.my-listings'))
        @include('components.ads.top-banner')
    @endif

    <!-- Main Content -->
    <main class="main-layout">
        @yield('content')
    </main>

    <!-- Bottom Ad Banner -->
    @if(!request()->routeIs('listings.my-listings'))
        @include('components.ads.bottom-banner')
    @endif

    <!-- Bottom Bar (VIP Carousel) -->
    @if(!request()->routeIs('listings.my-listings'))
        @include('components.bottom-bar')
    @endif

    <!-- Toast Container -->
    @include('components.toast-container')

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

