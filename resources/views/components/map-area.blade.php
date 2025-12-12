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
            // Clear existing markers from both sources
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];
            
            // Also clear markers from app.js if they exist
            if (window.markerLayers && Array.isArray(window.markerLayers)) {
                window.markerLayers.forEach(m => {
                    if (map.hasLayer(m)) {
                        map.removeLayer(m);
                    }
                });
                window.markerLayers = [];
            }

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
                    // Track IDs to avoid duplicates
                    const addedIds = new Set();
                    
                    data.listings.forEach(listing => {
                        // Skip if this ID was already added
                        if (addedIds.has(listing.id)) {
                            console.warn(`Duplicate listing ID ${listing.id} detected, skipping`);
                            return;
                        }
                        addedIds.add(listing.id);
                        
                        // Validate coordinates
                        if (!listing.latitude || !listing.longitude || isNaN(listing.latitude) || isNaN(listing.longitude)) {
                            console.warn(`Invalid coordinates for listing ${listing.id}`);
                            return;
                        }
                        
                        const lat = parseFloat(listing.latitude);
                        const lng = parseFloat(listing.longitude);
                        
                        // Validate coordinate ranges (Vietnam bounds approximately)
                        if (lat < 8.5 || lat > 23.5 || lng < 102.0 || lng > 110.0) {
                            console.warn(`Coordinates out of Vietnam bounds for listing ${listing.id}`);
                            return;
                        }
                        
                        const icon = listing.is_vip ? iconVip : iconNormal;
                        const imageUrl = listing.image ? (listing.image.startsWith('http') ? listing.image : `/storage/${listing.image}`) : '{{ asset("images/Image-not-found.png") }}';
                        const price = new Intl.NumberFormat('vi-VN').format(listing.price / 1000000);
                        const address = listing.address || '';
                        const cityDistrict = [listing.district, listing.city].filter(Boolean).join(', ');

                        const marker = L.marker([lat, lng], { 
                            icon: icon,
                            title: listing.title || `Tin đăng ${listing.id}`,
                            riseOnHover: true,
                            zIndexOffset: listing.is_vip ? 1000 : 500
                        }).addTo(map);
                        
                        marker.bindPopup(`
                            <div style="width:220px; font-family: 'SF Pro Display', sans-serif;">
                                <img src="${imageUrl}" style="width:100%; height:120px; object-fit:cover; border-radius:8px 8px 0 0; margin-bottom:8px; display:block;" onerror="this.src='{{ asset("images/Image-not-found.png") }}'">
                                <div style="padding: 0 8px 8px 8px;">
                                    <div style="font-weight:700; font-size:15px; color:#335793; margin-bottom:6px; line-height:1.3;">
                                        ${price} triệu • ${listing.area}m²
                                    </div>
                                    ${listing.category ? `<div style="color:#6c757d; font-size:12px; margin-bottom:4px;">
                                        <i class="bi bi-tag-fill" style="font-size:10px;"></i> ${listing.category}
                                    </div>` : ''}
                                    ${address ? `<div style="color:#6c757d; font-size:11px; margin-bottom:8px; line-height:1.4; display:flex; align-items:start; gap:4px;">
                                        <i class="bi bi-geo-alt-fill" style="font-size:11px; margin-top:2px; flex-shrink:0;"></i>
                                        <span>${address}${cityDistrict ? ', ' + cityDistrict : ''}</span>
                                    </div>` : ''}
                                    <button class="btn btn-primary btn-sm w-100" onclick="viewListing(${listing.id})" style="border:none; cursor:pointer; font-size:12px; padding:6px 12px; border-radius:6px;">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </button>
                                </div>
                            </div>
                        `, {
                            maxWidth: 250,
                            className: 'custom-popup',
                            closeButton: true,
                            autoPan: true,
                            autoPanPadding: [50, 50]
                        });

                        marker.on('click', async () => {
                            // Zoom to marker location
                            map.setView([listing.latitude, listing.longitude], Math.max(map.getZoom(), 16), {
                                animate: true,
                                duration: 0.5
                            });
                            
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
                    
                    // Store markers globally so app.js can clear them
                    window.mapAreaMarkers = markers;
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
