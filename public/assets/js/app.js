// ===== MAP SETUP =====
// Check if map element exists and not already initialized
let map;
const mapElement = document.getElementById('map');
if (mapElement && !window.mainMap) {
    map = L.map('map', { scrollWheelZoom: true }).setView([10.776, 106.700], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    // Expose map globally
    window.mainMap = map;
} else if (window.mainMap) {
    // Use existing map from map-area component
    map = window.mainMap;
}


// ===== DATA STORAGE =====
let lots = [];
let currentListing = null;
let loadingListings = false;
let activeMarker = null; // Track the currently active/selected marker
let loadingDetailQueue = []; // Queue for loadListingDetail calls
let isLoadingDetail = false; // Flag to prevent multiple simultaneous detail loads

// ===== MAP ICONS =====
// Custom marker style (dashed tím + pin vàng) - Improved design
function ensureLotMarkerStyle() {
    if (document.getElementById('lot-marker-style')) return;
    const style = document.createElement('style');
    style.id = 'lot-marker-style';
    style.innerHTML = `
      .lot-marker {
        position: relative;
        width: 36px;
        height: 48px;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        filter: drop-shadow(0 4px 12px rgba(0,0,0,0.15));
      }
      .lot-marker:hover {
        transform: scale(1.15);
        filter: drop-shadow(0 6px 16px rgba(0,0,0,0.25));
        z-index: 1000;
      }
      .lot-marker .lot-rect {
        position: absolute;
        top: 8px;
        left: 3px;
        width: 30px;
        height: 28px;
        border: 2.5px dashed #7c3aed;
        border-radius: 8px;
        background: linear-gradient(135deg, rgba(124,58,237,0.15) 0%, rgba(139,92,246,0.1) 100%);
        box-sizing: border-box;
        animation: pulse-border 2s ease-in-out infinite;
      }
      .lot-marker .lot-pin {
        position: absolute;
        left: 50%;
        top: -4px;
        transform: translateX(-50%);
        width: 22px;
        height: 22px;
        background: linear-gradient(135deg, #335793 0%, #4a6ba8 100%);
        border: 3px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 3px 8px rgba(51,87,147,0.4), 0 0 0 2px rgba(51,87,147,0.2);
        z-index: 2;
      }
      .lot-marker .lot-pin:after {
        content: '';
        position: absolute;
        left: 50%;
        bottom: -10px;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 12px solid #335793;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
      }
      .lot-marker.vip .lot-rect {
        border-color: #f97316;
        background: linear-gradient(135deg, rgba(249,115,22,0.2) 0%, rgba(251,146,60,0.15) 100%);
        border-width: 3px;
        animation: pulse-border-vip 2s ease-in-out infinite;
      }
      .lot-marker.vip .lot-pin {
        background: linear-gradient(135deg, #f4b400 0%, #ffd700 100%);
        border-color: #fff3cd;
        box-shadow: 0 3px 10px rgba(244,180,0,0.5), 0 0 0 2px rgba(244,180,0,0.3);
      }
      .lot-marker.vip .lot-pin:after {
        border-top-color: #f4b400;
      }
      .lot-marker-active {
        transform: scale(1.2) !important;
        z-index: 2000 !important;
        filter: drop-shadow(0 8px 20px rgba(51,87,147,0.5)) !important;
      }
      .lot-marker-active .lot-rect {
        border-color: #335793 !important;
        border-width: 3px !important;
        background: linear-gradient(135deg, rgba(51,87,147,0.3) 0%, rgba(74,107,168,0.2) 100%) !important;
        box-shadow: 0 0 0 3px rgba(51,87,147,0.3) !important;
        animation: pulse-active 1.5s ease-in-out infinite !important;
      }
      .lot-marker-active .lot-pin {
        background: linear-gradient(135deg, #335793 0%, #4a6ba8 100%) !important;
        box-shadow: 0 4px 12px rgba(51,87,147,0.6), 0 0 0 4px rgba(51,87,147,0.3) !important;
        transform: scale(1.15) !important;
      }
      .lot-marker-active.vip .lot-rect {
        border-color: #f4b400 !important;
        background: linear-gradient(135deg, rgba(244,180,0,0.4) 0%, rgba(255,215,0,0.3) 100%) !important;
        box-shadow: 0 0 0 3px rgba(244,180,0,0.4) !important;
        animation: pulse-active-vip 1.5s ease-in-out infinite !important;
      }
      .lot-marker-active.vip .lot-pin {
        background: linear-gradient(135deg, #f4b400 0%, #ffd700 100%) !important;
        box-shadow: 0 4px 14px rgba(244,180,0,0.7), 0 0 0 4px rgba(244,180,0,0.4) !important;
      }
      @keyframes pulse-border {
        0%, 100% { border-color: #7c3aed; opacity: 1; }
        50% { border-color: #a855f7; opacity: 0.8; }
      }
      @keyframes pulse-border-vip {
        0%, 100% { border-color: #f97316; opacity: 1; }
        50% { border-color: #fb923c; opacity: 0.8; }
      }
      @keyframes pulse-active {
        0%, 100% {
          box-shadow: 0 0 0 3px rgba(51,87,147,0.3);
          transform: scale(1);
        }
        50% {
          box-shadow: 0 0 0 6px rgba(51,87,147,0.15);
          transform: scale(1.05);
        }
      }
      @keyframes pulse-active-vip {
        0%, 100% {
          box-shadow: 0 0 0 3px rgba(244,180,0,0.4);
          transform: scale(1);
        }
        50% {
          box-shadow: 0 0 0 6px rgba(244,180,0,0.2);
          transform: scale(1.05);
        }
      }
    `;
    document.head.appendChild(style);
}
ensureLotMarkerStyle();

const iconNormal = L.divIcon({
    className: "",
    html: '<div class="lot-marker"><div class="lot-rect"></div><div class="lot-pin"></div></div>',
    iconSize: [36, 48],
    iconAnchor: [18, 48],
    popupAnchor: [0, -48]
});

const iconVip = L.divIcon({
    className: "",
    html: '<div class="lot-marker vip"><div class="lot-rect"></div><div class="lot-pin"></div></div>',
    iconSize: [36, 48],
    iconAnchor: [18, 48],
    popupAnchor: [0, -48]
});

let miniMap;
let miniMarker;
let activePolygon;
let miniPolygon;
let userMarker;
let userCircle;
let markerLayers = [];

// ===== RENDER MARKERS =====
function renderMarkers(data) {
    // Use global map if available
    const currentMap = map || window.mainMap;
    if (!currentMap) {
        console.warn('Map not initialized');
        return;
    }

    // Clear ALL existing markers from map (comprehensive cleanup)
    // Clear markerLayers
    markerLayers.forEach(m => {
        if (currentMap.hasLayer(m)) {
            currentMap.removeLayer(m);
        }
    });
    markerLayers = [];

    // Clear mapAreaMarkers
    if (window.mapAreaMarkers && Array.isArray(window.mapAreaMarkers)) {
        window.mapAreaMarkers.forEach(m => {
            if (currentMap.hasLayer(m)) {
                currentMap.removeLayer(m);
            }
        });
        window.mapAreaMarkers = [];
    }

    // Clear any other markers that might exist on the map
    currentMap.eachLayer(function(layer) {
        if (layer instanceof L.Marker && layer.lotId) {
            currentMap.removeLayer(layer);
        }
    });

    // Clear active marker reference
    activeMarker = null;

    // Track IDs to avoid duplicates
    const addedIds = new Set();

    data.forEach(lot => {
        // Skip if this ID was already added
        if (addedIds.has(lot.id)) {
            console.warn(`Duplicate listing ID ${lot.id} detected, skipping`);
            return;
        }

        // Validate coordinates
        if (!lot.lat || !lot.lng || isNaN(lot.lat) || isNaN(lot.lng)) {
            console.warn(`Invalid coordinates for listing ${lot.id}: lat=${lot.lat}, lng=${lot.lng}`);
            return;
        }

        // Validate coordinate ranges (Vietnam bounds approximately)
        const lat = parseFloat(lot.lat);
        const lng = parseFloat(lot.lng);
        if (lat < 8.5 || lat > 23.5 || lng < 102.0 || lng > 110.0) {
            console.warn(`Coordinates out of Vietnam bounds for listing ${lot.id}: lat=${lat}, lng=${lng}`);
            return;
        }

        addedIds.add(lot.id);

        const marker = L.marker([lat, lng], {
            icon: lot.isVip ? iconVip : iconNormal,
            title: lot.name || lot.title || `Tin đăng ${lot.id}`,
            riseOnHover: true,
            zIndexOffset: lot.isVip ? 1000 : 500
        }).addTo(currentMap);

        // Store lot ID in marker for easy lookup
        marker.lotId = lot.id;

        marker.bindPopup(popupTemplate(lot), {
            maxWidth: 250,
            className: 'custom-popup',
            closeButton: true,
            autoPan: true,
            autoPanPadding: [50, 50]
        });

        marker.on("click", async () => {
            // Remove active state from previous marker
            if (activeMarker && activeMarker !== marker) {
                const prevIcon = activeMarker.options.icon;
                if (prevIcon && prevIcon.options && prevIcon.options.html) {
                    const prevHtml = prevIcon.options.html;
                    // Remove active class if exists
                    const updatedHtml = prevHtml.replace(/lot-marker-active/g, '').trim();
                    if (updatedHtml !== prevHtml) {
                        activeMarker.setIcon(L.divIcon({
                            ...prevIcon.options,
                            html: updatedHtml
                        }));
                    }
                }
            }

            // Add active state to current marker
            const currentIcon = marker.options.icon;
            if (currentIcon && currentIcon.options && currentIcon.options.html) {
                let currentHtml = currentIcon.options.html;
                if (!currentHtml.includes('lot-marker-active')) {
                    currentHtml = currentHtml.replace('lot-marker', 'lot-marker lot-marker-active');
                    marker.setIcon(L.divIcon({
                        ...currentIcon.options,
                        html: currentHtml
                    }));
                }
            }
            activeMarker = marker;

            // Zoom to marker location
            currentMap.setView([lot.lat, lot.lng], Math.max(currentMap.getZoom(), 16), {
                animate: true,
                duration: 0.5
            });

            // Load full detail and update right panel
            await loadListingDetail(lot.id);
            const fullLot = lots.find(l => l.id === lot.id) || lot;
            await updateDetail(fullLot);

            // Scroll to detail panel on mobile
            if (window.innerWidth < 768) {
                document.getElementById('detail-panel')?.scrollIntoView({ behavior: 'smooth' });
            }
        });

        marker.on("popupopen", () => {
            const btn = document.querySelector(`[data-view-lot="${lot.id}"]`);
            if (btn) {
                btn.onclick = async () => {
                    await loadListingDetail(lot.id);
                    const fullLot = lots.find(l => l.id === lot.id) || lot;
                    await updateDetail(fullLot);
                };
            }
        });

        markerLayers.push(marker);
    });

    // Store markers globally so map-area can clear them
    window.markerLayers = markerLayers;

    // Fit map to show all markers if there are any
    if (markerLayers.length > 0) {
        // Calculate bounds from all markers
        const group = new L.featureGroup(markerLayers);
        const bounds = group.getBounds();

        // Fit map to show all markers with padding
        currentMap.fitBounds(bounds, {
            padding: [50, 50], // Padding in pixels
            maxZoom: 15, // Don't zoom in too much
            animate: true,
            duration: 0.8
        });

        // KHÔNG highlight tất cả markers khi load - chỉ highlight khi user click
    }
}

function popupTemplate(lot) {
    const imageUrl = lot.img || '/images/Image-not-found.png';
    const address = lot.address || lot.district || '';
    const cityDistrict = [lot.district, lot.city].filter(Boolean).join(', ');
    const isVip = lot.isVip || false;

    return `
        <div style="width:260px; font-family: 'SF Pro Display', sans-serif; background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.12);">
            <div style="position: relative;">
                ${isVip ? `<div style="position: absolute; top: 10px; right: 10px; z-index: 10; background: linear-gradient(135deg, #f4b400 0%, #ffd700 100%); color: #fff; font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 20px; box-shadow: 0 2px 8px rgba(244,180,0,0.4); text-transform: uppercase; letter-spacing: 0.5px;">
                    <i class="bi bi-star-fill" style="font-size: 9px;"></i> VIP
                </div>` : ''}
                <img src="${imageUrl}" style="width:100%; height:140px; object-fit:cover; display:block; transition: transform 0.3s;" onerror="this.src='/images/Image-not-found.png'" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, transparent 100%); padding: 12px;">
                    <div style="color: #fff; font-weight: 800; font-size: 18px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                        ${lot.price}
                    </div>
                </div>
            </div>
            <div style="padding: 14px;">
                <div style="font-weight: 700; font-size: 16px; color: #1a202c; margin-bottom: 8px; line-height: 1.3; display: flex; align-items: center; gap: 6px;">
                    <span style="color: #335793;">${lot.price}</span>
                    <span style="color: #6c757d; font-weight: 500;">•</span>
                    <span style="color: #335793;">${lot.size}</span>
                </div>
                ${lot.type ? `<div style="display: inline-flex; align-items: center; gap: 4px; background: linear-gradient(135deg, rgba(51,87,147,0.1) 0%, rgba(74,107,168,0.08) 100%); color: #335793; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 20px; margin-bottom: 8px;">
                    <i class="bi bi-tag-fill" style="font-size: 10px;"></i> ${lot.type}
                </div>` : ''}
                ${address ? `<div style="color: #4a5568; font-size: 12px; margin-bottom: 12px; line-height: 1.5; display: flex; align-items: start; gap: 6px; padding: 8px; background: rgba(51,87,147,0.03); border-radius: 8px;">
                    <i class="bi bi-geo-alt-fill" style="font-size: 13px; color: #335793; margin-top: 2px; flex-shrink: 0;"></i>
                    <span style="flex: 1;">${address}${cityDistrict ? ', ' + cityDistrict : ''}</span>
                </div>` : ''}
                <a href="/tin-dang/${lot.slug || lot.id}" class="btn btn-primary btn-sm w-100" data-view-lot="${lot.id}" style="background: linear-gradient(135deg, #335793 0%, #4a6ba8 100%); border: none; font-size: 13px; font-weight: 600; padding: 10px 16px; border-radius: 8px; box-shadow: 0 4px 12px rgba(51,87,147,0.3); transition: all 0.3s; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(51,87,147,0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(51,87,147,0.3)'">
                    <i class="bi bi-eye"></i> Xem chi tiết
                </a>
            </div>
        </div>
    `;
}

// ===== MINI MAP =====
function updateMiniMap(lot) {
    const miniMapEl = document.getElementById('mini-map');
    if (!miniMapEl) {
        console.warn('Element #mini-map not found');
        return;
    }

    if (!lot || !lot.lat || !lot.lng) {
        console.warn('Invalid lot data for mini map');
        return;
    }

    if (typeof L === 'undefined') {
        console.warn('Leaflet not loaded');
        return;
    }

    if (!miniMap) {
        miniMap = L.map('mini-map', {
            zoomControl: false,
            attributionControl: false
        }).setView([lot.lat, lot.lng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(miniMap);
    } else {
        miniMap.setView([lot.lat, lot.lng], 16);
    }

    if (miniMarker) {
        miniMap.removeLayer(miniMarker);
    }
    miniMarker = L.marker([lot.lat, lot.lng], { icon: lot.isVip ? iconVip : iconNormal }).addTo(miniMap);
}

// ===== UPDATE RIGHT PANEL =====
async function updateDetail(lot) {
    // Show skeleton loader while loading
    const skeleton = document.getElementById('detail-panel-skeleton');
    const emptyState = document.getElementById('detail-panel-empty');
    const detailContent = document.getElementById('detail-panel-content');

    if (skeleton) skeleton.style.display = 'block';
    if (emptyState) emptyState.style.display = 'none';
    if (detailContent) detailContent.style.display = 'none';

    // Load full detail if not loaded yet
    if (!lot.desc && lot.id) {
        await loadListingDetail(lot.id);
        lot = currentListing || lot;
    }

    // Hide skeleton and show detail content
    if (skeleton) skeleton.style.display = 'none';
    if (emptyState) emptyState.style.display = 'none';
    if (detailContent) detailContent.style.display = 'block';

    setGallery(lot);

    // Update price and size
    const priceEl = document.getElementById("lot-price");
    if (priceEl) {
        priceEl.innerHTML = `${lot.price} • ${lot.size}`;
    }

    // Update address
    const addressEl = document.getElementById("lot-address");
    if (addressEl) {
        const span = addressEl.querySelector('span');
        if (span) span.textContent = lot.address;
    }

    // Update type
    const typeEl = document.getElementById("lot-type");
    if (typeEl) typeEl.textContent = lot.type || "Đất";

    // Update details
    const legalEl = document.getElementById("lot-legal");
    if (legalEl) legalEl.textContent = lot.legal || "Đang cập nhật";

    const frontEl = document.getElementById("lot-front");
    if (frontEl) frontEl.textContent = lot.front || "Đang cập nhật";

    const roadEl = document.getElementById("lot-road");
    if (roadEl) roadEl.textContent = lot.road || "Đang cập nhật";

    const depthEl = document.getElementById("lot-depth");
    if (depthEl) depthEl.textContent = lot.depth || "Đang cập nhật";

    const directionEl = document.getElementById("lot-direction");
    if (directionEl) directionEl.textContent = lot.direction || "Đang cập nhật";

    const pricePerEl = document.getElementById("lot-price-per");
    if (pricePerEl) pricePerEl.textContent = lot.pricePer || "Đang cập nhật";

    // Update description
    const descEl = document.getElementById("lot-desc");
    const descContainer = document.getElementById("lot-desc-container");
    if (descEl && lot.desc) {
        descEl.textContent = lot.desc;
        if (descContainer) descContainer.style.display = 'block';
    } else if (descContainer) {
        descContainer.style.display = 'none';
    }

    // Update seller info
    const sellerNameEl = document.getElementById("seller-name");
    if (sellerNameEl) sellerNameEl.textContent = lot.seller?.name || "Đang cập nhật";

    const sellerPhoneEl = document.getElementById("seller-phone");
    if (sellerPhoneEl) sellerPhoneEl.textContent = lot.seller?.phone || "Đang cập nhật";

    // Update action buttons
    const callBtn = document.getElementById("btn-call");
    if (callBtn && lot.seller?.phone) {
        callBtn.href = `tel:${lot.seller.phone}`;
    }

    const zaloBtn = document.getElementById("btn-zalo");
    if (zaloBtn && lot.seller?.zalo) {
        zaloBtn.href = `https://zalo.me/${lot.seller.zalo}`;
        zaloBtn.style.display = 'flex';
    } else if (zaloBtn) {
        zaloBtn.style.display = 'none';
    }

    const viewDetailBtn = document.getElementById("btn-view-detail");
    if (viewDetailBtn && lot.slug) {
        viewDetailBtn.href = `/tin-dang/${lot.slug}`;
    } else if (viewDetailBtn && lot.id) {
        viewDetailBtn.href = `/tin-dang/${lot.id}`;
    }

    // Update favorite button
    const favBtn = document.getElementById("favorite-btn");
    if (favBtn && lot.id) {
        favBtn.setAttribute('data-listing-id', lot.id);
    }

    renderTags(lot.tags);
    renderSimilar(lot.id);
    updateMiniMap(lot);
    drawPolygon(lot);

    // Store current listing for favorite
    window.currentListingId = lot.id;
}

function setGallery(lot) {
    const main = document.getElementById("lot-main-img");
    if (!main) return;

    const thumbsWrap = document.getElementById("lot-thumbs");
    const imgs = lot.images && lot.images.length ? lot.images : [lot.img || '/images/Image-not-found.png'];

    // Set main image
    main.src = imgs[0];
    main.alt = lot.name || lot.title || 'Hình ảnh';
    main.style.cursor = 'pointer';
    main.onerror = function() {
        this.src = '/images/Image-not-found.png';
    };
    // Add click handler for lightbox
    main.onclick = function() {
        if (typeof openImageModal === 'function') {
            openImageModal(this.src);
        }
    };

    // Clear and set thumbnails
    if (thumbsWrap) {
        thumbsWrap.innerHTML = "";
        imgs.slice(0, 4).forEach((url, idx) => {
            const btn = document.createElement("button");
            btn.className = `thumb ${idx === 0 ? "active" : ""}`;
            const thumbImg = document.createElement("img");
            thumbImg.src = url;
            thumbImg.alt = "thumb";
            thumbImg.onerror = function() {
                this.src = '/images/Image-not-found.png';
            };
            btn.appendChild(thumbImg);
            btn.onclick = () => {
                main.src = url;
                thumbsWrap.querySelectorAll(".thumb").forEach(t => t.classList.remove("active"));
                btn.classList.add("active");
                // Update main image click handler
                main.onclick = function() {
                    if (typeof openImageModal === 'function') {
                        openImageModal(this.src);
                    }
                };
            };
            thumbsWrap.appendChild(btn);
        });
    }
}

function renderTags(tags = []) {
    const wrapper = document.getElementById("lot-tags");
    if (!wrapper) {
        console.warn('Element #lot-tags not found');
        return;
    }
    wrapper.innerHTML = "";
    tags.forEach(t => {
        wrapper.innerHTML += `<span class="badge rounded-pill bg-primary-subtle text-primary">${t}</span>`;
    });
}

// ===== RENDER SIMILAR LIST =====
function renderSimilar(activeId) {
    const list = document.getElementById("similar-list");
    if (!list) return;

    list.innerHTML = "";

    const similar = lots.filter(l => l.id !== activeId).slice(0, 5);

    if (similar.length === 0) {
        list.innerHTML = '<div class="text-muted small text-center py-3">Chưa có tin tương tự</div>';
        return;
    }

    similar.forEach(lot => {
        const item = document.createElement("div");
        item.className = "similar-item";
        item.style.cursor = "pointer";
        item.innerHTML = `
            <img src="${lot.img || '/images/Image-not-found.png'}" onerror="this.src='/images/Image-not-found.png'">
            <div class="flex-grow-1">
                <div class="fw-bold">${lot.price}</div>
                <div class="text-muted small">${lot.size} • ${lot.type || ''}</div>
                <a href="/tin-dang/${lot.slug || lot.id}" class="btn btn-outline-primary btn-sm mt-1">Xem chi tiết</a>
            </div>
        `;
        item.onclick = () => {
            if (lot.slug) {
                window.location.href = `/tin-dang/${lot.slug}`;
            } else {
                flyToLot(lot.id);
            }
        };
        list.appendChild(item);
    });
}

// ===== VIP CAROUSEL =====
async function renderVipCarousel() {
    const wrap = document.getElementById("vip-carousel");
    if (!wrap) return;

    // Load VIP listings
    try {
        const response = await fetch('/api/listings/map?category=vip');
        const data = await response.json();
        const vipListings = data.listings.filter(l => l.is_vip).slice(0, 10);

        wrap.innerHTML = "";

        if (vipListings.length === 0) {
            wrap.innerHTML = '<div class="text-center text-muted py-4 w-100"><i class="bi bi-inbox" style="font-size: 48px;"></i><p class="mt-2">Chưa có tin VIP nào</p></div>';
            return;
        }

        vipListings.forEach(listing => {
            const lot = {
                id: listing.id,
                name: listing.title,
                price: formatPrice(listing.price),
                size: `${formatNumberJS(listing.area)}m²`,
                img: listing.image ? (listing.image.startsWith('http') ? listing.image : `/storage/${listing.image}`) : '/images/Image-not-found.png',
                type: listing.category || '',
                address: listing.address,
                tags: [],
                seller: { name: '', phone: '' },
                slug: listing.slug
            };
            const card = document.createElement("div");
            card.className = "vip-card";

            // Tạo tags HTML
            const tagsHtml = lot.tags.slice(0, 3).map(tag =>
                `<span class="badge badge-vip-card">${tag}</span>`
            ).join('');

            card.innerHTML = `
            <div class="vip-badge-top">
                <span class="vip-label">
                    <i class="bi bi-star-fill"></i> VIP
                </span>
            </div>
            <div class="vip-card-image-wrapper">
                <img src="${lot.img}" alt="${lot.name}">
                <div class="vip-card-overlay">
                    <span class="vip-price-badge">${lot.price}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="vip-card-header">
                    <h6 class="vip-card-title mb-1">${lot.name}</h6>
                    <div class="vip-card-meta mb-2">
                        <span class="vip-meta-item">
                            <i class="bi bi-rulers"></i> ${lot.size}
                        </span>
                        <span class="vip-meta-item">
                            <i class="bi bi-tag"></i> ${lot.type}
                        </span>
                    </div>
                </div>

                <div class="vip-card-address mb-2">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span class="text-truncate d-inline-block" style="max-width: 200px;" title="${lot.address}">
                        ${lot.address}
                    </span>
                </div>

                <div class="vip-card-tags mb-2">
                    ${tagsHtml}
                </div>

                <div class="vip-card-details mb-3">
                    <div class="vip-detail-row">
                        <span class="vip-detail-item">
                            <i class="bi bi-file-earmark-check"></i> ${lot.legal || 'Đang cập nhật'}
                        </span>
                        <span class="vip-detail-item">
                            <i class="bi bi-arrows-expand"></i> MT: ${lot.front || 'N/A'}
                        </span>
                    </div>
                    <div class="vip-detail-row">
                        <span class="vip-detail-item">
                            <i class="bi bi-road"></i> ${lot.road || 'Đang cập nhật'}
                        </span>
                        <span class="vip-detail-item">
                            <i class="bi bi-currency-dollar"></i> ${lot.pricePer || 'N/A'}
                        </span>
                    </div>
                </div>

                <div class="vip-card-seller mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="vip-seller-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="vip-seller-name">${lot.seller.name}</div>
                            <div class="vip-seller-phone">
                                <i class="bi bi-telephone"></i> ${lot.seller.phone}
                            </div>
                        </div>
                    </div>
                </div>

                <a href="/tin-dang/${lot.slug || lot.id}" class="btn btn-primary btn-sm w-100 vip-card-btn">
                    <i class="bi bi-map"></i> Xem chi tiết
                </a>
            </div>
        `;
            wrap.appendChild(card);
        });
    } catch (error) {
        console.error('Error loading VIP listings:', error);
        wrap.innerHTML = '<div class="text-center text-muted py-4 w-100">Lỗi tải dữ liệu</div>';
    }
}

// ===== LOAD DATA FROM API =====
// Debounce timer for loadListings
let loadListingsTimer = null;

// Global variable to track queued loadListings calls
let loadListingsQueue = null;

async function loadListings(filters = {}) {
    // Prevent multiple simultaneous calls
    if (loadingListings) {
        // If already loading, queue this call (only one queue)
        if (!loadListingsQueue) {
            loadListingsQueue = new Promise((resolve) => {
                const checkLoading = setInterval(() => {
                    if (!loadingListings) {
                        clearInterval(checkLoading);
                        loadListingsQueue = null;
                        loadListings(filters).then(resolve);
                    }
                }, 200); // Increase interval to 200ms to reduce checks
            });
        }
        return loadListingsQueue;
    }

    loadingListings = true;

    // Show loading state on map (optional - can add overlay)
    const mapElement = document.getElementById('map');
    if (mapElement) {
        mapElement.style.opacity = '0.7';
    }

    try {
        const params = new URLSearchParams();

        // If filters object is empty, read from URL parameters
        if (Object.keys(filters).length === 0) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.forEach((value, key) => {
                if (['city', 'category', 'min_price', 'max_price', 'vip', 'legal_status'].includes(key)) {
                    params.append(key, value);
                }
            });
        } else {
            // Add filter params from filters object
            if (filters.city) params.append('city', filters.city);
            if (filters.district) params.append('district', filters.district);
            if (filters.category) params.append('category', filters.category);
            if (filters.minPrice) params.append('min_price', filters.minPrice);
            if (filters.maxPrice) params.append('max_price', filters.maxPrice);
            if (filters.maxArea) params.append('max_area', filters.maxArea);
            if (filters.hasRoad) params.append('has_road', '1');
            if (filters.vip) params.append('vip', filters.vip);
            if (filters.legalStatus) params.append('legal_status', filters.legalStatus);
        }

        // KHÔNG gửi bounds khi filter - chỉ lọc theo toàn bộ Việt Nam
        // Chỉ gửi bounds khi người dùng di chuyển bản đồ (map moveend)
        // if (map && filters.useBounds) {
        //     const bounds = map.getBounds();
        //     params.append('bounds[north]', bounds.getNorth());
        //     params.append('bounds[south]', bounds.getSouth());
        //     params.append('bounds[east]', bounds.getEast());
        //     params.append('bounds[west]', bounds.getWest());
        // }

        const url = `/api/listings/map${params.toString() ? '?' + params.toString() : ''}`;
        const response = await fetch(url);
        const data = await response.json();

        // Convert API data to app format with validation
        lots = data.listings
            .filter(listing => {
                // Validate coordinates
                if (!listing.latitude || !listing.longitude) {
                    console.warn(`Listing ${listing.id} missing coordinates`);
                    return false;
                }
                const lat = parseFloat(listing.latitude);
                const lng = parseFloat(listing.longitude);
                if (isNaN(lat) || isNaN(lng)) {
                    console.warn(`Listing ${listing.id} has invalid coordinates`);
                    return false;
                }
                // Validate Vietnam bounds
                if (lat < 8.5 || lat > 23.5 || lng < 102.0 || lng > 110.0) {
                    console.warn(`Listing ${listing.id} coordinates out of bounds`);
                    return false;
                }
                return true;
            })
            .map(listing => ({
                id: listing.id,
                name: listing.title,
                title: listing.title,
                price: formatPrice(listing.price),
                size: `${formatNumberJS(listing.area)}m²`,
                priceValue: listing.price,
                sizeValue: listing.area,
                lat: parseFloat(listing.latitude),
                lng: parseFloat(listing.longitude),
                slug: listing.slug,
                img: listing.image ? (listing.image.startsWith('http') ? listing.image : `/storage/${listing.image}`) : '/images/Image-not-found.png',
                type: listing.category || 'Đất',
                address: listing.address || '',
                city: listing.city || '',
                district: listing.district || '',
                tags: [], // Will be loaded from detail
                seller: { name: '', phone: '' }, // Will be loaded from detail
                isVip: listing.is_vip || false,
                legal: '',
                front: '',
                road: '',
                depth: '',
                roadWidth: '',
                direction: '',
                roadAccess: false,
                pricePer: '',
                planning: '',
                depositOnline: '',
                desc: '',
                images: [],
                polygon: []
            }));

        if (lots.length > 0) {
            renderMarkers(lots);
            // Load first listing detail only if not already loading and detail panel exists
            const detailPanel = document.getElementById('detail-panel');
            if (detailPanel && !isLoadingDetail && lots[0] && lots[0].id) {
                // Only load detail if we don't already have it or if it's different
                if (!currentListing || currentListing.id !== lots[0].id) {
                    await loadListingDetail(lots[0].id);
                }
            }
        } else {
            markerLayers.forEach(m => map.removeLayer(m));
            markerLayers = [];
            const urlParams = new URLSearchParams(window.location.search);
            const filterParams = ['city', 'district', 'category', 'max_price', 'max_area', 'has_road', 'vip'];
            const hasUrlFilters = filterParams.some(param => urlParams.has(param));
            const hasAppliedFilters = Object.keys(filters).length > 0;

            const isInitialLoad = !hasUrlFilters && !hasAppliedFilters;

            if (!isInitialLoad && window.showToast) {
                window.showToast('Không tìm thấy tin đăng nào phù hợp', 'info', 3000);
            }
        }
    } catch (error) {
        console.error('Error loading listings:', error);
        if (window.showToast) {
            window.showToast('Có lỗi xảy ra khi tải dữ liệu', 'error', 3000);
        }
    } finally {
        loadingListings = false;
        const mapElement = document.getElementById('map');
        if (mapElement) {
            mapElement.style.opacity = '1';
        }
    }
}

// Load listing detail from API
async function loadListingDetail(listingId) {
    // Prevent multiple simultaneous calls for the same listing
    if (isLoadingDetail) {
        // If already loading, check if it's the same listing
        if (loadingDetailQueue.includes(listingId)) {
            return; // Already queued
        }
        loadingDetailQueue.push(listingId);
        // Wait for current load to finish
        return new Promise((resolve) => {
            const checkLoading = setInterval(() => {
                if (!isLoadingDetail) {
                    clearInterval(checkLoading);
                    const index = loadingDetailQueue.indexOf(listingId);
                    if (index > -1) {
                        loadingDetailQueue.splice(index, 1);
                    }
                    loadListingDetail(listingId).then(resolve);
                }
            }, 100);
        });
    }

    // Skip if already have this listing detail loaded
    if (currentListing && currentListing.id === listingId && currentListing.desc) {
        return;
    }

    isLoadingDetail = true;

    try {
        const response = await fetch(`/api/listings/${listingId}`);
        const data = await response.json();
        const listing = data.listing;

        const lotIndex = lots.findIndex(l => l.id === listingId);
        if (lotIndex === -1) return;

        const lot = lots[lotIndex];
        lot.name = listing.title;
        lot.desc = listing.description || '';
        lot.legal = listing.legal_status || '';
        lot.front = listing.front_width ? `${formatNumberJS(listing.front_width)}m` : '';
        lot.road = listing.road_type || '';
        lot.depth = listing.depth ? `${formatNumberJS(listing.depth)}m` : '';
        lot.roadWidth = listing.road_width ? `${formatNumberJS(listing.road_width)}m` : '';
        lot.direction = listing.direction || '';
        lot.roadAccess = listing.has_road_access || false;
        lot.price = formatPrice(listing.price);
        // Format price per m2 - convert to triệu/m² if needed
        if (listing.price_per_m2) {
            let pricePerM2 = parseFloat(listing.price_per_m2);
            // Convert to triệu/m² if price_per_m2 is in đồng/m²
            if (pricePerM2 >= 1000000) {
                pricePerM2 = pricePerM2 / 1000000;
            }
            lot.pricePer = `${formatNumberJS(pricePerM2)} triệu/m²`;
        } else if (listing.price && listing.area && listing.area > 0) {
            // Calculate from price and area
            let pricePerM2 = parseFloat(listing.price) / parseFloat(listing.area);
            if (pricePerM2 >= 1000000) {
                pricePerM2 = pricePerM2 / 1000000;
            }
            lot.pricePer = `${formatNumberJS(pricePerM2)} triệu/m²`;
        } else {
            lot.pricePer = '';
        }
        lot.planning = listing.planning_info || '';
        lot.depositOnline = listing.deposit_online ? 'Có' : 'Không';
        lot.tags = listing.tags && Array.isArray(listing.tags) ? listing.tags : [];
        lot.seller = {
            name: listing.contact_name || '',
            phone: listing.contact_phone || ''
        };
        lot.images = listing.images && listing.images.length > 0
            ? listing.images.map(img => img.image_path.startsWith('http') ? img.image_path : `/storage/${img.image_path}`)
            : [lot.img];
        lot.polygon = listing.polygon_coordinates && Array.isArray(listing.polygon_coordinates)
            ? listing.polygon_coordinates
            : [];

        // Update current listing
        currentListing = lot;

        // Update UI if this is the active listing
        if (document.querySelector('.lot-price')) {
            updateDetail(lot);
        }
    } catch (error) {
        console.error('Error loading listing detail:', error);
    } finally {
        isLoadingDetail = false;
        // Remove this listingId from queue
        const index = loadingDetailQueue.indexOf(listingId);
        if (index > -1) {
            loadingDetailQueue.splice(index, 1);
        }
    }
}

// Helper function to format number without .0
function formatNumberJS(num) {
    if (num === null || num === undefined || isNaN(num)) return '0';
    const numFloat = parseFloat(num);
    // Remove trailing .0 or .00
    return numFloat.toString().replace(/\.0+$/, '');
}

function formatPrice(price) {
    // Format price in a user-friendly way (đồng nhất với PHP helper)
    if (!price || price <= 0) {
        return 'Liên hệ';
    }

    // Convert to triệu if price is in đồng (VND)
    let priceInMillion = price >= 1000000 ? price / 1000000 : price;

    // Format based on value - Rule: < 1 tỉ hiển thị triệu, >= 1 tỉ hiển thị tỉ
    if (priceInMillion >= 1000) {
        // >= 1000 triệu (>= 1 tỉ) → hiển thị theo tỉ
        const ty = priceInMillion / 1000;
        if (ty === Math.floor(ty)) {
            return new Intl.NumberFormat('vi-VN').format(ty) + ' tỉ';
        } else {
            // Làm tròn đến 1 chữ số thập phân, sau đó bỏ .0 nếu không cần
            const tyRounded = Math.round(ty * 10) / 10;
            let formatted = new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(tyRounded);
            formatted = formatted.replace(/\.0+$/, '');
            return formatted + ' tỉ';
        }
    } else {
        // < 1000 triệu (< 1 tỉ) → hiển thị theo triệu
        if (priceInMillion === Math.floor(priceInMillion)) {
            return new Intl.NumberFormat('vi-VN').format(priceInMillion) + ' triệu';
        } else {
            // Làm tròn đến 1 chữ số thập phân, sau đó bỏ .0 nếu không cần
            const priceRounded = Math.round(priceInMillion * 10) / 10;
            let formatted = new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(priceRounded);
            formatted = formatted.replace(/\.0+$/, '');
            return formatted + ' triệu';
        }
    }
}

// ===== FILTERS =====
const filterTypeEl = document.getElementById("filter-type");
const filterCityEl = document.getElementById("filter-city");
const filterDistrictEl = document.getElementById("filter-district");
const filterPriceEl = document.getElementById("filter-price");
const filterAreaEl = document.getElementById("filter-area");
const filterRoadEl = document.getElementById("filter-road");
const priceLabel = document.getElementById("price-label");
const areaLabel = document.getElementById("area-label");

async function applyFilters(e) {
    // Prevent form submission if event is passed
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    const category = filterTypeEl?.value || '';
    const city = filterCityEl?.value || '';
    const district = filterDistrictEl?.value || '';
    const priceMax = Number(filterPriceEl?.value || 5000);
    const areaMax = Number(filterAreaEl?.value || 1000);
    const needRoad = filterRoadEl?.checked || false;

    const filters = {};
    if (category) filters.category = category;
    if (city) filters.city = city;
    if (district) filters.district = district;

    // Convert triệu đồng to VND (đồng) for API
    // Only apply filter if value is less than max (meaning user changed it)
    if (priceMax && priceMax < 5000) {
        filters.maxPrice = priceMax * 1000000; // Convert triệu to đồng
    }
    if (areaMax && areaMax < 1000) {
        filters.maxArea = areaMax;
    }
    if (needRoad) filters.hasRoad = true;

    await loadListings(filters);

    // Update URL without reloading page
    const params = new URLSearchParams();
    if (category) params.append('category', category);
    if (city) params.append('city', city);
    if (district) params.append('district', district);
    // Only add to URL if not default values
    if (priceMax < 5000) params.append('max_price', priceMax);
    if (areaMax < 1000) params.append('max_area', areaMax);
    if (needRoad) params.append('has_road', '1');

    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
    window.history.pushState({}, '', newUrl);

    // Update filter count badge
    updateFilterCount();
}

function updateFilterCount() {
    const filterCountEl = document.getElementById('filter-count');
    if (!filterCountEl) return;

    let count = 0;
    if (filterTypeEl?.value) count++;
    if (filterCityEl?.value) count++;
    if (filterDistrictEl?.value) count++;
    if (filterPriceEl?.value && filterPriceEl.value < 5000) count++;
    if (filterAreaEl?.value && filterAreaEl.value < 1000) count++;
    if (filterRoadEl?.checked) count++;

    filterCountEl.textContent = count > 0 ? `${count} tiêu chí` : '0 tiêu chí';
}

function updateRangeLabels() {
    if (priceLabel) {
        const priceValue = parseInt(filterPriceEl.value);
        priceLabel.textContent = `${new Intl.NumberFormat('vi-VN').format(priceValue)} triệu`;
    }
    if (areaLabel) {
        const areaValue = parseInt(filterAreaEl.value);
        areaLabel.textContent = `${formatNumberJS(areaValue)} m²`;
    }
}

// Load districts when city changes
if (filterCityEl && filterDistrictEl) {
    filterCityEl.addEventListener('change', async function() {
        const cityId = this.value;

        if (cityId) {
            try {
                // Disable district select while loading
                filterDistrictEl.disabled = true;
                filterDistrictEl.innerHTML = '<option value="">Đang tải...</option>';

                const response = await fetch(`/api/districts?city_id=${cityId}`);

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                // Clear and populate districts
                filterDistrictEl.innerHTML = '<option value="">Chọn Quận/Huyện</option>';

                if (data.districts && Array.isArray(data.districts)) {
                    data.districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.name;
                        filterDistrictEl.appendChild(option);
                    });
                }

                // Re-enable district select
                filterDistrictEl.disabled = false;

                // Update filter count
                updateFilterCount();
            } catch (error) {
                console.error('Error loading districts:', error);
                filterDistrictEl.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
                filterDistrictEl.disabled = false;
            }
        } else {
            // Clear districts if no city selected
            filterDistrictEl.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            filterDistrictEl.disabled = false;

            // Update filter count
            updateFilterCount();
        }
    });

    // Load districts on page load if city is already selected
    if (filterCityEl.value) {
        filterCityEl.dispatchEvent(new Event('change'));
    }
}

