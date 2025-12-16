@php
    $ad = \App\Models\Ad::active()
        ->ofPosition('sidebar_right')
        ->ordered()
        ->first();
@endphp

@if($ad)
<div class="right-panel-ad-banner mb-3">
    <button class="btn-close-ad-small" onclick="this.parentElement.style.display='none'">
        <i class="bi bi-x"></i>
    </button>
    <a href="{{ $ad->link_url ?? '#' }}" class="ad-link-horizontal" target="_blank" onclick="trackAdClick({{ $ad->id }})">
        <div class="ad-badge-small">Quảng cáo</div>
        <div class="ad-content-horizontal d-flex align-items-center gap-3">
            <div class="ad-thumb">
                @if($ad->image_url)
                    <img src="{{ $ad->full_image_url }}" alt="{{ $ad->title }}" loading="lazy">
                @else
                    <i class="bi bi-image" style="font-size:28px; color:#9ca3af;"></i>
                @endif
            </div>
            <div class="ad-text-horizontal">
                <strong>{{ $ad->title }}</strong>
                @if($ad->description)
                    <small class="text-muted d-block">{{ $ad->description }}</small>
                @endif
            </div>
        </div>
    </a>
</div>
@endif

