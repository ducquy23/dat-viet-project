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

        // Map pins (use from app.js if available, otherwise use lot-marker style)
        const iconNormal = window.iconNormal || L.divIcon({
            className: "",
            html: '<div class="lot-marker"><div class="lot-rect"></div><div class="lot-pin"></div></div>',
            iconSize: [36, 48],
            iconAnchor: [18, 48],
            popupAnchor: [0, -48]
        });

        const iconVip = window.iconVip || L.divIcon({
            className: "",
            html: '<div class="lot-marker vip"><div class="lot-rect"></div><div class="lot-pin"></div></div>',
            iconSize: [36, 48],
            iconAnchor: [18, 48],
            popupAnchor: [0, -48]
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
                if (['city', 'category', 'max_price', 'max_area', 'has_road'].includes(key)) {
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
                        const price = new Intl.NumberFormat('vi-VN').format(listing.price);
                        const address = listing.address || '';
                        const cityDistrict = [listing.district, listing.city].filter(Boolean).join(', ');

                        const marker = L.marker([lat, lng], { 
                            icon: icon,
                            title: listing.title || `Tin đăng ${listing.id}`,
                            riseOnHover: true,
                            zIndexOffset: listing.is_vip ? 1000 : 500
                        }).addTo(map);
                        
                        marker.bindPopup(`
                            <div style="width:260px; font-family: 'SF Pro Display', sans-serif; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.12);">
                                <div style="position: relative;">
                                    ${listing.is_vip ? `<div style="position: absolute; top: 10px; right: 10px; z-index: 10; background: linear-gradient(135deg, #f4b400 0%, #ffd700 100%); color: #fff; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 20px; box-shadow: 0 2px 8px rgba(244,180,0,0.4); text-transform: uppercase; letter-spacing: 0.5px;">
                                        <i class="bi bi-star-fill" style="font-size: 9px;"></i> VIP
                                    </div>` : ''}
                                    <img src="${imageUrl}" style="width:100%; height:140px; object-fit:cover; display:block; transition: transform 0.3s;" onerror="this.src='{{ asset("images/Image-not-found.png") }}'" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%); padding: 12px;">
                                        <div style="color: #fff; font-weight: 800; font-size: 18px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                            ${price} triệu
                                        </div>
                                    </div>
                                </div>
                                <div style="padding: 14px;">
                                    <div style="font-weight: 700; font-size: 16px; color: #1a202c; margin-bottom: 8px; line-height: 1.3; display: flex; align-items: center; gap: 6px;">
                                        <span style="color: #335793;">${price}</span>
                                        <span style="color: #6c757d; font-weight: 500;">•</span>
                                        <span style="color: #335793;">${listing.area}m²</span>
                                    </div>
                                    ${listing.category ? `<div style="display: inline-flex; align-items: center; gap: 4px; background: linear-gradient(135deg, rgba(51,87,147,0.1) 0%, rgba(74,107,168,0.08) 100%); color: #335793; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 20px; margin-bottom: 8px;">
                                        <i class="bi bi-tag-fill" style="font-size: 10px;"></i> ${listing.category}
                                    </div>` : ''}
                                    ${address ? `<div style="color: #4a5568; font-size: 12px; margin-bottom: 12px; line-height: 1.5; display: flex; align-items: start; gap: 6px; padding: 8px; background: rgba(51,87,147,0.03); border-radius: 8px;">
                                        <i class="bi bi-geo-alt-fill" style="font-size: 13px; color: #335793; margin-top: 2px; flex-shrink: 0;"></i>
                                        <span style="flex: 1;">${address}${cityDistrict ? ', ' + cityDistrict : ''}</span>
                                    </div>` : ''}
                                    <button class="btn btn-primary btn-sm w-100" onclick="viewListing(${listing.id})" style="background: linear-gradient(135deg, #335793 0%, #4a6ba8 100%); border: none; font-size: 13px; font-weight: 600; padding: 10px 16px; border-radius: 8px; box-shadow: 0 4px 12px rgba(51,87,147,0.3); transition: all 0.3s; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(51,87,147,0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(51,87,147,0.3)'">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </button>
                                </div>
                            </div>
                        `, {
                            maxWidth: 280,
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