// Update filter count when other filters change
if (filterTypeEl) {
    filterTypeEl.addEventListener('change', updateFilterCount);
}
if (filterPriceEl) {
    filterPriceEl.addEventListener('input', function() {
        updateRangeLabels();
        updateFilterCount();
    });
}
if (filterAreaEl) {
    filterAreaEl.addEventListener('input', function() {
        updateRangeLabels();
        updateFilterCount();
    });
}
if (filterRoadEl) {
    filterRoadEl.addEventListener('change', updateFilterCount);
}

// Initialize filter count on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', updateFilterCount);
} else {
    updateFilterCount();
}

// Only add event listeners if elements exist (not all pages have filters)
const btnApplyFilters = document.getElementById("btn-apply-filters");
const btnNearby = document.getElementById("btn-nearby");
const filterForm = document.getElementById("filter-form");

if (btnApplyFilters) {
    btnApplyFilters.addEventListener("click", applyFilters);
}

// Prevent form submission, use AJAX instead
if (filterForm) {
    filterForm.addEventListener("submit", function(e) {
        e.preventDefault();
        applyFilters(e);
    });
}
if (btnNearby) {
    btnNearby.addEventListener("click", locateUserAndPickNearest);
}

// Mobile nearby button is handled in home.blade.php to avoid duplicate API calls
// Event listener removed to prevent double API calls
if (filterPriceEl) {
    filterPriceEl.addEventListener("input", function() {
        updateRangeLabels();
        updateFilterCount();
        // Update slider progress visual
        const progress = ((this.value - this.min) / (this.max - this.min)) * 100;
        this.style.setProperty('--range-progress', progress + '%');
    });
    // Initialize progress on load
    const priceProgress = ((filterPriceEl.value - filterPriceEl.min) / (filterPriceEl.max - filterPriceEl.min)) * 100;
    filterPriceEl.style.setProperty('--range-progress', priceProgress + '%');
}
if (filterAreaEl) {
    filterAreaEl.addEventListener("input", function() {
        updateRangeLabels();
        updateFilterCount();
        // Update slider progress visual
        const progress = ((this.value - this.min) / (this.max - this.min)) * 100;
        this.style.setProperty('--range-progress', progress + '%');
    });
    // Initialize progress on load
    const areaProgress = ((filterAreaEl.value - filterAreaEl.min) / (filterAreaEl.max - filterAreaEl.min)) * 100;
    filterAreaEl.style.setProperty('--range-progress', areaProgress + '%');
}
if (priceLabel && areaLabel && filterPriceEl && filterAreaEl) {
    updateRangeLabels();
}

