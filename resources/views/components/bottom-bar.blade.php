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
                    <button class="btn btn-sm btn-outline-primary vip-slider-prev" aria-label="Trước">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary vip-slider-next" aria-label="Sau">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <small class="text-muted d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left-right"></i>
                </small>
            </div>
        </div>
        <div class="vip-carousel-wrapper position-relative">
            @if(isset($vipListings) && count($vipListings) > 0)
                <div class="swiper vip-swiper" style="width: 100%; overflow: hidden;">
                    <div class="swiper-wrapper" style="display: flex !important; flex-direction: row !important; flex-wrap: nowrap !important;">
                        @foreach($vipListings as $listing)
                            <div class="swiper-slide" style="width: 320px !important; flex-shrink: 0 !important; display: flex !important;">
                                <div class="vip-card" onclick="window.location.href='{{ route('listings.show', $listing->slug) }}'" style="width: 100% !important;">
                                    <div class="vip-badge-top">
                                        <span class="vip-label">
                                            <i class="bi bi-star-fill"></i> VIP
                                        </span>
                                    </div>
                                    <div class="vip-card-image-wrapper">
                                        <img src="{{ $listing->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}" alt="{{ $listing->title }}">
                                        <div class="vip-card-overlay">
                                            <span class="vip-price-badge">{{ formatPrice($listing->price) }}</span>
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
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center text-muted py-4 w-100">
                    <i class="bi bi-inbox" style="font-size: 48px;"></i>
                    <p class="mt-2">Chưa có tin VIP nào</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<style>
/* Swiper Custom Styles - Force horizontal layout */
.vip-carousel-wrapper {
    overflow: hidden !important;
    width: 100% !important;
    position: relative;
}

/* Override vip-carousel styles from style.css */
.vip-carousel-wrapper .vip-carousel {
    display: none !important;
}

.vip-swiper.swiper,
.vip-swiper {
    padding-bottom: 0.5rem !important;
    width: 100% !important;
    overflow: hidden !important;
    margin: 0 !important;
    display: block !important;
    box-sizing: border-box !important;
    position: relative !important;
}

.vip-swiper .swiper-wrapper {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: stretch !important;
    width: auto !important;
    transform: translate3d(0px, 0px, 0px) !important;
    position: relative !important;
    box-sizing: border-box !important;
    transition-property: transform !important;
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

.vip-swiper .swiper-slide {
    height: auto !important;
    width: 320px !important;
    min-width: 320px !important;
    max-width: 320px !important;
    flex-shrink: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    box-sizing: border-box !important;
    position: relative !important;
    list-style: none !important;
    margin: 0 !important;
}

.vip-swiper .swiper-slide .vip-card {
    width: 100% !important;
    height: 100% !important;
    cursor: grab !important;
    display: flex !important;
    flex-direction: column !important;
    min-width: 320px !important;
    max-width: 320px !important;
    margin: 0 !important;
}

.vip-swiper .swiper-slide .vip-card:active {
    cursor: grabbing !important;
}

/* Disable button khi ở đầu/cuối */
.vip-slider-prev.swiper-button-disabled,
.vip-slider-next.swiper-button-disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}
</style>
@endpush

@push('scripts')
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
// Initialize VIP Swiper
(function() {
    function initVipSwiper() {
        // Wait for Swiper library to be available
        if (typeof Swiper === 'undefined') {
            console.warn('Swiper library not loaded yet, retrying...');
            setTimeout(initVipSwiper, 100);
            return;
        }

        const swiperElement = document.querySelector('.vip-swiper');
        if (!swiperElement) {
            console.warn('VIP Swiper element not found');
            return;
        }

        // Destroy existing instance if any
        if (swiperElement.swiper) {
            swiperElement.swiper.destroy(true, true);
        }

        // Force horizontal layout before initialization
        const wrapper = swiperElement.querySelector('.swiper-wrapper');
        if (wrapper) {
            wrapper.style.display = 'flex';
            wrapper.style.flexDirection = 'row';
            wrapper.style.flexWrap = 'nowrap';
        }

        const slides = swiperElement.querySelectorAll('.swiper-slide');
        slides.forEach(function(slide) {
            slide.style.width = '320px';
            slide.style.flexShrink = '0';
            slide.style.display = 'flex';
            slide.style.flexDirection = 'column';
        });

        try {
            const vipSwiper = new Swiper('.vip-swiper', {
                direction: 'horizontal',
                slidesPerView: 'auto',
                spaceBetween: 12,
                freeMode: true,
                grabCursor: true,
                speed: 600,
                watchOverflow: true,
                resistance: true,
                resistanceRatio: 0,
                navigation: {
                    nextEl: '.vip-slider-next',
                    prevEl: '.vip-slider-prev',
                },
                on: {
                    init: function() {
                        updateNavigationButtons(this);
                        this.update();
                        // Force layout update
                        setTimeout(() => {
                            this.update();
                            const wrapper = this.wrapperEl;
                            if (wrapper) {
                                wrapper.style.display = 'flex';
                                wrapper.style.flexDirection = 'row';
                            }
                        }, 50);
                    },
                    slideChange: function() {
                        updateNavigationButtons(this);
                    },
                    reachBeginning: function() {
                        updateNavigationButtons(this);
                    },
                    reachEnd: function() {
                        updateNavigationButtons(this);
                    }
                }
            });

            // Force update after initialization
            setTimeout(function() {
                vipSwiper.update();
                vipSwiper.updateSlides();
                vipSwiper.updateSlidesClasses();
            }, 200);
        } catch (error) {
            console.error('Error initializing VIP Swiper:', error);
        }
    }

    function updateNavigationButtons(swiper) {
        const prevBtn = document.querySelector('.vip-slider-prev');
        const nextBtn = document.querySelector('.vip-slider-next');

        if (prevBtn) {
            if (swiper.isBeginning) {
                prevBtn.classList.add('swiper-button-disabled');
                prevBtn.disabled = true;
            } else {
                prevBtn.classList.remove('swiper-button-disabled');
                prevBtn.disabled = false;
            }
        }

        if (nextBtn) {
            if (swiper.isEnd) {
                nextBtn.classList.add('swiper-button-disabled');
                nextBtn.disabled = true;
            } else {
                nextBtn.classList.remove('swiper-button-disabled');
                nextBtn.disabled = false;
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initVipSwiper, 100);
        });
    } else {
        setTimeout(initVipSwiper, 100);
    }

    // Re-initialize on window load
    window.addEventListener('load', function() {
        setTimeout(initVipSwiper, 200);
    });
})();
</script>
@endpush

