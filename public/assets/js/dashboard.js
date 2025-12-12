// ===== LOAD DATA FROM API =====
let listings = [];
let loading = false;

async function loadListings() {
    if (loading) return;
    loading = true;

    try {
        const response = await fetch('/tin-cua-toi?format=json');
        const data = await response.json();
        
        listings = data.listings || data.data || [];
        
        render();
        updateStats();
    } catch (error) {
        console.error('Error loading listings:', error);
        // Fallback: try to parse from HTML table if API fails
        parseListingsFromTable();
    } finally {
        loading = false;
    }
}

// Fallback: Parse listings from existing HTML table
function parseListingsFromTable() {
    const rows = document.querySelectorAll('#listing-body tr');
    listings = Array.from(rows).map(row => {
        const cells = row.querySelectorAll('td');
        return {
            id: row.dataset.id || Math.random(),
            title: cells[0]?.querySelector('.fw-semibold')?.textContent || '',
            address: cells[0]?.querySelector('.text-muted')?.textContent || '',
            price: cells[1]?.textContent || '',
            size: cells[2]?.textContent || '',
            vip: cells[3]?.querySelector('.chip-vip') !== null,
            status: cells[4]?.querySelector('.chip-active') ? 'active' : 
                   cells[4]?.querySelector('.chip-pending') ? 'pending' : 'draft',
            type: 'thocu' // Default
        };
    });
    render();
    updateStats();
}

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
        approved: { text: "Đang hiển thị", cls: "chip chip-active" },
        pending: { text: "Chờ duyệt", cls: "chip chip-pending" },
        rejected: { text: "Từ chối", cls: "chip chip-danger" },
        draft: { text: "Nháp", cls: "chip chip-draft" },
        expired: { text: "Hết hạn", cls: "chip chip-secondary" },
        sold: { text: "Đã bán", cls: "chip chip-info" }
    };
    const picked = map[stt] || map.pending;
    return `<span class="${picked.cls}">${picked.text}</span>`;
}

function render() {
    if (!bodyEl) return;

    const status = filterStatus?.value || '';
    const type = filterType?.value || '';
    const vip = filterVip?.value || '';
    const keyword = filterSearch?.value?.trim().toLowerCase() || '';

    const filtered = listings.filter(l => {
        if (status && l.status !== status) return false;
        if (type && l.type !== type) return false;
        if (vip && ((vip === "vip") !== l.vip)) return false;
        if (keyword && !(`${l.title} ${l.address}`.toLowerCase().includes(keyword))) return false;
        return true;
    });

    bodyEl.innerHTML = "";
    
    if (filtered.length === 0) {
        bodyEl.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4 text-muted">
                    Không có tin đăng nào
                </td>
            </tr>
        `;
        return;
    }

    filtered.forEach(item => {
        const tr = document.createElement("tr");
        tr.dataset.id = item.id;
        tr.innerHTML = `
            <td>
                <div class="fw-semibold">${item.title || 'Chưa có tiêu đề'}</div>
                <div class="text-muted small">${item.address || ''}</div>
            </td>
            <td>${item.price || 'N/A'}</td>
            <td>${item.size || 'N/A'}</td>
            <td>${badgeVip(item.vip || item.is_vip || false)}</td>
            <td>${badgeStatus(item.status || 'pending')}</td>
            <td class="text-end">
                <div class="d-flex flex-wrap justify-content-end gap-2 action-btns">
                    ${item.slug ? `<a href="/tin-dang/${item.slug}" class="btn btn-outline-secondary btn-sm">Xem</a>` : ''}
                    <button class="btn btn-outline-secondary btn-sm" onclick="editListing(${item.id})">Sửa</button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="hideListing(${item.id})">Ẩn</button>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteListing(${item.id})">Xóa</button>
                </div>
            </td>
        `;
        bodyEl.appendChild(tr);
    });
}

function updateStats() {
    if (statTotal) statTotal.textContent = listings.length;
    if (statActive) statActive.textContent = listings.filter(l => l.status === "active" || l.status === "approved").length;
    if (statVip) statVip.textContent = listings.filter(l => l.vip || l.is_vip).length;
    if (statPending) statPending.textContent = listings.filter(l => l.status === "pending").length;
}

// Action functions
function editListing(id) {
    // TODO: Implement edit functionality
    alert('Chức năng sửa đang phát triển');
}

function hideListing(id) {
    // TODO: Implement hide functionality
    if (confirm('Bạn có chắc muốn ẩn tin đăng này?')) {
        alert('Chức năng ẩn đang phát triển');
    }
}

function deleteListing(id) {
    if (confirm('Bạn có chắc muốn xóa tin đăng này? Hành động này không thể hoàn tác.')) {
        // TODO: Implement delete functionality
        fetch(`/api/listings/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                listings = listings.filter(l => l.id !== id);
                render();
                updateStats();
            } else {
                alert('Có lỗi xảy ra: ' + (data.message || 'Không thể xóa'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa tin đăng');
        });
    }
}

// Filters
if (filterStatus) filterStatus.addEventListener("change", render);
if (filterType) filterType.addEventListener("change", render);
if (filterVip) filterVip.addEventListener("change", render);
if (filterSearch) {
    filterSearch.addEventListener("input", () => {
        clearTimeout(window.__filterTimer);
        window.__filterTimer = setTimeout(render, 150);
    });
}

if (document.getElementById("btn-clear")) {
    document.getElementById("btn-clear").addEventListener("click", () => {
        if (filterStatus) filterStatus.value = "";
        if (filterType) filterType.value = "";
        if (filterVip) filterVip.value = "";
        if (filterSearch) filterSearch.value = "";
        render();
    });
}

// Load data on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadListings);
} else {
    loadListings();
}