// ===== JUMP TO LOT =====
async function flyToLot(id) {
    const lot = lots.find(l => l.id === id);
    if (!lot) {
        // Try to load from API
        await loadListingDetail(id);
        const loadedLot = lots.find(l => l.id === id);
        if (loadedLot) {
            map.setView([loadedLot.lat, loadedLot.lng], 17);
            updateDetail(loadedLot);
        }
        return;
    }
    map.setView([lot.lat, lot.lng], 17);
    await updateDetail(lot);
}

// ===== FAVORITE BUTTON =====
// Removed: onclick handler is already set in detail-panel.blade.php
// Không override onclick handler để tránh conflict với toggleFavorite function

// Initialize: Load listings on page load
loadListings().then(() => {
    // Only update detail if detail panel exists
    const detailPanel = document.getElementById('detail-panel');
    if (lots.length > 0 && detailPanel) {
        updateDetail(lots[0]);
    }
});

// KHÔNG reload listings khi map bounds change - chỉ lọc theo toàn bộ Việt Nam
// Nếu muốn reload khi di chuyển bản đồ, uncomment code bên dưới
// if (map) {
//     map.on('moveend', function() {
//         // Debounce to avoid too many requests
//         clearTimeout(window.mapReloadTimer);
//         window.mapReloadTimer = setTimeout(() => {
//             loadListings();
//         }, 500);
//     });
// } else if (window.mainMap) {
//     // Use global map if available
//     window.mainMap.on('moveend', function() {
//         clearTimeout(window.mapReloadTimer);
//         window.mapReloadTimer = setTimeout(() => {
//             loadListings();
//         }, 500);
//     });
// }

