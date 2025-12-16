<!-- Server-side rendered listing content -->
<!-- Gallery -->
<div class="gallery-main mb-2">
    <img
        id="lot-main-img"
        src="{{ $listing->primaryImage?->image_url ?? ($listing->images->first()?->image_url ?? asset('images/Image-not-found.png')) }}"
        class="img-fluid rounded lot-image"
        alt="{{ $listing->title }}"
        style="cursor: pointer;"
        onclick="openImageModal(this.src)">
</div>

<div class="gallery-thumbs d-flex gap-2 mb-3" id="lot-thumbs">
    @foreach($listing->images->take(4) as $image)
        <button class="thumb {{ $loop->first ? 'active' : '' }}" onclick="changeMainImage('{{ $image->image_url }}', this)">
            <img src="{{ $image->image_url }}" alt="Thumbnail">
        </button>
    @endforeach
</div>

<!-- Price & Address -->
<div class="d-flex align-items-start justify-content-between mb-3">
    <div class="flex-grow-1">
        <h4 class="lot-price fw-bold mb-1">{{ number_format($listing->price, 0) }} triệu • {{ $listing->area }}m²</h4>
        <p class="text-muted small mb-0 d-flex align-items-center gap-1" id="lot-address">
            <i class="bi bi-geo-alt-fill"></i>
            <span>{{ $listing->address }}</span>
        </p>
    </div>
    @auth('partner')
    <button class="btn btn-light btn-fav d-flex align-items-center justify-content-center {{ isset($isFavorited) && $isFavorited ? 'active' : '' }}"
            id="favorite-btn"
            onclick="toggleFavorite({{ $listing->id }})"
            title="Thêm vào yêu thích"
            style="width:44px; height:44px; border-radius:50%; padding:0;">
        <i class="bi bi-heart" style="font-size:18px;"></i>
    </button>
    @endauth
</div>

<!-- Mini Map -->
<div id="mini-map" class="mini-map mb-3 rounded-3" style="height: 200px;"></div>

<!-- Category & Tags -->
<div class="mb-3">
    <p class="text-muted small mb-2 d-flex align-items-center gap-1">
        <i class="bi bi-tag-fill"></i>
        <span>Loại đất: <b class="text-dark">{{ $listing->category->name }}</b></span>
    </p>
    <div class="d-flex flex-wrap gap-2" id="lot-tags">
        @foreach($listing->tags ?? [] as $tag)
            <span class="badge rounded-pill bg-primary-subtle text-primary">{{ $tag }}</span>
        @endforeach
    </div>
</div>

<!-- Seller Info -->
<div class="seller-card mb-3">
    <div class="d-flex align-items-center gap-2 mb-2">
        <div class="seller-avatar">
            <i class="bi bi-person-circle"></i>
        </div>
        <div class="flex-grow-1">
            <div class="fw-semibold">{{ $listing->contact_name }}</div>
            <div class="text-muted small d-flex align-items-center gap-1">
                <i class="bi bi-telephone"></i>
                <span>{{ $listing->contact_phone }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Details -->
<div class="detail-box mb-3">
    <div class="d-flex align-items-center mb-2">
        <i class="bi bi-card-text text-primary me-2"></i>
        <h6 class="fw-bold mb-0">Thông tin chi tiết</h6>
    </div>
    <ul class="list-unstyled mb-0 small">
        <li class="d-flex justify-content-between py-1">
            <span class="text-muted">Pháp lý</span>
            <span class="fw-semibold text-dark">{{ $listing->legal_status ?? 'Đang cập nhật' }}</span>
        </li>
        <li class="d-flex justify-content-between py-1">
            <span class="text-muted">Mặt tiền</span>
            <span class="fw-semibold text-dark">{{ $listing->front_width ? $listing->front_width . 'm' : 'Đang cập nhật' }}</span>
        </li>
        <li class="d-flex justify-content-between py-1">
            <span class="text-muted">Đường</span>
            <span class="fw-semibold text-dark">{{ $listing->road_type ?? 'Đang cập nhật' }}</span>
        </li>
        <li class="d-flex justify-content-between py-1">
            <span class="text-muted">Chiều sâu</span>
            <span class="fw-semibold text-dark">{{ $listing->depth ? $listing->depth . 'm' : 'Đang cập nhật' }}</span>
        </li>
        <li class="d-flex justify-content-between py-1">
            <span class="text-muted">Hướng</span>
            <span class="fw-semibold text-dark">{{ $listing->direction ?? 'Đang cập nhật' }}</span>
        </li>
        <li class="d-flex justify-content-between py-1">
            <span class="text-muted">Đơn giá</span>
            <span class="fw-semibold text-dark">{{ $listing->price_per_m2 ? number_format($listing->price_per_m2, 1) . 'tr/m²' : 'Đang cập nhật' }}</span>
        </li>
    </ul>
</div>

<!-- Description -->
@if($listing->description)
<div class="description-box mb-3">
    <div class="d-flex align-items-center mb-2">
        <i class="bi bi-info-circle text-primary me-2"></i>
        <h6 class="fw-bold mb-0">Mô tả</h6>
    </div>
    <p class="small text-muted mb-0">{{ $listing->description }}</p>
</div>
@endif

<!-- Action Buttons -->
<div class="d-flex gap-2 mb-2">
    <a href="tel:{{ $listing->contact_phone }}" class="btn btn-primary w-50 d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-telephone-fill"></i>
        <span>Gọi điện</span>
    </a>
    @if($listing->contact_zalo)
    <a href="https://zalo.me/{{ $listing->contact_zalo }}" target="_blank" class="btn btn-outline-primary w-50 d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-chat-dots-fill"></i>
        <span>Chat Zalo</span>
    </a>
    @endif
</div>

<a href="{{ route('listings.show', $listing->slug) }}" class="btn btn-outline-secondary w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
    <i class="bi bi-eye"></i>
    <span>Xem chi tiết đầy đủ</span>
</a>

<!-- Similar Listings -->
@if(isset($relatedListings) && $relatedListings->count() > 0)
<h5 class="fw-bold border-top pt-3 mb-3">CÁC LÔ ĐẤT TƯƠNG TỰ</h5>
<div id="similar-list">
    @foreach($relatedListings as $similar)
        <div class="similar-item" onclick="window.location.href='{{ route('listings.show', $similar->slug) }}'">
            <img src="{{ $similar->primaryImage?->image_url ?? asset('images/Image-not-found.png') }}" alt="{{ $similar->title }}">
            <div class="flex-grow-1">
                <div class="fw-bold">{{ number_format($similar->price, 0) }} triệu</div>
                <div class="text-muted small">{{ $similar->area }}m² • {{ $similar->category->name }}</div>
                <a href="{{ route('listings.show', $similar->slug) }}" class="btn btn-outline-primary btn-sm mt-1">Xem chi tiết</a>
            </div>
        </div>
    @endforeach
</div>
@endif

