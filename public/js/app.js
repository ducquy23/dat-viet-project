// ===== MAP SETUP =====
const map = L.map('map', { scrollWheelZoom: true }).setView([10.776, 106.700], 16);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
}).addTo(map);


// ===== FIXED DATA =====
const lots = [
    {
        id: 1,
        name: "Lô VIP 350m²",
        price: "350.000đ",
        size: "350m²",
        priceValue: 350,
        sizeValue: 350,
        lat: 10.7765,
        lng: 106.701,
        img: "https://xdcs.cdnchinhphu.vn/446259493575335936/2023/2/24/chinh-sach-moi-anh-huong-den-thi-truong-bat-dong-san1602162451-16772104701281575851658.png",
        type: "Đất nông nghiệp",
        address: "123 Phan Xích Long, Quận Phú Nhuận, TP.HCM",
        city: "hcm",
        district: "phunhuan",
        tags: ["Sổ đỏ", "Mặt tiền", "Gần chợ"],
        seller: { name: "Anh Minh", phone: "0327 200 505" },
        isVip: true,
        legal: "Sổ đỏ",
        front: "6m",
        road: "Ô tô 6m",
        depth: "12m",
        roadWidth: "12m",
        direction: "Đông Nam",
        roadAccess: true,
        pricePer: "1tr/m²",
        planning: "Không vướng quy hoạch",
        depositOnline: "Có",
        desc: "Lô đất vuông vức, gần chợ và trường học, thích hợp xây nhà ở hoặc đầu tư cho thuê.",
        images: [
            "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=900&q=80",
            "https://images.unsplash.com/photo-1460353581641-37baddab0fa2?auto=format&fit=crop&w=600&q=80",
            "https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80"
        ],
        polygon: [
            [10.77685, 106.70065],
            [10.77690, 106.70105],
            [10.77625, 106.70115],
            [10.77620, 106.70075]
        ]
    },
    {
        id: 2,
        name: "Lô góc 420m²",
        price: "420.000đ",
        size: "420m²",
        priceValue: 420,
        sizeValue: 420,
        lat: 10.775,
        lng: 106.702,
        img: "https://media.istockphoto.com/id/1409298953/vi/anh/c%C3%A1c-%C4%91%E1%BA%A1i-l%C3%BD-b%E1%BA%A5t-%C4%91%E1%BB%99ng-s%E1%BA%A3n-b%E1%BA%AFt-tay-nhau-sau-khi-k%C3%BD-k%E1%BA%BFt-th%E1%BB%8Fa-thu%E1%BA%ADn-h%E1%BB%A3p-%C4%91%E1%BB%93ng-ho%C3%A0n-t%E1%BA%A5t.jpg?b=1&s=612x612&w=0&k=20&c=EUQjk4iNtViMfmWdxR61ctUW0x_sUmQj-LRvqHt8ijk=",
        type: "Đất thổ cư",
        address: "55 Nguyễn Hữu Cảnh, Quận Bình Thạnh, TP.HCM",
        city: "hcm",
        district: "binhthanh",
        tags: ["Sổ hồng", "Đường ô tô", "Gần trường"],
        seller: { name: "Chị Lan", phone: "0901 888 777" },
        isVip: false,
        legal: "Sổ hồng",
        front: "5.5m",
        road: "Đường nhựa 8m",
        depth: "20m",
        roadWidth: "8m",
        direction: "Tây Bắc",
        roadAccess: true,
        pricePer: "1.1tr/m²",
        planning: "Quy hoạch ổn định",
        depositOnline: "Có",
        desc: "Khu dân cư hiện hữu, gần trường học và trung tâm thương mại, pháp lý rõ ràng.",
        images: [
            "https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=900&q=80",
            "https://images.unsplash.com/photo-1505693014937-96b5dbee9705?auto=format&fit=crop&w=600&q=80",
            "https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80"
        ],
        polygon: [
            [10.77535, 106.70155],
            [10.77560, 106.70205],
            [10.77485, 106.70215],
            [10.77465, 106.70160]
        ]
    },
    {
        id: 3,
        name: "Lô VIP 500m²",
        price: "510.000đ",
        size: "500m²",
        priceValue: 510,
        sizeValue: 500,
        lat: 10.7772,
        lng: 106.699,
        img: "https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&q=80",
        type: "Đất thổ cư",
        address: "12 Đinh Tiên Hoàng, Quận 1, TP.HCM",
        city: "hcm",
        district: "quan1",
        tags: ["VIP", "Gần trung tâm", "View sông"],
        seller: { name: "Anh Tuấn", phone: "0989 999 111" },
        isVip: true,
        legal: "Sổ đỏ",
        front: "7m",
        road: "Lộ giới 10m",
        depth: "18m",
        roadWidth: "10m",
        direction: "Đông Bắc",
        roadAccess: true,
        pricePer: "1.02tr/m²",
        planning: "Không tranh chấp",
        depositOnline: "Có",
        desc: "Lô VIP ngay trung tâm, view sông đẹp, phù hợp xây khách sạn mini hoặc căn hộ dịch vụ.",
        images: [
            "https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=900&q=80",
            "https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80",
            "https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=600&q=80"
        ],
        polygon: [
            [10.77755, 106.69865],
            [10.77785, 106.69915],
            [10.77695, 106.69935],
            [10.77670, 106.69885]
        ]
    }
];

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
    return `
        <div style="width:180px">
            <img src="${lot.img}" style="width:100%; height:90px; object-fit:cover; border-radius:8px; margin-bottom:6px;">
            <div class="fw-semibold">${lot.price} • ${lot.size}</div>
            <div class="text-muted small mb-2">${lot.type}</div>
            <button class="btn btn-primary btn-sm w-100" data-view-lot="${lot.id}">Xem chi tiết</button>
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
function updateDetail(lot) {
    setGallery(lot);
    document.querySelector(".lot-price").innerHTML = `${lot.price} • ${lot.size}`;
    document.getElementById("lot-address").textContent = lot.address;
    document.getElementById("lot-type").textContent = lot.type;
    document.getElementById("lot-legal").textContent = lot.legal || "Đang cập nhật";
    document.getElementById("lot-front").textContent = lot.front || "Đang cập nhật";
    document.getElementById("lot-road").textContent = lot.road || "Đang cập nhật";
    document.getElementById("lot-depth").textContent = lot.depth || "Đang cập nhật";
    document.getElementById("lot-width").textContent = lot.roadWidth || lot.road || "Đang cập nhật";
    document.getElementById("lot-direction").textContent = lot.direction || "Đang cập nhật";
    document.getElementById("lot-price-per").textContent = lot.pricePer || "Đang cập nhật";
    document.getElementById("lot-plan").textContent = lot.planning || "Đang cập nhật";
    document.getElementById("lot-deposit").textContent = lot.depositOnline || "Đang cập nhật";
    document.getElementById("lot-desc").textContent = lot.desc || "Đang cập nhật mô tả.";

    document.getElementById("seller-name").textContent = lot.seller.name;
    document.getElementById("seller-phone").textContent = lot.seller.phone;

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
    list.innerHTML = "";

    lots.filter(l => l.id !== activeId).forEach(lot => {
        const item = document.createElement("div");
        item.className = "similar-item";
        item.innerHTML = `
            <img src="${lot.img}">
            <div class="flex-grow-1">
                <div class="fw-bold">${lot.price}</div>
                <div class="text-muted small">${lot.size} • ${lot.type}</div>
                <button class="btn btn-outline-primary btn-sm mt-1" data-jump="${lot.id}">Xem trên bản đồ</button>
            </div>
        `;
        list.appendChild(item);
    });

    list.querySelectorAll("[data-jump]").forEach(btn => {
        btn.onclick = () => flyToLot(Number(btn.getAttribute("data-jump")));
    });
}

// ===== VIP CAROUSEL =====
function renderVipCarousel() {
    const wrap = document.getElementById("vip-carousel");
    if (!wrap) return;
    wrap.innerHTML = "";

    lots.filter(l => l.isVip).forEach(lot => {
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
                
                <button class="btn btn-primary btn-sm w-100 vip-card-btn" data-jump="${lot.id}">
                    <i class="bi bi-map"></i> Xem trên bản đồ
                </button>
            </div>
        `;
        wrap.appendChild(card);
    });

    wrap.querySelectorAll("[data-jump]").forEach(btn => {
        btn.onclick = () => flyToLot(Number(btn.getAttribute("data-jump")));
    });
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