// Expose functions globally for use in other scripts
window.flyToLot = flyToLot;
window.loadListingDetail = loadListingDetail;
window.updateDetail = updateDetail;
window.lots = lots;
window.renderMarkers = renderMarkers;

// ===== POLYGON DRAW =====
function drawPolygon(lot) {
    if (!lot) {
        console.warn('Invalid lot data for polygon');
        return;
    }

    const currentMap = map || window.mainMap;
    if (!currentMap) {
        console.warn('Map not available for polygon');
        return;
    }

    if (typeof L === 'undefined') {
        console.warn('Leaflet not loaded');
        return;
    }

    if (activePolygon) {
        currentMap.removeLayer(activePolygon);
        activePolygon = null;
    }
    if (miniPolygon && miniMap) {
        miniMap.removeLayer(miniPolygon);
        miniPolygon = null;
    }

    if (lot.polygon && Array.isArray(lot.polygon) && lot.polygon.length > 0) {
        try {
            activePolygon = L.polygon(lot.polygon, {
                color: "#7c3aed",
                weight: 2,
                fillColor: "#a855f7",
                fillOpacity: 0.2,
                dashArray: "6 4"
            }).addTo(currentMap);
            currentMap.fitBounds(activePolygon.getBounds(), { maxZoom: 18, padding: [40, 40] });

            if (miniMap) {
                miniPolygon = L.polygon(lot.polygon, {
                    color: "#7c3aed",
                    weight: 2,
                    fillColor: "#c084fc",
                    fillOpacity: 0.25,
                    dashArray: "4 3"
                }).addTo(miniMap);
            }
        } catch (error) {
            console.error('Error drawing polygon:', error);
        }
    }
}

