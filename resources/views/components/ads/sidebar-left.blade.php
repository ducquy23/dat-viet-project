@php
    $ad = \App\Models\Ad::active()
        ->ofPosition('sidebar_left')
        ->ordered()
        ->first();
@endphp

@if($ad)
<div class="sidebar-ad-banner mt-3">
    <button class="btn-close-ad-small" onclick="this.parentElement.style.display='none'">
        <i class="bi bi-x"></i>
    </button>
    <a href="{{ $ad->link_url ?? '#' }}" class="ad-link-vertical" target="_blank" onclick="trackAdClick({{ $ad->id }})">
        <div class="ad-badge-small">Quảng cáo</div>
        <div class="ad-image-placeholder">
            @if($ad->image_url)
                <img src="{{ $ad->full_image_url }}" alt="{{ $ad->title }}" loading="lazy">
            @else
                <i class="bi bi-image"></i>
            @endif
        </div>
        <div class="ad-text-small">
            <strong>{{ $ad->title }}</strong>
            @if($ad->description)
                <small class="text-muted d-block">{{ $ad->description }}</small>
            @endif
        </div>
    </a>
</div>
@endif

