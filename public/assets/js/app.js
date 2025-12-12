// ===== MAP SETUP =====
const map = L.map('map', { scrollWheelZoom: true }).setView([10.776, 106.700], 16);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
}).addTo(map);


// ===== DATA STORAGE =====
let lots = [];
let currentListing = null;
let loadingListings = false;

// ===== MAP ICONS =====
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

let miniMap;
let miniMarker;
let activePolygon;
let miniPolygon;
let userMarker;
let userCircle;
let markerLayers = [];

// ===== RENDER MARKERS =====
function renderMarkers(data) {
    markerLayers.forEach(m => map.removeLayer(m));
    markerLayers = [];

    data.forEach(lot => {
        const marker = L.marker([lot.lat, lot.lng], { icon: lot.isVip ? iconVip : iconNormal }).addTo(map);
        marker.bindPopup(popupTemplate(lot));

        marker.on("click", () => updateDetail(lot));

        marker.on("popupopen", () => {
            const btn = document.querySelector(`[data-view-lot="${lot.id}"]`);
            if (btn) {
                btn.onclick = () => flyToLot(lot.id);
            }
        });

        markerLayers.push(marker);
    });
}

function popupTemplate(lot) {
    const imageUrl = lot.img || '/images/placeholder.jpg';
    return `
        <div style="width:180px">
            <img src="${imageUrl}" style="width:100%; height:90px; object-fit:cover; border-radius:8px; margin-bottom:6px;" onerror="this.src='/images/placeholder.jpg'">
            <div class="fw-semibold">${lot.price} • ${lot.size}</div>
            <div class="text-muted small mb-2">${lot.type || ''}</div>
            <a href="/tin-dang/${lot.slug || lot.id}" class="btn btn-primary btn-sm w-100" data-view-lot="${lot.id}">Xem chi tiết</a>
        </div>
    `;
}

// ===== MINI MAP =====
function updateMiniMap(lot) {
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
    // Load full detail if not loaded yet
    if (!lot.desc && lot.id) {
        await loadListingDetail(lot.id);
        lot = currentListing || lot;
    }

    setGallery(lot);
    if (document.querySelector(".lot-price")) {
        document.querySelector(".lot-price").innerHTML = `${lot.price} • ${lot.size}`;
    }
    if (document.getElementById("lot-address")) {
        document.getElementById("lot-address").textContent = lot.address;
    }
    if (document.getElementById("lot-type")) {
        document.getElementById("lot-type").textContent = lot.type;
    }
    if (document.getElementById("lot-legal")) {
        document.getElementById("lot-legal").textContent = lot.legal || "Đang cập nhật";
    }
    if (document.getElementById("lot-front")) {
        document.getElementById("lot-front").textContent = lot.front || "Đang cập nhật";
    }
    if (document.getElementById("lot-road")) {
        document.getElementById("lot-road").textContent = lot.road || "Đang cập nhật";
    }
    if (document.getElementById("lot-depth")) {
        document.getElementById("lot-depth").textContent = lot.depth || "Đang cập nhật";
    }
    if (document.getElementById("lot-width")) {
        document.getElementById("lot-width").textContent = lot.roadWidth || lot.road || "Đang cập nhật";
    }
    if (document.getElementById("lot-direction")) {
        document.getElementById("lot-direction").textContent = lot.direction || "Đang cập nhật";
    }
    if (document.getElementById("lot-price-per")) {
        document.getElementById("lot-price-per").textContent = lot.pricePer || "Đang cập nhật";
    }
    if (document.getElementById("lot-plan")) {
        document.getElementById("lot-plan").textContent = lot.planning || "Đang cập nhật";
    }
    if (document.getElementById("lot-deposit")) {
        document.getElementById("lot-deposit").textContent = lot.depositOnline || "Đang cập nhật";
    }
    if (document.getElementById("lot-desc")) {
        document.getElementById("lot-desc").textContent = lot.desc || "Đang cập nhật mô tả.";
    }

    if (document.getElementById("seller-name")) {
        document.getElementById("seller-name").textContent = lot.seller.name;
    }
    if (document.getElementById("seller-phone")) {
        document.getElementById("seller-phone").textContent = lot.seller.phone;
    }

    renderTags(lot.tags);
    renderSimilar(lot.id);
    renderVipCarousel();
    updateMiniMap(lot);
    drawPolygon(lot);
}