// ===== GEOLOCATION & NEAREST =====
function getDistanceKm(lat1, lng1, lat2, lng2) {
    const toRad = deg => (deg * Math.PI) / 180;
    const R = 6371;
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLng / 2) * Math.sin(dLng / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function findNearestLots(lat, lng) {
    return lots
        .map(l => ({ ...l, distance: getDistanceKm(lat, lng, l.lat, l.lng) }))
        .sort((a, b) => a.distance - b.distance);
}

function markUserLocation(lat, lng) {
    if (userMarker) map.removeLayer(userMarker);
    if (userCircle) map.removeLayer(userCircle);

    userMarker = L.marker([lat, lng], {
        icon: L.divIcon({
            className: "",
            html: '<div class="map-pin" style="background:#0ea5e9;"></div>',
            iconSize: [18, 18],
            iconAnchor: [9, 18],
        })
    }).addTo(map);

    userCircle = L.circle([lat, lng], {
        radius: 200,
        color: "#0ea5e9",
        fillColor: "#0ea5e9",
        fillOpacity: 0.08,
        weight: 1
    }).addTo(map);
}

async function locateUserAndPickNearest() {
    if (!navigator.geolocation) {
        if (window.showToast) {
            window.showToast('Trình duyệt của bạn không hỗ trợ định vị', 'warning', 3000);
        }
        return;
    }

    const currentMap = map || window.mainMap;
    if (!currentMap) return;

    // Show loading state
    if (window.showToast) {
        window.showToast('Đang tìm vị trí của bạn...', 'info', 2000);
    }

    navigator.geolocation.getCurrentPosition(
        async (pos) => {
            const { latitude, longitude } = pos.coords;

            // Mark user location on map
            markUserLocation(latitude, longitude);

            // Use current map bounds or no bounds limit (get all listings)
            // Include existing filters from URL
            const params = new URLSearchParams();
            const urlParams = new URLSearchParams(window.location.search);
            ['city', 'category', 'min_price', 'max_price', 'vip', 'legal_status'].forEach(key => {
                if (urlParams.has(key)) {
                    params.append(key, urlParams.get(key));
                }
            });

            // Fetch listings from API (no bounds limit - get all)
            try {
                const response = await fetch(`/api/listings/map?${params.toString()}`);
                const data = await response.json();

                if (data.listings && data.listings.length > 0) {
                    // Convert API data to app format
                    const nearbyLots = data.listings
                        .filter(listing => {
                            if (!listing.latitude || !listing.longitude) return false;
                            const lat = parseFloat(listing.latitude);
                            const lng = parseFloat(listing.longitude);
                            return !isNaN(lat) && !isNaN(lng) &&
                                lat >= 8.5 && lat <= 23.5 &&
                                lng >= 102.0 && lng <= 110.0;
                        })
                        .map(listing => ({
                            id: listing.id,
                            name: listing.title,
                            title: listing.title,
                            price: formatPrice(listing.price),
                            size: `${formatNumberJS(listing.area)}m²`,
                            priceValue: listing.price,
                            sizeValue: listing.area,
                            lat: parseFloat(listing.latitude),
                            lng: parseFloat(listing.longitude),
                            slug: listing.slug,
                            img: listing.image ? (listing.image.startsWith('http') ? listing.image : `/storage/${listing.image}`) : '/images/Image-not-found.png',
                            type: listing.category || 'Đất',
                            address: listing.address || '',
                            city: listing.city || '',
                            district: listing.district || '',
                            tags: [],
                            seller: { name: '', phone: '' },
                            isVip: listing.is_vip || false,
                            legal: '',
                            front: '',
                            road: '',
                            depth: '',
                            roadWidth: '',
                            direction: '',
                            roadAccess: false,
                            pricePer: '',
                            planning: '',
                            depositOnline: '',
                            desc: '',
                            images: [],
                            polygon: []
                        }));

                    // Update lots array with listings
                    lots = nearbyLots;

                    // Render markers on map
                    renderMarkers(nearbyLots);

                    // Center map on user location
                    currentMap.setView([latitude, longitude], 15, {
                        animate: true,
                        duration: 0.5
                    });

                    if (window.showToast) {
                        window.showToast(`Tìm thấy ${nearbyLots.length} tin đăng`, 'success', 2000);
                    }
                } else {
                    // No listings found
                    currentMap.setView([latitude, longitude], 15, {
                        animate: true,
                        duration: 0.5
                    });
                    if (window.showToast) {
                        window.showToast('Không tìm thấy tin đăng nào', 'info', 3000);
                    }
                }
            } catch (error) {
                console.error('Error loading listings:', error);
                if (window.showToast) {
                    window.showToast('Có lỗi xảy ra khi tải tin đăng', 'error', 3000);
                }
                // Fallback: center on user location
                currentMap.setView([latitude, longitude], 15, {
                    animate: true,
                    duration: 0.5
                });
            }
        },
        (error) => {
            console.error('Geolocation error:', error);
            if (window.showToast) {
                let message = 'Không thể lấy vị trí của bạn';
                if (error.code === 1 || error.code === error.PERMISSION_DENIED) {
                    message = 'Vui lòng cho phép truy cập vị trí trong cài đặt trình duyệt để sử dụng tính năng này';
                } else if (error.code === 2 || error.code === error.POSITION_UNAVAILABLE) {
                    message = 'Không thể xác định vị trí. Vui lòng kiểm tra kết nối GPS/WiFi';
                } else if (error.code === 3 || error.code === error.TIMEOUT) {
                    message = 'Hết thời gian chờ lấy vị trí. Vui lòng thử lại hoặc kiểm tra kết nối';
                }
                window.showToast(message, 'error', 4000);
            }
        },
        {
            enableHighAccuracy: false, // Set false để nhanh hơn, không cần GPS chính xác
            timeout: 15000, // Tăng timeout lên 15 giây
            maximumAge: 60000 // Chấp nhận vị trí đã cache trong 1 phút
        }
    );
}

// Don't auto-run on load - only when user clicks button
// locateUserAndPickNearest();

// ===== POST MODAL - 3 BƯỚC =====
let currentStep = 1;
let postMap;
let postMarker;
let selectedPackage = 'vip';

// Initialize post map when modal opens
document.getElementById('postModal')?.addEventListener('shown.bs.modal', async function() {
    // Reset step
    currentStep = 1;
    updatePostSteps();

    // Load form data (categories, cities)
    await loadPostFormData();

    // Wait for modal to fully render before initializing map
    setTimeout(() => {
        const mapContainer = document.getElementById('post-map');
        if (!mapContainer) {
            console.error('Map container #post-map not found');
            return;
        }

        // Default location: Ho Chi Minh City
        const defaultLat = 10.776;
        const defaultLng = 106.700;

        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            console.error('Leaflet library not loaded');
            mapContainer.innerHTML = '<div class="alert alert-danger p-2">Lỗi: Thư viện bản đồ chưa được tải. Vui lòng tải lại trang.</div>';
            return;
        }

        // Initialize map if not exists
        if (!postMap) {
            try {
                // Clear container first
                mapContainer.innerHTML = '';

                postMap = L.map('post-map', {
                    zoomControl: true,
                    attributionControl: true
                }).setView([defaultLat, defaultLng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(postMap);

                // Map click handler
                postMap.on('click', function(e) {
                    const { lat, lng } = e.latlng;
                    setPostLocation(lat, lng);
                    // Reverse geocode để cập nhật địa chỉ
                    reverseGeocodeForPost(lat, lng);
                });

                // Set initial marker after map tiles load
                postMap.whenReady(function() {
                    setPostLocation(defaultLat, defaultLng);
                });
            } catch (error) {
                console.error('Error initializing post map:', error);
                mapContainer.innerHTML = '<div class="alert alert-danger p-2">Lỗi khởi tạo bản đồ: ' + error.message + '</div>';
                return;
            }
        } else {
            // Map already exists, invalidate size to recalculate
            postMap.invalidateSize();
            postMap.setView([defaultLat, defaultLng], 15);
            // Update marker
            setTimeout(() => {
                setPostLocation(defaultLat, defaultLng);
            }, 100);
        }

        // Try to get current location automatically after a short delay
        setTimeout(() => {
            getCurrentLocationForPost();
        }, 500);
    }, 400); // Increased delay to ensure modal is fully rendered
});

// Get current location for post modal
function getCurrentLocationForPost() {
    if (!navigator.geolocation) {
        // Fallback to default location
        setPostLocation(10.776, 106.700);
        return;
    }

    navigator.geolocation.getCurrentPosition(
        pos => {
            const { latitude, longitude } = pos.coords;
            setPostLocation(latitude, longitude);
        },
        error => {
            // Fallback to default location if geolocation fails
            console.log('Geolocation error:', error);
            setPostLocation(10.776, 106.700);
        },
        { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
    );
}

// Set location on post map
function setPostLocation(lat, lng) {
    if (!postMap) return;

    // Update hidden inputs
    document.getElementById('post-latitude').value = lat;
    document.getElementById('post-longitude').value = lng;

    // Remove existing marker
    if (postMarker) {
        postMap.removeLayer(postMarker);
    }

    // Add new marker
    postMarker = L.marker([lat, lng], {
        icon: L.divIcon({
            className: "",
            html: '<div class="map-pin" style="background:#e03131;"></div>',
            iconSize: [18, 18],
            iconAnchor: [9, 18],
        }),
        draggable: true
    }).addTo(postMap);

    // Center map on location
    postMap.setView([lat, lng], 16);

    // Update location when marker is dragged
    postMarker.on('dragend', function(e) {
        const position = postMarker.getLatLng();
        const latInput = document.getElementById('post-latitude');
        const lngInput = document.getElementById('post-longitude');
        if (latInput) latInput.value = position.lat;
        if (lngInput) lngInput.value = position.lng;

        // Reverse geocode để cập nhật địa chỉ
        reverseGeocodeForPost(position.lat, position.lng);
    });
}

// Reverse geocode: lấy địa chỉ từ tọa độ
async function reverseGeocodeForPost(lat, lng) {
    const addressInput = document.getElementById('post-address-search');
    if (!addressInput) return;

    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
            headers: {
                'User-Agent': 'DatViet Real Estate App'
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data && data.display_name) {
                addressInput.value = data.display_name;
            }
        }
    } catch (error) {
        console.error('Error reverse geocoding:', error);
        // Không hiển thị lỗi cho người dùng, chỉ log
    }
}

// Post modal step navigation
function updatePostSteps() {
    document.querySelectorAll('.post-step-content').forEach((el, idx) => {
        el.classList.toggle('active', idx + 1 === currentStep);
    });

    document.querySelectorAll('.step-item').forEach((el, idx) => {
        const stepNum = idx + 1;
        el.classList.toggle('active', stepNum === currentStep);
        el.classList.toggle('completed', stepNum < currentStep);

        if (stepNum < currentStep) {
            el.querySelector('.step-line')?.classList.add('completed');
        }
    });

    const btnPrev = document.getElementById('btn-prev-step');
    const btnNext = document.getElementById('btn-next-step');
    const btnSubmit = document.getElementById('btn-submit-post');

    if (btnPrev) btnPrev.style.display = currentStep > 1 ? 'inline-flex' : 'none';
    if (btnNext) btnNext.style.display = currentStep < 3 ? 'inline-flex' : 'none';
    if (btnSubmit) btnSubmit.style.display = currentStep === 3 ? 'inline-flex' : 'none';
}

// Load categories, cities, districts
async function loadPostFormData() {
    // Load categories
    try {
        const categoriesRes = await fetch('/api/categories');
        const categoriesData = await categoriesRes.json();
        const categorySelect = document.getElementById('post-category');
        if (categorySelect && categoriesData.categories) {
            categorySelect.innerHTML = '<option value="">Chọn loại đất</option>';
            categoriesData.categories.forEach(cat => {
                categorySelect.innerHTML += `<option value="${cat.id}">${cat.name}</option>`;
            });
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }

    // Load cities
    try {
        const citiesRes = await fetch('/api/cities');
        const citiesData = await citiesRes.json();
        const citySelect = document.getElementById('post-city');
        if (citySelect && citiesData.cities) {
            citySelect.innerHTML = '<option value="">Chọn Tỉnh/Thành phố</option>';
            citiesData.cities.forEach(city => {
                citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
            });
        }
    } catch (error) {
        console.error('Error loading cities:', error);
    }

    // Load districts when city changes
    const citySelect = document.getElementById('post-city');
    if (citySelect) {
        // Remove existing listener to avoid duplicates
        const newCitySelect = citySelect.cloneNode(true);
        citySelect.parentNode.replaceChild(newCitySelect, citySelect);

        newCitySelect.addEventListener('change', async function() {
            const cityId = this.value;
            const districtSelect = document.getElementById('post-district');

            if (!cityId) {
                if (districtSelect) districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                return;
            }

            try {
                const districtsRes = await fetch(`/api/districts?city_id=${cityId}`);
                const districtsData = await districtsRes.json();
                if (districtSelect && districtsData.districts) {
                    districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                    districtsData.districts.forEach(district => {
                        districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading districts:', error);
            }
        });
    }
}

document.getElementById('btn-next-step')?.addEventListener('click', function() {
    if (currentStep === 1) {
        const lat = document.getElementById('post-latitude').value;
        const lng = document.getElementById('post-longitude').value;

        if (!lat || !lng || !postMarker) {
            Swal.fire({
                icon: 'warning',
                title: 'Thiếu thông tin',
                text: 'Vui lòng chọn vị trí trên bản đồ hoặc dùng vị trí hiện tại',
                confirmButtonText: 'Đã hiểu'
            });
            return;
        }
    } else if (currentStep === 2) {
        // Validate các trường bắt buộc
        const category = document.getElementById('post-category')?.value;
        const city = document.getElementById('post-city')?.value;
        const title = document.getElementById('post-title')?.value;
        const address = document.getElementById('post-address')?.value;
        const price = document.getElementById('post-price')?.value;
        const area = document.getElementById('post-area')?.value;
        const contactName = document.getElementById('post-contact-name')?.value;
        const phone = document.getElementById('post-phone')?.value;

        let errorMessage = '';
        if (!category) {
            errorMessage = 'Vui lòng chọn loại đất';
            document.getElementById('post-category')?.focus();
        } else if (!city) {
            errorMessage = 'Vui lòng chọn tỉnh/thành phố';
            document.getElementById('post-city')?.focus();
        } else if (!title || !title.trim()) {
            errorMessage = 'Vui lòng nhập tiêu đề tin đăng';
            document.getElementById('post-title')?.focus();
        } else if (!address || !address.trim()) {
            errorMessage = 'Vui lòng nhập địa chỉ chi tiết';
            document.getElementById('post-address')?.focus();
        } else if (!price || parseFloat(price) <= 0) {
            errorMessage = 'Vui lòng nhập giá bán hợp lệ';
            document.getElementById('post-price')?.focus();
        } else if (!area || parseFloat(area) <= 0) {
            errorMessage = 'Vui lòng nhập diện tích hợp lệ';
            document.getElementById('post-area')?.focus();
        } else if (!contactName || !contactName.trim()) {
            errorMessage = 'Vui lòng nhập tên người liên hệ';
            document.getElementById('post-contact-name')?.focus();
        } else if (!phone || !phone.trim()) {
            errorMessage = 'Vui lòng nhập số điện thoại';
            document.getElementById('post-phone')?.focus();
        }

        if (errorMessage) {
            Swal.fire({
                icon: 'warning',
                title: 'Thiếu thông tin',
                text: errorMessage,
                confirmButtonText: 'Đã hiểu'
            });
            return;
        }
    }

    if (currentStep < 3) {
        currentStep++;
        updatePostSteps();
    }
});

document.getElementById('btn-prev-step')?.addEventListener('click', function() {
    if (currentStep > 1) {
        currentStep--;
        updatePostSteps();
    }
});

document.getElementById('btn-use-current-location')?.addEventListener('click', function() {
    if (!postMap) {
        console.error('Post map not initialized');
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Bản đồ chưa sẵn sàng. Vui lòng đợi một chút và thử lại.',
            confirmButtonText: 'Đã hiểu'
        });
        return;
    }

    if (!navigator.geolocation) {
        Swal.fire({
            icon: 'warning',
            title: 'Không hỗ trợ',
            text: 'Trình duyệt không hỗ trợ định vị',
            confirmButtonText: 'Đã hiểu'
        });
        return;
    }

    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lấy vị trí...';

    navigator.geolocation.getCurrentPosition(
        pos => {
            const { latitude, longitude } = pos.coords;
            setPostLocation(latitude, longitude);
            btn.disabled = false;
            btn.innerHTML = originalText;
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: 'Đã lấy vị trí hiện tại',
                timer: 2000,
                showConfirmButton: false
            });
        },
        error => {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Không thể lấy vị trí hiện tại. Vui lòng chọn vị trí trên bản đồ.',
                confirmButtonText: 'Đã hiểu'
            });
            btn.disabled = false;
            btn.innerHTML = originalText;
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
});

// Tìm kiếm địa chỉ và cập nhật vị trí trên bản đồ
document.getElementById('btn-search-address')?.addEventListener('click', function() {
    searchAddressForPost();
});

document.getElementById('post-address-search')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        searchAddressForPost();
    }
});

