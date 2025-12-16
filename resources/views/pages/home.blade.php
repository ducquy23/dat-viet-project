@extends('layouts.app')

@section('title', 'Trang chủ')
@section('description', 'Tìm kiếm và đăng tin bán đất trên bản đồ trực quan')

@section('content')
<div class="container-fluid p-0">
    <div class="row g-0 layout-row flex-lg-nowrap">
        <!-- LEFT FILTER -->
        @include('components.filter-sidebar')

        <!-- MAP -->
        @include('components.map-area')

        <!-- RIGHT PANEL -->
        @include('components.detail-panel')
    </div>
</div>
@endsection

@push('styles')
<style>
/* Active Filter Tags */
.filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: linear-gradient(135deg, rgba(51, 87, 147, 0.1) 0%, rgba(74, 107, 168, 0.08) 100%);
    border: 1px solid rgba(51, 87, 147, 0.2);
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: #335793;
    transition: all 0.2s;
}

.filter-tag:hover {
    background: linear-gradient(135deg, rgba(51, 87, 147, 0.15) 0%, rgba(74, 107, 168, 0.12) 100%);
    border-color: rgba(51, 87, 147, 0.3);
    transform: translateY(-1px);
}

.filter-tag .btn-close {
    width: 16px;
    height: 16px;
    padding: 0;
    font-size: 10px;
    opacity: 0.6;
    background: none;
    border: none;
}

.filter-tag .btn-close:hover {
    opacity: 1;
    background: rgba(51, 87, 147, 0.1);
    border-radius: 50%;
}

/* Mobile Filter Button */
#mobile-filter-toggle {
    box-shadow: 0 4px 12px rgba(51, 87, 147, 0.3);
    transition: all 0.3s;
}

#mobile-filter-toggle:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(51, 87, 147, 0.4);
}

/* Skeleton Loaders */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s ease-in-out infinite;
    border-radius: 8px;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.skeleton-text {
    height: 16px;
    margin-bottom: 8px;
}

.skeleton-title {
    height: 24px;
    width: 60%;
    margin-bottom: 12px;
}

.skeleton-image {
    height: 200px;
    width: 100%;
    margin-bottom: 12px;
}

/* Toast Notifications */
.toast-container {
    position: fixed;
    top: 80px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.custom-toast {
    min-width: 300px;
    max-width: 400px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    padding: 16px;
    display: flex;
    align-items: start;
    gap: 12px;
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.custom-toast.success {
    border-left: 4px solid #10b981;
}

.custom-toast.error {
    border-left: 4px solid #ef4444;
}

.custom-toast.warning {
    border-left: 4px solid #f59e0b;
}

.custom-toast.info {
    border-left: 4px solid #3b82f6;
}

.custom-toast-icon {
    font-size: 24px;
    flex-shrink: 0;
}

.custom-toast.success .custom-toast-icon {
    color: #10b981;
}

.custom-toast.error .custom-toast-icon {
    color: #ef4444;
}

.custom-toast.warning .custom-toast-icon {
    color: #f59e0b;
}

.custom-toast.info .custom-toast-icon {
    color: #3b82f6;
}

.custom-toast-content {
    flex: 1;
}

.custom-toast-title {
    font-weight: 600;
    margin-bottom: 4px;
    color: #1a202c;
}

.custom-toast-message {
    font-size: 14px;
    color: #4a5568;
}

.custom-toast-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #9ca3af;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.custom-toast-close:hover {
    color: #4a5568;
}

/* Quick Filter Chips */
.quick-filter-chip,
.quick-filter-chip-mobile {
    transition: all 0.2s;
    border: 2px solid #335793;
    font-weight: 600;
}

.quick-filter-chip:hover,
.quick-filter-chip-mobile:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(51, 87, 147, 0.2);
}

.quick-filter-chip.active,
.quick-filter-chip-mobile.active {
    background: linear-gradient(135deg, #335793 0%, #4a6ba8 100%);
    color: white;
    border-color: #335793;
    box-shadow: 0 4px 12px rgba(51, 87, 147, 0.3);
}

/* Empty State Improvements */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #64748b;
}

.empty-state i {
    font-size: 64px;
    color: #cbd5e1;
    margin-bottom: 16px;
    display: block;
}

.empty-state p {
    font-size: 16px;
    margin: 0;
    color: #64748b;
}

/* Loading Overlay */
.map-loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    border-radius: 16px;
}

.map-loading-overlay .spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 0.3em;
    color: #335793;
}
</style>
@endpush

