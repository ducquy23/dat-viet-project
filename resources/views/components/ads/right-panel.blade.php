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
        <div class="ad-content-horizontal">
            <div class="ad-icon">
                <i class="bi bi-bank"></i>
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