async function searchAddressForPost() {
    if (!postMap) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Bản đồ chưa sẵn sàng. Vui lòng đợi một chút và thử lại.',
            confirmButtonText: 'Đã hiểu'
        });
        return;
    }

    const addressInput = document.getElementById('post-address-search');
    const searchBtn = document.getElementById('btn-search-address');

    if (!addressInput || !addressInput.value.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập địa chỉ cần tìm',
            confirmButtonText: 'Đã hiểu'
        });
        return;
    }

    const address = addressInput.value.trim();
    const originalBtnText = searchBtn.innerHTML;
    searchBtn.disabled = true;
    searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    try {
        // Sử dụng Nominatim (OpenStreetMap Geocoding API) - miễn phí
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1&countrycodes=vn`, {
            headers: {
                'User-Agent': 'DatViet Real Estate App'
            }
        });

        if (!response.ok) {
            throw new Error('Không thể kết nối đến dịch vụ tìm kiếm địa chỉ');
        }

        const data = await response.json();

        if (data && data.length > 0) {
            const result = data[0];
            const lat = parseFloat(result.lat);
            const lng = parseFloat(result.lon);

            // Cập nhật vị trí trên bản đồ
            setPostLocation(lat, lng);

            // Cập nhật input với địa chỉ chính xác
            addressInput.value = result.display_name;

            // Cập nhật input address trong form
            const formAddressInput = document.getElementById('post-address');
            if (formAddressInput) {
                formAddressInput.value = result.display_name;
            }

            // Hiển thị thông báo thành công
            const existingAlert = addressInput.parentElement.parentElement.querySelector('.alert-success');
            if (existingAlert) {
                existingAlert.remove();
            }

            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show mt-2';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i> Đã tìm thấy: ${result.display_name}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            addressInput.parentElement.parentElement.appendChild(alertDiv);

            // Tự động ẩn thông báo sau 3 giây
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Không tìm thấy',
                text: 'Không tìm thấy địa chỉ. Vui lòng thử lại với địa chỉ khác hoặc chọn trực tiếp trên bản đồ.',
                confirmButtonText: 'Đã hiểu'
            });
        }
    } catch (error) {
        console.error('Error searching address:', error);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Có lỗi xảy ra khi tìm kiếm địa chỉ. Vui lòng thử lại hoặc chọn trực tiếp trên bản đồ.',
            confirmButtonText: 'Đã hiểu'
        });
    } finally {
        searchBtn.disabled = false;
        searchBtn.innerHTML = originalBtnText;
    }
}

// Package selection
document.querySelectorAll('.package-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.package-card').forEach(c => c.classList.remove('active'));
        this.classList.add('active');

        selectedPackage = this.dataset.package;

        // Đồng bộ radio ẩn để Laravel nhận đúng package_id
        const normalRadio = document.getElementById('package-normal');
        const vipRadio = document.getElementById('package-vip');

        if (this.dataset.package === 'vip') {
            if (vipRadio) vipRadio.checked = true;
            if (normalRadio) normalRadio.checked = false;
        } else if (this.dataset.package === 'normal') {
            if (normalRadio) normalRadio.checked = true;
            if (vipRadio) vipRadio.checked = false;
        }
    });
});

// Submit post - dùng AJAX để nhận JSON, tránh hiển thị thô trên trình duyệt
document.getElementById('post-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    await submitPostForm();
});

document.getElementById('btn-submit-post')?.addEventListener('click', async function() {
    await submitPostForm();
});

async function submitPostForm() {
    const price = document.getElementById('post-price')?.value;
    const area = document.getElementById('post-area')?.value;
    const phone = document.getElementById('post-phone')?.value;
    const lat = document.getElementById('post-latitude')?.value;
    const lng = document.getElementById('post-longitude')?.value;

    if (!price || !area || !phone || !lat || !lng) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng điền đầy đủ thông tin',
            confirmButtonText: 'Đã hiểu'
        });
        return;
    }

    const form = document.getElementById('post-form');
    if (!form) return;

    const btn = document.getElementById('btn-submit-post');
    const btnNext = document.getElementById('btn-next-step');
    const btnPrev = document.getElementById('btn-prev-step');
    const originalText = btn ? btn.innerHTML : '';

    try {
        // Khoá nút
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang gửi...';
        }
        if (btnNext) btnNext.disabled = true;
        if (btnPrev) btnPrev.disabled = true;

        const formData = new FormData(form);
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (!response.ok || result.error) {
            const msg = result.error || result.message || 'Có lỗi xảy ra, vui lòng thử lại.';
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: msg,
                confirmButtonText: 'Đã hiểu'
            });
            return;
        }

        // Lấy package đã chọn (1: Thường, 2: VIP)
        const checkedPackageInput = form.querySelector('input[name="package_id"]:checked');
        const packageId = checkedPackageInput ? checkedPackageInput.value : null;

        // Nếu chọn gói VIP thì tạo thanh toán PayOS
        if (packageId === '2' && result.listing_id) {
            try {
                Swal.fire({
                    icon: 'info',
                    title: 'Đang chuyển đến PayOS',
                    text: 'Vui lòng đợi trong giây lát...',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });

                const payResponse = await fetch('/api/payments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        package_id: parseInt(packageId, 10),
                        listing_id: result.listing_id,
                        return_url: window.location.origin + '/tin-cua-toi',
                        cancel_url: window.location.origin + '/tin-cua-toi'
                    })
                });

                const payResult = await payResponse.json();

                if (!payResponse.ok || !payResult.checkout_url) {
                    const msg = payResult.message || 'Không thể tạo thanh toán PayOS. Tin vẫn đã được lưu.';
                    throw new Error(msg);
                }

                // Redirect sang trang thanh toán PayOS
                window.location.href = payResult.checkout_url;
                return;
            } catch (error) {
                console.error('Error creating PayOS payment:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi thanh toán',
                    text: error.message || 'Không thể tạo thanh toán PayOS. Tin vẫn đã được lưu.',
                    confirmButtonText: 'Xem tin của tôi'
                }).then(() => {
                    const redirectUrl = result.redirect_to || '/tin-cua-toi';
                    window.location.href = redirectUrl;
                });
                return;
            }
        }

        // Gói thường (miễn phí) hoặc không tạo được thanh toán thì giữ flow cũ
        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: result.message || 'Tin đăng đã được gửi và đang chờ duyệt',
            confirmButtonText: 'Xem tin của tôi'
        }).then(() => {
            const redirectUrl = result.redirect_to || '/tin-cua-toi';
            window.location.href = redirectUrl;
        });
    } catch (error) {
        console.error('Error submit post:', error);
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Không thể gửi tin đăng. Vui lòng thử lại.',
            confirmButtonText: 'Đã hiểu'
        });
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText || '<i class="bi bi-check-lg"></i> Đăng tin ngay';
        }
        if (btnNext) btnNext.disabled = false;
        if (btnPrev) btnPrev.disabled = false;
    }
}

// ===== REGISTER MODAL - 1-2 BƯỚC =====
document.getElementById('btn-send-otp')?.addEventListener('click', function() {
    const phone = document.getElementById('register-phone').value;
    if (!phone || phone.length < 10) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập số điện thoại hợp lệ',
            confirmButtonText: 'Đã hiểu'
        });
        return;
    }

    // Simulate sending OTP
    document.getElementById('otp-section').style.display = 'block';
    document.getElementById('btn-send-otp').style.display = 'none';
    document.getElementById('btn-verify-otp').style.display = 'block';
    Swal.fire({
        icon: 'success',
        title: 'Đã gửi OTP',
        text: 'Mã OTP đã được gửi đến số điện thoại của bạn',
        timer: 3000,
        showConfirmButton: false
    });
});

document.getElementById('btn-verify-otp')?.addEventListener('click', function() {
    const otp = document.getElementById('register-otp').value;
    if (!otp || otp.length !== 6) {
        Swal.fire({
            icon: 'warning',
            title: 'Thiếu thông tin',
            text: 'Vui lòng nhập mã OTP 6 số',
            confirmButtonText: 'Đã hiểu'
        });
        return;
    }

    // Simulate verification
    Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: 'Đăng ký thành công! Bạn có thể đăng tin ngay.',
        confirmButtonText: 'Đồng ý'
    }).then(() => {
        bootstrap.Modal.getInstance(document.getElementById('registerModal'))?.hide();

        // Reset form
        document.getElementById('register-phone').value = '';
        document.getElementById('register-otp').value = '';
        document.getElementById('otp-section').style.display = 'none';
        document.getElementById('btn-send-otp').style.display = 'block';
        document.getElementById('btn-verify-otp').style.display = 'none';
    });
});

// Social login (simulate)
document.getElementById('btn-register-google')?.addEventListener('click', function() {
    Swal.fire({
        icon: 'info',
        title: 'Thông tin',
        text: 'Đăng ký với Google (1 bước) - Tích hợp Google OAuth',
        confirmButtonText: 'Đã hiểu'
    });
});

document.getElementById('btn-register-facebook')?.addEventListener('click', function() {
    Swal.fire({
        icon: 'info',
        title: 'Thông tin',
        text: 'Đăng ký với Facebook (1 bước) - Tích hợp Facebook OAuth',
        confirmButtonText: 'Đã hiểu'
    });
});

// Track advertisement clicks
function trackAdClick(adId) {
    fetch(`/api/ads/${adId}/click`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).catch(error => {
        console.error('Error tracking ad click:', error);
    });
}