@push('scripts')
<script>
    // Active Filter Tags Management
    function updateActiveFilters() {
        const params = new URLSearchParams(window.location.search);
        const activeFiltersContainer = document.getElementById('active-filters');
        const activeFiltersMobile = document.getElementById('active-filters-mobile');
        const filterCount = document.getElementById('filter-count');
        const filterCountMobile = document.getElementById('filter-count-mobile');
        const mobileBadge = document.getElementById('mobile-filter-badge');
        
        let activeCount = 0;
        const filters = [];
        
        // Category
        const categoryId = params.get('category');
        if (categoryId) {
            const categorySelect = document.getElementById('filter-type') || document.getElementById('filter-type-mobile');
            if (categorySelect) {
                const option = categorySelect.querySelector(`option[value="${categoryId}"]`);
                if (option) {
                    filters.push({ type: 'category', id: categoryId, label: option.textContent, key: 'category' });
                    activeCount++;
                }
            }
        }
        
        // City
        const cityId = params.get('city');
        if (cityId) {
            const citySelect = document.getElementById('filter-city') || document.getElementById('filter-city-mobile');
            if (citySelect) {
                const option = citySelect.querySelector(`option[value="${cityId}"]`);
                if (option) {
                    filters.push({ type: 'city', id: cityId, label: option.textContent, key: 'city' });
                    activeCount++;
                }
            }
        }
        
        // District
        const districtId = params.get('district');
        if (districtId) {
            const districtSelect = document.getElementById('filter-district') || document.getElementById('filter-district-mobile');
            if (districtSelect) {
                const option = districtSelect.querySelector(`option[value="${districtId}"]`);
                if (option) {
                    filters.push({ type: 'district', id: districtId, label: option.textContent, key: 'district' });
                    activeCount++;
                }
            }
        }
        
        // Price
        const maxPrice = params.get('max_price');
        if (maxPrice && maxPrice < 5000) {
            filters.push({ type: 'price', label: `Giá ≤ ${new Intl.NumberFormat('vi-VN').format(maxPrice)} triệu`, key: 'max_price' });
            activeCount++;
        }
        
        // Area
        const maxArea = params.get('max_area');
        if (maxArea && maxArea < 1000) {
            filters.push({ type: 'area', label: `Diện tích ≤ ${new Intl.NumberFormat('vi-VN').format(maxArea)} m²`, key: 'max_area' });
            activeCount++;
        }
        
        // Road
        if (params.get('has_road')) {
            filters.push({ type: 'road', label: 'Đường ô tô', key: 'has_road' });
            activeCount++;
        }
        
        // Render tags
        function renderTags(container) {
            if (!container) return;
            container.innerHTML = '';
            if (filters.length > 0) {
                container.style.display = 'flex';
                filters.forEach(filter => {
                    const tag = document.createElement('div');
                    tag.className = 'filter-tag';
                    tag.innerHTML = `
                        <span>${filter.label}</span>
                        <button type="button" class="btn-close" onclick="removeFilter('${filter.key}', '${filter.id || ''}')" aria-label="Xóa"></button>
                    `;
                    container.appendChild(tag);
                });
            } else {
                container.style.display = 'none';
            }
        }
        
        renderTags(activeFiltersContainer);
        renderTags(activeFiltersMobile);
        
        // Update count
        if (filterCount) filterCount.textContent = activeCount > 0 ? `${activeCount} tiêu chí` : '0 tiêu chí';
        if (filterCountMobile) filterCountMobile.textContent = activeCount > 0 ? `${activeCount} tiêu chí` : '0 tiêu chí';
        
        // Update mobile badge
        if (mobileBadge) {
            if (activeCount > 0) {
                mobileBadge.textContent = activeCount;
                mobileBadge.style.display = 'block';
            } else {
                mobileBadge.style.display = 'none';
            }
        }
    }
    
    // Remove filter function
    window.removeFilter = function(key, id) {
        const params = new URLSearchParams(window.location.search);
        params.delete(key);
        
        // If removing city, also remove district
        if (key === 'city') {
            params.delete('district');
        }
        
        // Redirect with new params
        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.location.href = newUrl;
    };
    
    // Clear all filters
    function clearAllFilters() {
        window.location.href = window.location.pathname;
    }
    
    // Sync desktop and mobile filters
    function syncFilters() {
        const desktopForm = document.getElementById('filter-form');
        const mobileForm = document.getElementById('filter-form-mobile');
        
        if (!desktopForm || !mobileForm) return;
        
        // Sync from desktop to mobile
        ['filter-type', 'filter-city', 'filter-district', 'filter-price', 'filter-area', 'filter-road'].forEach(id => {
            const desktop = document.getElementById(id);
            const mobile = document.getElementById(id + '-mobile');
            if (desktop && mobile) {
                if (desktop.type === 'checkbox') {
                    mobile.checked = desktop.checked;
                } else {
                    mobile.value = desktop.value;
                }
            }
        });
        
        // Sync from mobile to desktop
        ['filter-type-mobile', 'filter-city-mobile', 'filter-district-mobile', 'filter-price-mobile', 'filter-area-mobile', 'filter-road-mobile'].forEach(id => {
            const mobile = document.getElementById(id);
            const desktopId = id.replace('-mobile', '');
            const desktop = document.getElementById(desktopId);
            if (mobile && desktop) {
                if (mobile.type === 'checkbox') {
                    desktop.checked = mobile.checked;
                } else {
                    desktop.value = mobile.value;
                }
            }
        });
    }
    
    // Filter form handlers
    function setupFilterHandlers() {
        // Desktop handlers
        document.getElementById('filter-price')?.addEventListener('input', function() {
            document.getElementById('price-label').textContent =
                new Intl.NumberFormat('vi-VN').format(this.value) + ' triệu';
            syncFilters();
        });

        document.getElementById('filter-area')?.addEventListener('input', function() {
            document.getElementById('area-label').textContent =
                new Intl.NumberFormat('vi-VN').format(this.value) + ' m²';
            syncFilters();
        });
        
        // Mobile handlers
        document.getElementById('filter-price-mobile')?.addEventListener('input', function() {
            document.getElementById('price-label-mobile').textContent =
                new Intl.NumberFormat('vi-VN').format(this.value) + ' triệu';
            syncFilters();
        });

        document.getElementById('filter-area-mobile')?.addEventListener('input', function() {
            document.getElementById('area-label-mobile').textContent =
                new Intl.NumberFormat('vi-VN').format(this.value) + ' m²';
            syncFilters();
        });
        
        // Clear filters buttons
        document.getElementById('btn-clear-filters')?.addEventListener('click', clearAllFilters);
        document.getElementById('btn-clear-filters-mobile')?.addEventListener('click', clearAllFilters);
        
        // Sync on change
        ['filter-type', 'filter-city', 'filter-district', 'filter-road'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', syncFilters);
            }
        });
        
        ['filter-type-mobile', 'filter-city-mobile', 'filter-district-mobile', 'filter-road-mobile'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', syncFilters);
            }
        });
    }
    
    // Quick Filter Chips
    function setupQuickFilterChips() {
        document.querySelectorAll('.quick-filter-chip, .quick-filter-chip-mobile').forEach(chip => {
            chip.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.dataset.filter;
                const value = this.dataset.value;
                const params = new URLSearchParams(window.location.search);
                
                // Special handling for VIP filter (need to check package)
                if (filter === 'vip') {
                    // VIP filter would need special handling - for now just toggle
                    if (params.get('vip') === value) {
                        params.delete('vip');
                        this.classList.remove('active');
                    } else {
                        params.set('vip', value);
                        this.classList.add('active');
                    }
                } else {
                    // Toggle filter
                    if (params.get(filter) === value) {
                        params.delete(filter);
                        this.classList.remove('active');
                    } else {
                        params.set(filter, value);
                        this.classList.add('active');
                    }
                }
                
                // Apply filter via form submission
                const form = document.getElementById('filter-form') || document.getElementById('filter-form-mobile');
                if (form) {
                    // Update form values
                    if (filter === 'has_road') {
                        const checkbox = form.querySelector('#filter-road') || form.querySelector('#filter-road-mobile');
                        if (checkbox) checkbox.checked = params.get(filter) === value;
                    } else if (filter === 'legal_status') {
                        // This would need a new field or handle differently
                    }
                    
                    // Submit form
                    form.submit();
                } else {
                    // Fallback: redirect
                    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                    window.location.href = newUrl;
                }
            });
            
            // Check if filter is active
            const params = new URLSearchParams(window.location.search);
            if (params.get(this.dataset.filter) === this.dataset.value) {
                this.classList.add('active');
            }
        });
    }
    
    // Show skeleton loader
    window.showSkeletonLoader = function(elementId) {
        const skeleton = document.getElementById(elementId + '-skeleton');
        const content = document.getElementById(elementId + '-content') || document.getElementById(elementId);
        if (skeleton) skeleton.style.display = 'block';
        if (content) content.style.display = 'none';
    };
    
    // Hide skeleton loader
    window.hideSkeletonLoader = function(elementId) {
        const skeleton = document.getElementById(elementId + '-skeleton');
        const content = document.getElementById(elementId + '-content') || document.getElementById(elementId);
        if (skeleton) skeleton.style.display = 'none';
        if (content) content.style.display = 'block';
    };
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateActiveFilters();
        setupFilterHandlers();
        setupQuickFilterChips();
    });
    
    // Update filters when URL changes
    window.addEventListener('popstate', updateActiveFilters);

    function trackAdClick(adId) {
        fetch(`/api/ads/${adId}/click`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
    }
</script>
@endpush

