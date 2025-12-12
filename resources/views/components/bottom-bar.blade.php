<!-- VIP Bottom Bar -->
<div class="bottom-bar bg-white shadow-lg">
    <div class="container-fluid position-relative">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                <i class="bi bi-star-fill" style="color: #f4b400;"></i>
                <span>Tin VIP nổi bật</span>
            </h6>
            <div class="d-flex align-items-center gap-3">
                <div class="vip-slider-controls d-none d-md-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-primary vip-slider-btn vip-slider-prev" onclick="vipSliderPrev()" aria-label="Trước">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary vip-slider-btn vip-slider-next" onclick="vipSliderNext()" aria-label="Sau">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <small class="text-muted d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left-right"></i>
                    <span class="d-none d-md-inline">Kéo để xem thêm</span>
                </small>
            </div>
        </div>
        <div class="vip-carousel-wrapper position-relative">
            <div id="vip-carousel" class="vip-carousel d-flex gap-3 pb-2">
                @forelse($vipListings ?? [] as $listing)
                    <div class="vip-card" onclick="window.location.href='{{ route('listings.show', $listing->slug) }}'">
                        <div class="vip-badge-top">
                            <span class="vip-label">
                                <i class="bi bi-star-fill"></i> VIP
                            </span>
                        </div>
                        <div class="vip-card-image-wrapper">
                            <img src="{{ $listing->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}" alt="{{ $listing->title }}">
                            <div class="vip-card-overlay">
                                <span class="vip-price-badge">{{ number_format($listing->price / 1000000) }} triệu</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="vip-card-header">
                                <h6 class="vip-card-title mb-1">{{ Str::limit($listing->title, 50) }}</h6>
                                <div class="vip-card-meta mb-2">
                                    <span class="vip-meta-item">
                                        <i class="bi bi-rulers"></i> {{ number_format($listing->area, 1) }}m²
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
            <!-- Gradient overlays for better UX -->
            <div class="vip-carousel-gradient vip-carousel-gradient-left"></div>
            <div class="vip-carousel-gradient vip-carousel-gradient-right"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// VIP Slider functionality
let vipCarousel = null;
let isVipDragging = false;
let vipStartX = 0;
let vipScrollLeft = 0;

function initVipSlider() {
    vipCarousel = document.getElementById('vip-carousel');
    if (!vipCarousel) return;

    // Mouse drag
    vipCarousel.addEventListener('mousedown', (e) => {
        isVipDragging = true;
        vipCarousel.style.cursor = 'grabbing';
        vipStartX = e.pageX - vipCarousel.offsetLeft;
        vipScrollLeft = vipCarousel.scrollLeft;
    });

    vipCarousel.addEventListener('mouseleave', () => {
        isVipDragging = false;
        vipCarousel.style.cursor = 'grab';
    });

    vipCarousel.addEventListener('mouseup', () => {
        isVipDragging = false;
        vipCarousel.style.cursor = 'grab';
    });

    vipCarousel.addEventListener('mousemove', (e) => {
        if (!isVipDragging) return;
        e.preventDefault();
        const x = e.pageX - vipCarousel.offsetLeft;
        const walk = (x - vipStartX) * 2; // Scroll speed
        vipCarousel.scrollLeft = vipScrollLeft - walk;
        updateVipSliderButtons();
    });

    // Touch drag
    let vipTouchStartX = 0;
    let vipTouchScrollLeft = 0;

    vipCarousel.addEventListener('touchstart', (e) => {
        vipTouchStartX = e.touches[0].pageX - vipCarousel.offsetLeft;
        vipTouchScrollLeft = vipCarousel.scrollLeft;
    });

    vipCarousel.addEventListener('touchmove', (e) => {
        const x = e.touches[0].pageX - vipCarousel.offsetLeft;
        const walk = (x - vipTouchStartX) * 1.5;
        vipCarousel.scrollLeft = vipTouchScrollLeft - walk;
        updateVipSliderButtons();
    });

    // Scroll event
    vipCarousel.addEventListener('scroll', updateVipSliderButtons);
    
    // Initial button state
    updateVipSliderButtons();
}

function vipSliderPrev() {
    if (!vipCarousel) return;
    const cardWidth = vipCarousel.querySelector('.vip-card')?.offsetWidth || 320;
    const gap = 12;
    vipCarousel.scrollBy({
        left: -(cardWidth + gap) * 2,
        behavior: 'smooth'
    });
}

function vipSliderNext() {
    if (!vipCarousel) return;
    const cardWidth = vipCarousel.querySelector('.vip-card')?.offsetWidth || 320;
    const gap = 12;
    vipCarousel.scrollBy({
        left: (cardWidth + gap) * 2,
        behavior: 'smooth'
    });
}

function updateVipSliderButtons() {
    if (!vipCarousel) return;
    const prevBtn = document.querySelector('.vip-slider-prev');
    const nextBtn = document.querySelector('.vip-slider-next');
    
    if (prevBtn) {
        prevBtn.disabled = vipCarousel.scrollLeft <= 0;
        prevBtn.style.opacity = vipCarousel.scrollLeft <= 0 ? '0.5' : '1';
    }
    
    if (nextBtn) {
        const maxScroll = vipCarousel.scrollWidth - vipCarousel.clientWidth;
        nextBtn.disabled = vipCarousel.scrollLeft >= maxScroll - 10;
        nextBtn.style.opacity = vipCarousel.scrollLeft >= maxScroll - 10 ? '0.5' : '1';
    }
    
    // Update gradient overlays
    const leftGradient = document.querySelector('.vip-carousel-gradient-left');
    const rightGradient = document.querySelector('.vip-carousel-gradient-right');
    
    if (leftGradient) {
        leftGradient.style.opacity = vipCarousel.scrollLeft > 10 ? '0' : '1';
    }
    
    if (rightGradient) {
        const maxScroll = vipCarousel.scrollWidth - vipCarousel.clientWidth;
        rightGradient.style.opacity = vipCarousel.scrollLeft < maxScroll - 10 ? '1' : '0';
    }
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVipSlider);
} else {
    initVipSlider();
}
</script>
@endpush

