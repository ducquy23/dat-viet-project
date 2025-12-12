// Data mẫu
const listings = [
    {
        id: 1,
        title: "Lô thổ cư 200m² mặt tiền",
        price: "1.5 tỷ",
        size: "200m²",
        type: "thocu",
        vip: true,
        status: "active",
        address: "123 Phan Xích Long, Phú Nhuận"
    },
    {
        id: 2,
        title: "Đất vườn 500m² view sông",
        price: "2.3 tỷ",
        size: "500m²",
        type: "nongnghiep",
        vip: false,
        status: "pending",
        address: "Cù Lao Dung, Sóc Trăng"
    },
    {
        id: 3,
        title: "Nháp: Lô góc 150m² Quận 9",
        price: "Nháp",
        size: "150m²",
        type: "thocu",
        vip: false,
        status: "draft",
        address: "Phú Hữu, TP Thủ Đức"
    }
];

const bodyEl = document.getElementById("listing-body");
const filterStatus = document.getElementById("filter-status");
const filterType = document.getElementById("filter-type");
const filterVip = document.getElementById("filter-vip");
const filterSearch = document.getElementById("filter-search");

const statTotal = document.getElementById("stat-total");
const statActive = document.getElementById("stat-active");
const statVip = document.getElementById("stat-vip");
const statPending = document.getElementById("stat-pending");

function badgeVip(isVip) {
    return isVip
        ? '<span class="chip chip-vip">VIP</span>'
        : '<span class="chip chip-normal">Thường</span>';
}

function badgeStatus(stt) {
    const map = {
        active: { text: "Đang hiển thị", cls: "chip chip-active" },
        pending: { text: "Chờ duyệt", cls: "chip chip-pending" },
        draft: { text: "Nháp", cls: "chip chip-draft" },
    };
    const picked = map[stt] || map.pending;
    return `<span class="${picked.cls}">${picked.text}</span>`;
}

function render() {
    const status = filterStatus.value;
    const type = filterType.value;
    const vip = filterVip.value;
    const keyword = filterSearch.value.trim().toLowerCase();

    const filtered = listings.filter(l => {
        if (status && l.status !== status) return false;
        if (type && l.type !== type) return false;
        if (vip && ((vip === "vip") !== l.vip)) return false;
        if (keyword && !(`${l.title} ${l.address}`.toLowerCase().includes(keyword))) return false;
        return true;
    });

    bodyEl.innerHTML = "";
    filtered.forEach(item => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td>
                <div class="fw-semibold">${item.title}</div>
                <div class="text-muted small">${item.address}</div>
            </td>
            <td>${item.price}</td>
            <td>${item.size}</td>
            <td>${badgeVip(item.vip)}</td>
            <td>${badgeStatus(item.status)}</td>
            <td class="text-end">
                <div class="d-flex flex-wrap justify-content-end gap-2 action-btns">
                    <button class="btn btn-outline-secondary btn-sm">Sửa</button>
                    <button class="btn btn-outline-secondary btn-sm">Ẩn</button>
                    <button class="btn btn-outline-danger btn-sm">Xóa</button>
                </div>
            </td>
        `;
        bodyEl.appendChild(tr);
    });

    statTotal.textContent = listings.length;
    statActive.textContent = listings.filter(l => l.status === "active").length;
    statVip.textContent = listings.filter(l => l.vip).length;
    statPending.textContent = listings.filter(l => l.status === "pending").length;
}

// Filters
[filterStatus, filterType, filterVip].forEach(sel => sel.addEventListener("change", render));
filterSearch.addEventListener("input", () => {
    clearTimeout(window.__filterTimer);
    window.__filterTimer = setTimeout(render, 150);
});

document.getElementById("btn-clear").addEventListener("click", () => {
    filterStatus.value = "";
    filterType.value = "";
    filterVip.value = "";
    filterSearch.value = "";
    render();
});

render();

