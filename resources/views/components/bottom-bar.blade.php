<!-- VIP Bottom Bar -->
<div class="bottom-bar bg-white shadow-lg">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-star-fill" style="color: #f4b400;"></i>
                <span>Tin VIP nổi bật</span>
            </h6>
            <small class="text-muted d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left-right"></i>
                <span>Vuốt ngang để xem thêm</span>
            </small>
        </div>
        <div id="vip-carousel" class="vip-carousel d-flex gap-3 overflow-auto pb-2">
            @forelse($vipListings ?? [] as $listing)
                <div class="vip-card" onclick="viewListing({{ $listing->id }})">
                    <div class="vip-badge-top">
                        <span class="vip-label">
                            <i class="bi bi-star-fill"></i> VIP
                        </span>
                    </div>
                    <div class="vip-card-image-wrapper">
                        <img src="{{ $listing->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}" alt="{{ $listing->title }}">
                        <div class="vip-card-overlay">
                            <span class="vip-price-badge">{{ number_format($listing->price) }} triệu</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="vip-card-header">
                            <h6 class="vip-card-title mb-1">{{ $listing->title }}</h6>
                            <div class="vip-card-meta mb-2">
                                <span class="vip-meta-item">
                                    <i class="bi bi-rulers"></i> {{ $listing->area }}m²
                                </span>
                                <span class="vip-meta-item">
                                    <i class="bi bi-tag"></i> {{ $listing->category->name }}
                                </span>
                            </div>
                        </div>

                        <div class="vip-card-address mb-2">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $listing->address }}">
                                {{ $listing->address }}
                            </span>
                        </div>

                        <div class="vip-card-tags mb-2">
                            @if($listing->tags && is_array($listing->tags))
                                @foreach(array_slice($listing->tags, 0, 3) as $tag)
                                    <span class="badge badge-vip-card">{{ $tag }}</span>
                                @endforeach
                            @endif
                        </div>

                        <a href="{{ route('listings.show', $listing->slug) }}" class="btn btn-primary btn-sm w-100 vip-card-btn" onclick="event.stopPropagation();">
                            <i class="bi bi-map"></i> Xem chi tiết
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4 w-100">
                    <i class="bi bi-inbox" style="font-size: 48px;"></i>
                    <p class="mt-2">Chưa có tin VIP nào</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

