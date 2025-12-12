<!-- MAP AREA -->
<div class="col-12 col-md-6 col-lg-7">
    <div class="map-wrapper p-3">
        <div id="map" class="rounded-4 shadow-sm"></div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize map
    const map = L.map('map', { scrollWheelZoom: true }).setView([10.776, 106.700], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Map pins
    const iconNormal = L.divIcon({
        className: "",
        html: '<div class="map-pin"></div>',
        iconSize: [18, 18],
        iconAnchor: [9, 18],
        popupAnchor: [0, -18]
    });

    const iconVip = L.divIcon({
        className: "",
        html: '<div class="map-pin vip"></div>',
        iconSize: [18, 18],
        iconAnchor: [9, 18],
        popupAnchor: [0, -18]
    });

    // Load listings from API
    let markers = [];
    let currentListing = null;
    
    function loadListings() {
        // Clear existing markers
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        // Lấy filter params từ URL
        const params = new URLSearchParams(window.location.search);
        const apiUrl = new URL('{{ route("api.listings.map") }}', window.location.origin);
        params.forEach((value, key) => {
            if (['city', 'district', 'category', 'max_price', 'max_area', 'has_road'].includes(key)) {
                apiUrl.searchParams.append(key, value);
            }
        });

        // Fetch listings
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                data.listings.forEach(listing => {
                    const icon = listing.is_vip ? iconVip : iconNormal;
                    const imageUrl = listing.image ? (listing.image.startsWith('http') ? listing.image : `/storage/${listing.image}`) : '{{ asset("images/placeholder.jpg") }}';
                    const price = new Intl.NumberFormat('vi-VN').format(listing.price);
                    
                    const marker = L.marker([listing.latitude, listing.longitude], { icon })
                        .addTo(map)
                        .bindPopup(`
                            <div style="width:180px">
                                <img src="${imageUrl}" style="width:100%; height:90px; object-fit:cover; border-radius:8px; margin-bottom:6px;">
                                <div class="fw-semibold">${price} triệu • ${listing.area}m²</div>
                                <div class="text-muted small mb-2">${listing.category || ''}</div>
                                <a href="/tin-dang/${listing.slug}" class="btn btn-primary btn-sm w-100" style="text-decoration:none; color:white;">
                                    Xem chi tiết
                                </a>
                            </div>
                        `);
                    
                    marker.on('click', () => {
                        loadListingDetail(listing.id);
                    });
                    
                    markers.push(marker);
                });
            });
    }

    function loadListingDetail(listingId) {
        fetch(`/api/listings/${listingId}`)
            .then(response => response.json())
            .then(data => {
                currentListing = data.listing;
                // Reload page với listing detail hoặc update detail panel via AJAX
                // Tạm thời redirect đến trang chi tiết
                if (data.listing && data.listing.slug) {
                    window.location.href = `/tin-dang/${data.listing.slug}`;
                }
            })
            .catch(error => {
                console.error('Error loading listing detail:', error);
            });
    }

    function viewListing(id) {
        loadListingDetail(id);
        // Scroll to detail panel on mobile
        if (window.innerWidth < 768) {
            document.getElementById('detail-panel')?.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Load listings on page load
    loadListings();
    
    // Reload listings when map bounds change
    map.on('moveend', function() {
        const bounds = map.getBounds();
        // Có thể load thêm listings khi zoom/pan
        // loadListings();
    });
</script>
@endpush

