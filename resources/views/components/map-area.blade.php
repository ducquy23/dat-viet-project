<!-- MAP AREA -->
<div class="col-12 col-md-6 col-lg-7">
    <div class="map-wrapper p-3">
        <div id="map" class="rounded-4 shadow-sm"></div>
    </div>
</div>

@push('scripts')
<script>
    // Wait for app.js to load
    document.addEventListener('DOMContentLoaded', function() {
        // Use map from app.js if available, otherwise create new one
        if (!window.mainMap) {
            window.mainMap = L.map('map', { scrollWheelZoom: true }).setView([10.776, 106.700], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(window.mainMap);
        }
        
        const map = window.mainMap;
        
        // Map pins (use from app.js if available)
        const iconNormal = window.iconNormal || L.divIcon({
            className: "",
            html: '<div class="map-pin"></div>',
            iconSize: [18, 18],
            iconAnchor: [9, 18],
            popupAnchor: [0, -18]
        });

        const iconVip = window.iconVip || L.divIcon({
            className: "",
            html: '<div class="map-pin vip"></div>',
            iconSize: [18, 18],
            iconAnchor: [9, 18],
            popupAnchor: [0, -18]
        });

        // Store markers
        let markers = [];
        
        // Load listings from API
        function loadListingsForMap() {
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
                        const price = new Intl.NumberFormat('vi-VN').format(listing.price / 1000000);
                        
                        const marker = L.marker([listing.latitude, listing.longitude], { icon })
                            .addTo(map)
                            .bindPopup(`
                                <div style="width:180px">
                                    <img src="${imageUrl}" style="width:100%; height:90px; object-fit:cover; border-radius:8px; margin-bottom:6px;" onerror="this.src='{{ asset("images/placeholder.jpg") }}'">
                                    <div class="fw-semibold">${price} triệu • ${listing.area}m²</div>
                                    <div class="text-muted small mb-2">${listing.category || ''}</div>
                                    <button class="btn btn-primary btn-sm w-100" onclick="viewListing(${listing.id})" style="border:none; cursor:pointer;">
                                        Xem chi tiết
                                    </button>
                                </div>
                            `);
                        
                        marker.on('click', async () => {
                            // Load detail and update right panel
                            if (window.loadListingDetail && window.updateDetail) {
                                await window.loadListingDetail(listing.id);
                                const lot = window.lots?.find(l => l.id === listing.id);
                                if (lot) {
                                    await window.updateDetail(lot);
                                }
                            }
                            // Scroll to detail panel on mobile
                            if (window.innerWidth < 768) {
                                document.getElementById('detail-panel')?.scrollIntoView({ behavior: 'smooth' });
                            }
                        });
                        
                        markers.push(marker);
                    });
                })
                .catch(error => {
                    console.error('Error loading listings:', error);
                });
        }

        // Expose viewListing function globally
        window.viewListing = async function(id) {
            if (window.loadListingDetail && window.updateDetail) {
                await window.loadListingDetail(id);
                const lot = window.lots?.find(l => l.id === id);
                if (lot) {
                    await window.updateDetail(lot);
                }
            }
            // Scroll to detail panel on mobile
            if (window.innerWidth < 768) {
                document.getElementById('detail-panel')?.scrollIntoView({ behavior: 'smooth' });
            }
        };

        // Load listings on page load
        loadListingsForMap();
        
        // Reload listings when map bounds change (debounced)
        let reloadTimer;
        map.on('moveend', function() {
            clearTimeout(reloadTimer);
            reloadTimer = setTimeout(() => {
                // Optional: reload listings when map moves
                // loadListingsForMap();
            }, 500);
        });
    });
</script>
@endpush
