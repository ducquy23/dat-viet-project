@php
    $ad = \App\Models\Ad::active()
        ->ofPosition('bottom')
        ->ordered()
        ->first();
@endphp

@if($ad)
<div class="bottom-ad-banner d-none d-md-block">
    <div class="container-fluid px-4">
        <div class="ad-banner-horizontal-bottom">
            <button class="btn-close-ad" onclick="this.parentElement.parentElement.style.display='none'">
                <i class="bi bi-x-lg"></i>
            </button>
            <a href="{{ $ad->link_url ?? '#' }}" class="ad-link" target="_blank" onclick="trackAdClick({{ $ad->id }})">
                <div class="ad-content">
                    <div class="ad-badge">Quảng cáo</div>
                    <div class="ad-text">
                        <strong>{{ $ad->title }}</strong>
                        @if($ad->link_text)
                            <span class="text-muted ms-2">{{ $ad->link_text }} →</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endif