function applyFilters() {
    const type = filterTypeEl.value;
    const city = filterCityEl.value;
    const district = filterDistrictEl.value;
    const priceMax = Number(filterPriceEl.value || 5000);
    const areaMax = Number(filterAreaEl.value || 1000);
    const needRoad = filterRoadEl.checked;

    const filtered = lots.filter(l => {
        if (type === "thocu" && l.type !== "Đất thổ cư") return false;
        if (type === "nongnghiep" && l.type !== "Đất nông nghiệp") return false;
        if (city && l.city !== city) return false;
        if (district && l.district !== district) return false;
        if (l.priceValue && l.priceValue > priceMax) return false;
        if (l.sizeValue && l.sizeValue > areaMax) return false;
        if (needRoad && !l.roadAccess) return false;
        return true;
    });

    const target = filtered.length ? filtered : lots;
    renderMarkers(target);
    updateDetail(target[0]);
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
function flyToLot(id) {
    const lot = lots.find(l => l.id === id);
    if (!lot) return;
    map.setView([lot.lat, lot.lng], 17);
    updateDetail(lot);
}

// ===== FAVORITE BUTTON =====
const favBtn = document.getElementById("favorite-btn");
if (favBtn) {
    favBtn.onclick = () => favBtn.classList.toggle("active");
}

// Init first lot
renderMarkers(lots);
updateDetail(lots[0]);

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
