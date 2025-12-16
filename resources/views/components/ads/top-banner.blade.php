@php
    $ad = \App\Models\Ad::active()
        ->ofPosition('top')
        ->ordered()
        ->first();
@endphp

@if($ad)
<div class="top-banner-ad d-none d-lg-block">
    <div class="container-fluid px-4">
        <div class="ad-banner-horizontal" @if($ad->image_url) style="background-image: url('{{ $ad->full_image_url }}');" @endif>
            <div class="ad-banner-overlay"></div>
            <button class="btn-close-ad" onclick="this.parentElement.parentElement.style.display='none'">
                <i class="bi bi-x-lg"></i>
            </button>
            <a href="{{ $ad->link_url ?? '#' }}" class="ad-link" target="_blank" onclick="trackAdClick({{ $ad->id }})">
                <div class="ad-content-top">
                    <div class="ad-section-left">
                    <div class="ad-badge">Quảng cáo</div>
                        <div class="ad-title-section">
                            <h2 class="ad-title-main">{{ $ad->title }}</h2>
                            @if($ad->description)
                                <p class="ad-subtitle">{{ $ad->description }}</p>
                            @endif
                        @if($ad->link_text)
                                <div class="ad-cta-button">
                                    <span>{{ $ad->link_text }}</span>
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                        @endif
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endif

