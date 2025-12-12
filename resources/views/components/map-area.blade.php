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
    
    function loadListings() {
        // Clear existing markers
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        // Fetch listings
        fetch('{{ route("api.listings.map") }}')
            .then(response => response.json())
            .then(data => {
                data.listings.forEach(listing => {
                    const icon = listing.is_vip ? iconVip : iconNormal;
                    const marker = L.marker([listing.latitude, listing.longitude], { icon })
                        .addTo(map)
                        .bindPopup(`
                            <div style="width:180px">
                                <img src="${listing.image}" style="width:100%; height:90px; object-fit:cover; border-radius:8px; margin-bottom:6px;">
                                <div class="fw-semibold">${listing.price} • ${listing.area}m²</div>
                                <div class="text-muted small mb-2">${listing.category}</div>
                                <button class="btn btn-primary btn-sm w-100" onclick="viewListing(${listing.id})">
                                    Xem chi tiết
                                </button>
                            </div>
                        `);
                    
                    marker.on('click', () => {
                        loadListingDetail(listing.id);
                    });
                    
                    markers.push(marker);
                });
            });
    }

    function viewListing(id) {
        loadListingDetail(id);
        // Scroll to detail panel on mobile
        if (window.innerWidth < 768) {
            document.getElementById('detail-panel').scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Load listings on page load
    loadListings();
</script>
@endpush