function setGallery(lot) {
    const main = document.getElementById("lot-main-img");
    const thumbsWrap = document.getElementById("lot-thumbs");
    const imgs = lot.images && lot.images.length ? lot.images : [lot.img];
    main.src = imgs[0];
    thumbsWrap.innerHTML = "";
    imgs.forEach((url, idx) => {
        const btn = document.createElement("button");
        btn.className = `thumb ${idx === 0 ? "active" : ""}`;
        btn.innerHTML = `<img src="${url}" alt="thumb">`;
        btn.onclick = () => {
            main.src = url;
            thumbsWrap.querySelectorAll(".thumb").forEach(t => t.classList.remove("active"));
            btn.classList.add("active");
        };
        thumbsWrap.appendChild(btn);
    });
}

function renderTags(tags = []) {
    const wrapper = document.getElementById("lot-tags");
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
            <img src="${lot.img || '/images/placeholder.jpg'}" onerror="this.src='/images/placeholder.jpg'">
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
                size: `${listing.area}m²`,
                img: listing.image ? (listing.image.startsWith('http') ? listing.image : `/storage/${listing.image}`) : '/images/placeholder.jpg',
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
async function loadListings(filters = {}) {
    if (loadingListings) return;
    loadingListings = true;

    try {
        const params = new URLSearchParams();
        
        // Add filter params
        if (filters.city) params.append('city', filters.city);
        if (filters.district) params.append('district', filters.district);
        if (filters.category) params.append('category', filters.category);
        if (filters.maxPrice) params.append('max_price', filters.maxPrice);
        if (filters.maxArea) params.append('max_area', filters.maxArea);
        if (filters.hasRoad) params.append('has_road', '1');
        
        // Add map bounds if available
        if (map) {
            const bounds = map.getBounds();
            params.append('bounds[north]', bounds.getNorth());
            params.append('bounds[south]', bounds.getSouth());
            params.append('bounds[east]', bounds.getEast());
            params.append('bounds[west]', bounds.getWest());
        }

        const url = `/api/listings/map${params.toString() ? '?' + params.toString() : ''}`;
        const response = await fetch(url);
        const data = await response.json();

        // Convert API data to app format
        lots = data.listings.map(listing => ({
            id: listing.id,
            name: listing.title,
            price: formatPrice(listing.price),
            size: `${listing.area}m²`,
            priceValue: listing.price,
            sizeValue: listing.area,
            lat: parseFloat(listing.latitude),
            lng: parseFloat(listing.longitude),
            slug: listing.slug,
            img: listing.image ? (listing.image.startsWith('http') ? listing.image : `/storage/${listing.image}`) : '/images/placeholder.jpg',
            type: listing.category || 'Đất',
            address: listing.address,
            city: listing.city,
            district: listing.district,
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
            // Load first listing detail
            await loadListingDetail(lots[0].id);
        } else {
            // Clear markers if no results
            markerLayers.forEach(m => map.removeLayer(m));
            markerLayers = [];
        }
    } catch (error) {
        console.error('Error loading listings:', error);
    } finally {
        loadingListings = false;
    }
}

// Load listing detail from API
async function loadListingDetail(listingId) {
    try {
        const response = await fetch(`/api/listings/${listingId}`);
        const data = await response.json();
        const listing = data.listing;

        // Find and update the lot in lots array
        const lotIndex = lots.findIndex(l => l.id === listingId);
        if (lotIndex === -1) return;

        const lot = lots[lotIndex];
        
        // Update lot with full details
        lot.name = listing.title;
        lot.desc = listing.description || '';
        lot.legal = listing.legal_status || '';
        lot.front = listing.front_width ? `${listing.front_width}m` : '';
        lot.road = listing.road_type || '';
        lot.depth = listing.depth ? `${listing.depth}m` : '';
        lot.roadWidth = listing.road_width ? `${listing.road_width}m` : '';
        lot.direction = listing.direction || '';
        lot.roadAccess = listing.has_road_access || false;
        lot.pricePer = listing.price_per_m2 ? `${(listing.price_per_m2 / 1000000).toFixed(1)}tr/m²` : '';
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
    }
}

function formatPrice(price) {
    if (!price) return '0 triệu';
    const millions = price / 1000000;
    return new Intl.NumberFormat('vi-VN').format(millions) + ' triệu';
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

async function applyFilters() {
    const category = filterTypeEl?.value;
    const city = filterCityEl?.value;
    const district = filterDistrictEl?.value;
    const priceMax = Number(filterPriceEl?.value || 5000);
    const areaMax = Number(filterAreaEl?.value || 1000);
    const needRoad = filterRoadEl?.checked;

    const filters = {};
    if (category) filters.category = category;
    if (city) filters.city = city;
    if (district) filters.district = district;
    if (priceMax) filters.maxPrice = priceMax;
    if (areaMax) filters.maxArea = areaMax;
    if (needRoad) filters.hasRoad = true;

    await loadListings(filters);
}

function updateRangeLabels() {
    priceLabel.textContent = `${filterPriceEl.value}+`;
    areaLabel.textContent = `${filterAreaEl.value}+`;
}

document.getElementById("btn-apply-filters").addEventListener("click", applyFilters);
document.getElementById("btn-nearby").addEventListener("click", locateUserAndPickNearest);
filterPriceEl.addEventListener("input", updateRangeLabels);
filterAreaEl.addEventListener("input", updateRangeLabels);
updateRangeLabels();

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
const favBtn = document.getElementById("favorite-btn");
if (favBtn) {
    favBtn.onclick = () => favBtn.classList.toggle("active");
}

// Initialize: Load listings on page load
loadListings().then(() => {
    if (lots.length > 0) {
        updateDetail(lots[0]);
    }
});

// Reload listings when map bounds change
if (map) {
    map.on('moveend', function() {
        // Debounce to avoid too many requests
        clearTimeout(window.mapReloadTimer);
        window.mapReloadTimer = setTimeout(() => {
            loadListings();
        }, 500);
    });
}

// expose for debugging
window.flyToLot = flyToLot;

// ===== POLYGON DRAW =====
function drawPolygon(lot) {
    if (activePolygon) {
        map.removeLayer(activePolygon);
        activePolygon = null;
    }
    if (miniPolygon && miniMap) {
        miniMap.removeLayer(miniPolygon);
        miniPolygon = null;
    }

    if (lot.polygon && lot.polygon.length) {
        activePolygon = L.polygon(lot.polygon, {
            color: "#7c3aed",
            weight: 2,
            fillColor: "#a855f7",
            fillOpacity: 0.2,
            dashArray: "6 4"
        }).addTo(map);
        map.fitBounds(activePolygon.getBounds(), { maxZoom: 18, padding: [40, 40] });

        if (miniMap) {
            miniPolygon = L.polygon(lot.polygon, {
                color: "#7c3aed",
                weight: 2,
                fillColor: "#c084fc",
                fillOpacity: 0.25,
                dashArray: "4 3"
            }).addTo(miniMap);
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

function locateUserAndPickNearest() {
    if (!navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(
        pos => {
            const { latitude, longitude } = pos.coords;
            markUserLocation(latitude, longitude);
            const nearest = findNearestLots(latitude, longitude)[0];
            if (nearest) {
                updateDetail(nearest);
                map.setView([nearest.lat, nearest.lng], 17);
            } else {
                map.setView([latitude, longitude], 15);
            }
        },
        () => {},
        { enableHighAccuracy: true, timeout: 8000 }
    );
}

// Try geolocation to show nearest lots on load
locateUserAndPickNearest();

// ===== POST MODAL - 3 BƯỚC =====
let currentStep = 1;
let postMap;
let postMarker;
let selectedPackage = 'vip';

// Initialize post map when modal opens
document.getElementById('postModal')?.addEventListener('shown.bs.modal', function() {
    if (!postMap) {
        postMap = L.map('post-map', { zoomControl: true }).setView([10.776, 106.700], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(postMap);

        postMap.on('click', function(e) {
            const { lat, lng } = e.latlng;
            if (postMarker) postMap.removeLayer(postMarker);
            postMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: "",
                    html: '<div class="map-pin" style="background:#e03131;"></div>',
                    iconSize: [18, 18],
                    iconAnchor: [9, 18],
                })
            }).addTo(postMap);
        });
    }
    currentStep = 1;
    updatePostSteps();
});

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

    btnPrev.style.display = currentStep > 1 ? 'inline-flex' : 'none';
    btnNext.style.display = currentStep < 3 ? 'inline-flex' : 'none';
    btnSubmit.style.display = currentStep === 3 ? 'inline-flex' : 'none';
}

document.getElementById('btn-next-step')?.addEventListener('click', function() {
    if (currentStep === 1) {
        if (!postMarker) {
            alert('Vui lòng chọn vị trí trên bản đồ hoặc dùng vị trí hiện tại');
            return;
        }
    } else if (currentStep === 2) {
        const price = document.getElementById('post-price').value;
        const area = document.getElementById('post-area').value;
        const phone = document.getElementById('post-phone').value;
        
        if (!price || !area || !phone) {
            alert('Vui lòng điền đầy đủ thông tin bắt buộc');
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
    if (!navigator.geolocation) {
        alert('Trình duyệt không hỗ trợ định vị');
        return;
    }
    
    navigator.geolocation.getCurrentPosition(
        pos => {
            const { latitude, longitude } = pos.coords;
            if (postMap) {
                postMap.setView([latitude, longitude], 16);
                if (postMarker) postMap.removeLayer(postMarker);
                postMarker = L.marker([latitude, longitude], {
                    icon: L.divIcon({
                        className: "",
                        html: '<div class="map-pin" style="background:#e03131;"></div>',
                        iconSize: [18, 18],
                        iconAnchor: [9, 18],
                    })
                }).addTo(postMap);
            }
        },
        () => alert('Không thể lấy vị trí hiện tại')
    );
});

// Package selection
document.querySelectorAll('.package-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.package-card').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        selectedPackage = this.dataset.package;
    });
});

// Submit post
document.getElementById('btn-submit-post')?.addEventListener('click', function() {
    const price = document.getElementById('post-price').value;
    const area = document.getElementById('post-area').value;
    const phone = document.getElementById('post-phone').value;
    
    if (!price || !area || !phone || !postMarker) {
        alert('Vui lòng điền đầy đủ thông tin');
        return;
    }
    
    // Simulate posting
    alert(`Tin đã được gửi! Gói ${selectedPackage === 'vip' ? 'VIP' : 'Thường'}\nChúng tôi sẽ duyệt trong vòng 24h.`);
    bootstrap.Modal.getInstance(document.getElementById('postModal'))?.hide();
    
    // Reset form
    currentStep = 1;
    updatePostSteps();
    document.getElementById('post-price').value = '';
    document.getElementById('post-area').value = '';
    document.getElementById('post-phone').value = '';
    if (postMarker) {
        postMap.removeLayer(postMarker);
        postMarker = null;
    }
});

// ===== REGISTER MODAL - 1-2 BƯỚC =====
document.getElementById('btn-send-otp')?.addEventListener('click', function() {
    const phone = document.getElementById('register-phone').value;
    if (!phone || phone.length < 10) {
        alert('Vui lòng nhập số điện thoại hợp lệ');
        return;
    }
    
    // Simulate sending OTP
    document.getElementById('otp-section').style.display = 'block';
    document.getElementById('btn-send-otp').style.display = 'none';
    document.getElementById('btn-verify-otp').style.display = 'block';
    alert('Mã OTP đã được gửi đến số điện thoại của bạn');
});

document.getElementById('btn-verify-otp')?.addEventListener('click', function() {
    const otp = document.getElementById('register-otp').value;
    if (!otp || otp.length !== 6) {
        alert('Vui lòng nhập mã OTP 6 số');
        return;
    }
    
    // Simulate verification
    alert('Đăng ký thành công! Bạn có thể đăng tin ngay.');
    bootstrap.Modal.getInstance(document.getElementById('registerModal'))?.hide();
    
    // Reset form
    document.getElementById('register-phone').value = '';
    document.getElementById('register-otp').value = '';
    document.getElementById('otp-section').style.display = 'none';
    document.getElementById('btn-send-otp').style.display = 'block';
    document.getElementById('btn-verify-otp').style.display = 'none';
});

// Social login (simulate)
document.getElementById('btn-register-google')?.addEventListener('click', function() {
    alert('Đăng ký với Google (1 bước) - Tích hợp Google OAuth');
});

document.getElementById('btn-register-facebook')?.addEventListener('click', function() {
    alert('Đăng ký với Facebook (1 bước) - Tích hợp Facebook OAuth');
});
