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

    <!-- Contact / Support -->
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="support-card d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 p-3 p-md-4">
                    <div class="support-icon">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold text-primary">Hỗ trợ 24/7 - Công ty vận hành Đất Việt Map</h6>
                        <p class="mb-0 text-muted small">Nếu cần hỗ trợ đăng tin, thanh toán hoặc xử lý sự cố, vui lòng liên hệ:</p>
                    </div>
                    <div class="support-actions d-flex flex-column flex-sm-row gap-2">
                        <a class="btn btn-outline-primary" href="tel:0909000888">
                            <i class="bi bi-telephone-fill me-1"></i> Hotline: 0968425499
                        </a>
                        <a class="btn btn-outline-success" href="https://zalo.me/0909000888" target="_blank" rel="noopener">
                            <i class="bi bi-chat-dots-fill me-1"></i> Zalo: 0968425499
                        </a>
                        <a class="btn btn-outline-secondary" href="mailto:support@datvietmap.vn">
                            <i class="bi bi-envelope-fill me-1"></i> support@datvietmap.vn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Support card */
        .support-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fb 100%);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: var(--dv-shadow-sm, 0 6px 20px rgba(0,0,0,0.06));
        }

        .support-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: rgba(51, 87, 147, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #335793;
            font-size: 28px;
            flex-shrink: 0;
        }

        .support-actions .btn {
            white-space: nowrap;
        }

        .support-actions .btn-outline-success {
            border: 2px solid #198754 !important;
            border-width: 2px !important;
            border-color: #198754 !important;
            border-style: solid !important;
            font-weight: 600;
        }

        .support-actions .btn-outline-success:hover {
            border-color: #157347 !important;
            background-color: #198754;
            color: white;
        }

        .support-actions .btn-outline-secondary {
            border: 2px solid #6c757d !important;
            border-width: 2px !important;
            border-color: #6c757d !important;
            border-style: solid !important;
            font-weight: 600;
        }

        .support-actions .btn-outline-secondary:hover {
            border-color: #5c636a !important;
            background-color: #6c757d;
            color: white;
        }

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

        /* Price Range Slider */
        .price-filter-section {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            background: #fff;
        }

        .price-filter-header {
            user-select: none;
        }

        .price-filter-header:hover {
            opacity: 0.8;
        }

        .price-range-slider-wrapper {
            height: 30px;
            margin: 20px 0;
            padding: 12px 0;
        }

        .price-range-track {
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            transform: translateY(-50%);
        }

        .price-range-fill {
            height: 100%;
            background: #335793;
            border-radius: 3px;
            position: absolute;
            transition: all 0.1s ease;
        }

        .price-handle {
            width: 18px;
            height: 18px;
            background: #fff;
            border: 2px solid #335793;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.1s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            pointer-events: none;
        }

        .price-range-input {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 30px;
            margin: 0;
            padding: 0;
            transform: translateY(-50%);
            opacity: 0;
            cursor: pointer;
            z-index: 5;
            -webkit-appearance: none;
            appearance: none;
        }

        .price-range-input::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            cursor: pointer;
            background: transparent;
        }

        .price-range-input::-moz-range-thumb {
            width: 18px;
            height: 18px;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .price-range-input::-webkit-slider-runnable-track {
            height: 30px;
            cursor: pointer;
        }

        .price-range-input::-moz-range-track {
            height: 30px;
            cursor: pointer;
        }

        .price-range-display {
            margin-top: 12px;
            font-size: 14px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Global flag to prevent sync during drag
        let isPriceDragging = false;

        // Active Filter Tags Management
        function updateActiveFilters() {
            const params = new URLSearchParams(window.location.search);
            const activeFiltersContainer = document.getElementById('active-filters');
            const activeFiltersMobile = document.getElementById('active-filters-mobile');
            let isPriceDragging = false;
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

            // Price Range
            const minPrice = params.get('min_price');
            const maxPrice = params.get('max_price');
            if (minPrice || maxPrice) {
                const minMillion = minPrice ? Math.round(parseInt(minPrice) / 1000000) : 50;
                const maxMillion = maxPrice ? Math.round(parseInt(maxPrice) / 1000000) : 50000;
                const maxLabel = maxMillion >= 50000 ? 'Không giới hạn' : formatPrice(maxMillion);
                filters.push({ type: 'price', label: `Giá: ${formatPrice(minMillion)} - ${maxLabel}`, key: 'price_range' });
                activeCount++;
            }

            // VIP
            if (params.get('vip')) {
                filters.push({ type: 'vip', label: 'Ưu tiên VIP', key: 'vip' });
                activeCount++;
            }

            // Pháp lý
            const legalStatus = params.get('legal_status');
            if (legalStatus) {
                filters.push({ type: 'legal_status', label: `Pháp lý: ${legalStatus}`, key: 'legal_status' });
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
            if (key === 'price_range') {
                params.delete('min_price');
                params.delete('max_price');
            } else {
                params.delete(key);
            }

            // Redirect with new params
            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.location.href = newUrl;
        };

        // Toggle price filter collapse/expand
        window.togglePriceFilter = function(header) {
            const content = header.nextElementSibling;
            const icon = header.querySelector('.price-filter-icon');
            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.classList.remove('bi-chevron-up');
                icon.classList.add('bi-chevron-down');
            } else {
                content.style.display = 'none';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-up');
            }
        };

        // Clear all filters
        function clearAllFilters() {
            window.location.href = window.location.pathname;
        }

        // Global flag to prevent sync during mobile select change
        let isMobileSelectChanging = false;

        // Sync desktop and mobile filters
        function syncFilters(skipMobileToDesktop = false) {
            if (isPriceDragging) return;
            const desktopForm = document.getElementById('filter-form');
            const mobileForm = document.getElementById('filter-form-mobile');

            if (!desktopForm || !mobileForm) return;

            // Sync from desktop to mobile (only if not changing on mobile)
            if (!isMobileSelectChanging) {
                ['filter-type', 'filter-city', 'filter-vip', 'filter-legal-status'].forEach(id => {
                    const desktop = document.getElementById(id);
                    const mobile = document.getElementById(id + '-mobile');
                    if (desktop && mobile) {
                        if (desktop.type === 'checkbox') {
                            mobile.checked = desktop.checked;
                        } else if (desktop.type === 'hidden') {
                            mobile.value = desktop.value;
                        } else {
                            // For select elements, ensure value is set as string
                            const desktopValue = String(desktop.value || '');
                            mobile.value = desktopValue;
                            // Update selectedIndex
                            if (desktopValue) {
                                const optionIndex = Array.from(mobile.options).findIndex(opt => opt.value === desktopValue);
                                if (optionIndex >= 0) {
                                    mobile.selectedIndex = optionIndex;
                                }
                            }
                        }
                    }
                });
            }

            // Sync price range
            const minPriceDesktop = document.getElementById('filter-price-min');
            const maxPriceDesktop = document.getElementById('filter-price-max');
            const minPriceMobile = document.getElementById('filter-price-min-mobile');
            const maxPriceMobile = document.getElementById('filter-price-max-mobile');
            if (minPriceDesktop && minPriceMobile) minPriceMobile.value = minPriceDesktop.value;
            if (maxPriceDesktop && maxPriceMobile) maxPriceMobile.value = maxPriceDesktop.value;

            // Sync from mobile to desktop (only if not skipping)
            if (!skipMobileToDesktop) {
                ['filter-type-mobile', 'filter-city-mobile', 'filter-vip-mobile', 'filter-legal-status-mobile'].forEach(id => {
                    const mobile = document.getElementById(id);
                    const desktopId = id.replace('-mobile', '');
                    const desktop = document.getElementById(desktopId);
                    if (mobile && desktop) {
                        if (mobile.type === 'checkbox') {
                            desktop.checked = mobile.checked;
                        } else if (mobile.type === 'hidden') {
                            desktop.value = mobile.value;
                        } else {
                            // For select elements, ensure value is set as string
                            const mobileValue = String(mobile.value || '');
                            desktop.value = mobileValue;
                            // Update selectedIndex
                            if (mobileValue) {
                                const optionIndex = Array.from(desktop.options).findIndex(opt => opt.value === mobileValue);
                                if (optionIndex >= 0) {
                                    desktop.selectedIndex = optionIndex;
                                }
                            }
                        }
                    }
                });
            }

            // Sync price range from mobile to desktop
            if (minPriceMobile && minPriceDesktop) minPriceDesktop.value = minPriceMobile.value;
            if (maxPriceMobile && maxPriceDesktop) maxPriceDesktop.value = maxPriceMobile.value;
        }

        // Format price: convert million to display format - Rule: < 1 tỉ hiển thị triệu, >= 1 tỉ hiển thị tỉ
        function formatPrice(million) {
            if (million >= 50000) {
                return 'Không giới hạn';
            } else if (million >= 1000) {
                // >= 1000 triệu (>= 1 tỉ) → hiển thị theo tỉ
                const ty = million / 1000;
                if (ty === Math.floor(ty)) {
                    return `đ${new Intl.NumberFormat('vi-VN').format(ty)} tỉ`;
                } else {
                    const tyRounded = Math.round(ty * 10) / 10;
                    return `đ${new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(tyRounded)} tỉ`;
                }
            } else {
                // < 1000 triệu (< 1 tỉ) → hiển thị theo triệu
                if (million === Math.floor(million)) {
                    return `đ${new Intl.NumberFormat('vi-VN').format(million)} triệu`;
                } else {
                    const priceRounded = Math.round(million * 10) / 10;
                    return `đ${new Intl.NumberFormat('vi-VN', { minimumFractionDigits: 1, maximumFractionDigits: 1 }).format(priceRounded)} triệu`;
                }
            }
        }

        // Update price range slider UI
        function updatePriceRange(minInput, maxInput, displayEl, fillEl, minHandle, maxHandle) {
            const min = parseInt(minInput.value);
            const max = parseInt(maxInput.value);
            const minVal = parseInt(minInput.min);
            const maxVal = parseInt(maxInput.max);

            const minPercent = ((min - minVal) / (maxVal - minVal)) * 100;
            const maxPercent = ((max - minVal) / (maxVal - minVal)) * 100;

            if (fillEl) {
                fillEl.style.left = minPercent + '%';
                fillEl.style.right = (100 - maxPercent) + '%';
            }

            if (minHandle) minHandle.style.left = minPercent + '%';
            if (maxHandle) maxHandle.style.left = maxPercent + '%';

            if (displayEl) {
                const maxLabel = max >= 50000 ? 'Không giới hạn' : formatPrice(max);
                displayEl.textContent = `${formatPrice(min)} - ${maxLabel}`;
            }

            const minHidden = document.getElementById('min_price_hidden') || document.getElementById('min_price_hidden_mobile');
            const maxHidden = document.getElementById('max_price_hidden') || document.getElementById('max_price_hidden_mobile');
            if (minHidden) minHidden.value = min * 1_000_000;
            // If max is 50000 (unlimited), set a very high value or empty
            if (maxHidden) {
                if (max >= 50000) {
                    maxHidden.value = ''; // Empty means no limit
                } else {
                    maxHidden.value = max * 1_000_000;
                }
            }
        }


        // Setup price range slider
        function setupPriceRangeSlider(prefix = '') {
            const minInput = document.getElementById(`filter-price-min${prefix}`);
            const maxInput = document.getElementById(`filter-price-max${prefix}`);
            const displayEl = document.getElementById(`price-range-display${prefix}`);
            const wrapper = minInput?.closest('.price-range-slider-wrapper');
            const fillEl = wrapper?.querySelector('.price-range-fill');
            const minHandle = wrapper?.querySelector('.price-handle-min');
            const maxHandle = wrapper?.querySelector('.price-handle-max');

            if (!minInput || !maxInput) return;

            // Track which input is currently being dragged
            let activeInput = null;

            // Initial update
            updatePriceRange(minInput, maxInput, displayEl, fillEl, minHandle, maxHandle);

            // Function to determine which input should be active based on click position
            function getActiveInput(x) {
                const rect = wrapper.getBoundingClientRect();
                const percent = ((x - rect.left) / rect.width) * 100;

                const min = parseInt(minInput.value);
                const max = parseInt(maxInput.value);
                const minVal = parseInt(minInput.min);
                const maxVal = parseInt(minInput.max);
                const minPercent = ((min - minVal) / (maxVal - minVal)) * 100;
                const maxPercent = ((max - minVal) / (maxVal - minVal)) * 100;

                // Calculate distance to each handle
                const distanceToMin = Math.abs(percent - minPercent);
                const distanceToMax = Math.abs(percent - maxPercent);

                // If click is closer to min handle, activate min
                // Otherwise activate max
                return distanceToMin <= distanceToMax ? minInput : maxInput;
            }

            // Handle mousedown to determine which slider to activate
            wrapper.addEventListener('mousedown', function (e) {
                // Only handle if clicking on the track area, not on the inputs directly
                if (e.target === minInput || e.target === maxInput) {
                    activeInput = e.target;
                    isDragging = true;
                    isPriceDragging = true; // ⬅️ BƯỚC 1: Set flag
                    return;
                }

                const rect = wrapper.getBoundingClientRect();
                const percent = ((e.clientX - rect.left) / rect.width) * 100;

                activeInput = getActiveInput(e.clientX);
                isDragging = true;
                isPriceDragging = true; // ⬅️ BƯỚC 1: Set flag

                const minVal = parseInt(activeInput.min);
                const maxVal = parseInt(activeInput.max);

                let value = minVal + (percent / 100) * (maxVal - minVal);
                value = Math.round(value / 50) * 50;

                if (activeInput === minInput) {
                    value = Math.min(value, parseInt(maxInput.value));
                } else {
                    value = Math.max(value, parseInt(minInput.value));
                }

                activeInput.value = value;
                activeInput.dispatchEvent(new Event('input', { bubbles: true }));
            });


            // Re-enable both inputs on mouseup
            const handleMouseUp = function() {
                minInput.style.zIndex = '5';
                maxInput.style.zIndex = '6';
                activeInput = null;
                isDragging = false;
                isPriceDragging = false; // ⬅️ BƯỚC 1: Reset flag
            };
            document.addEventListener('mouseup', handleMouseUp);
            wrapper.addEventListener('mouseup', handleMouseUp);
            wrapper.addEventListener('mouseleave', handleMouseUp);

            // Add event listeners with proper min/max constraints
            minInput.addEventListener('input', function() {
                let currentMin = parseInt(this.value);
                const currentMax = parseInt(maxInput.value);
                const minVal = parseInt(this.min);
                const maxVal = parseInt(this.max);

                // Ensure within bounds
                currentMin = Math.max(minVal, Math.min(maxVal, currentMin));

                // Prevent min from exceeding max
                if (currentMin > currentMax) {
                    currentMin = currentMax;
                }

                this.value = currentMin;
                updatePriceRange(minInput, maxInput, displayEl, fillEl, minHandle, maxHandle);
                syncFilters();
            });

            maxInput.addEventListener('input', function() {
                const currentMin = parseInt(minInput.value);
                let currentMax = parseInt(this.value);
                const minVal = parseInt(this.min);
                const maxVal = parseInt(this.max);

                // Ensure within bounds
                currentMax = Math.max(minVal, Math.min(maxVal, currentMax));

                // Prevent max from going below min
                if (currentMax < currentMin) {
                    currentMax = currentMin;
                }

                this.value = currentMax;
                updatePriceRange(minInput, maxInput, displayEl, fillEl, minHandle, maxHandle);
                syncFilters();
            });

            // Handle mouse move to update value while dragging
            let isDragging = false;
            wrapper.addEventListener('mousemove', function(e) {
                if (activeInput && (e.buttons === 1 || isDragging)) { // Only if mouse is down
                    isDragging = true;
                    isPriceDragging = true; // ⬅️ BƯỚC 1: Set flag khi đang drag
                    const rect = wrapper.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const percent = Math.max(0, Math.min(100, (x / rect.width) * 100));
                    const minVal = parseInt(activeInput.min);
                    const maxVal = parseInt(activeInput.max);
                    let value = minVal + (percent / 100) * (maxVal - minVal);
                    value = Math.round(value / 50) * 50; // Round to step (50 triệu)

                    // Ensure value is within bounds
                    value = Math.max(minVal, Math.min(maxVal, value));

                    // Apply constraints
                    if (activeInput === minInput) {
                        const currentMax = parseInt(maxInput.value);
                        value = Math.min(value, currentMax);
                    } else {
                        const currentMin = parseInt(minInput.value);
                        value = Math.max(value, currentMin);
                    }

                    // Only update if value changed
                    if (parseInt(activeInput.value) !== value) {
                        activeInput.value = value;
                        activeInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }
            });

            // Reset dragging flag on mouseup
            document.addEventListener('mouseup', function() {
                isPriceDragging = false;
            });
        }

        // Filter form handlers
        function setupFilterHandlers() {
            // Setup price range sliders
            setupPriceRangeSlider('');
            setupPriceRangeSlider('-mobile');

            // Clear filters buttons
            document.getElementById('btn-clear-filters')?.addEventListener('click', clearAllFilters);
            document.getElementById('btn-clear-filters-mobile')?.addEventListener('click', clearAllFilters);

            // Handle filter form submission (prevent default, use AJAX)
            const filterForm = document.getElementById('filter-form');
            const filterFormMobile = document.getElementById('filter-form-mobile');

            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    applyFiltersFromForm(this);
                });
            }

            if (filterFormMobile) {
                filterFormMobile.addEventListener('submit', function(e) {
                    e.preventDefault();
                    applyFiltersFromForm(this);
                });
            }

            // Sync on change
            ['filter-type', 'filter-city'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', syncFilters);
                }
            });

            ['filter-type-mobile', 'filter-city-mobile'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', function(e) {
                        // Set flag to prevent desktop-to-mobile sync
                        isMobileSelectChanging = true;

                        // Get the selected value
                        const selectedValue = String(this.value || '');

                        // Ensure the value is set correctly
                        this.value = selectedValue;

                        // Force UI update by setting selectedIndex
                        if (selectedValue) {
                            const optionIndex = Array.from(this.options).findIndex(opt => opt.value === selectedValue);
                            if (optionIndex >= 0) {
                                this.selectedIndex = optionIndex;
                                // Ensure the option is selected
                                this.options[optionIndex].selected = true;
                            }
                        } else {
                            this.selectedIndex = 0; // Select first option (empty)
                            if (this.options[0]) {
                                this.options[0].selected = true;
                            }
                        }

                        // Use setTimeout to ensure value is set before syncing
                        setTimeout(() => {
                            // Double-check value is still correct
                            if (this.value !== selectedValue) {
                                this.value = selectedValue;
                                if (selectedValue) {
                                    const optionIndex = Array.from(this.options).findIndex(opt => opt.value === selectedValue);
                                    if (optionIndex >= 0) {
                                        this.selectedIndex = optionIndex;
                                        this.options[optionIndex].selected = true;
                                    }
                                }
                            }

                            // Sync from mobile to desktop only
                            syncFilters(false);

                            // Reset flag after sync
                            setTimeout(() => {
                                isMobileSelectChanging = false;
                            }, 50);
                        }, 10);
                    });
                }
            });

            // Handle "Tìm gần tôi" button for mobile
            const btnNearbyMobile = document.getElementById('btn-nearby-mobile');
            if (btnNearbyMobile) {
                btnNearbyMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Get current filter values from mobile form
                    const mobileForm = document.getElementById('filter-form-mobile');
                    if (mobileForm) {
                        // Update price hidden inputs first
                        const minInput = document.getElementById('filter-price-min-mobile');
                        const maxInput = document.getElementById('filter-price-max-mobile');
                        const minHidden = mobileForm.querySelector('#min_price_hidden_mobile');
                        const maxHidden = mobileForm.querySelector('#max_price_hidden_mobile');

                        if (minInput && minHidden) {
                            const minMillion = parseInt(minInput.value) || 50;
                            minHidden.value = minMillion * 1_000_000;
                        }
                        if (maxInput && maxHidden) {
                            const maxMillion = parseInt(maxInput.value) || 50000;
                            if (maxMillion >= 50000) {
                                maxHidden.value = '';
                            } else {
                                maxHidden.value = maxMillion * 1_000_000;
                            }
                        }

                        const formData = new FormData(mobileForm);
                        const params = new URLSearchParams();

                        // Collect current filter values
                        for (const [key, value] of formData.entries()) {
                            if (key === 'min_price_million' || key === 'max_price_million') {
                                continue;
                            }
                            if (value && value.trim() !== '') {
                                params.append(key, value);
                            }
                        }

                        // Get price from hidden inputs
                        if (minHidden && minHidden.value) {
                            params.set('min_price', minHidden.value);
                        }
                        if (maxHidden && maxHidden.value) {
                            params.set('max_price', maxHidden.value);
                        }

                        // Update URL with current filters
                        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                        window.history.pushState({}, '', newUrl);

                        // Update active filters
                        updateActiveFilters();
                    }

                    // Call locateUserAndPickNearest function
                    if (window.locateUserAndPickNearest) {
                        window.locateUserAndPickNearest();
                    }
                });
            }

            // Ensure buttons are not disabled
            const btnApplyMobile = document.getElementById('btn-apply-filters-mobile');
            const btnNearbyMobileCheck = document.getElementById('btn-nearby-mobile');
            if (btnApplyMobile) {
                btnApplyMobile.disabled = false;
                btnApplyMobile.removeAttribute('disabled');
            }
            if (btnNearbyMobileCheck) {
                btnNearbyMobileCheck.disabled = false;
                btnNearbyMobileCheck.removeAttribute('disabled');
            }
        }

        // Apply filters from form and update map
        function applyFiltersFromForm(form) {
            // Update price hidden inputs before collecting form data
            const isMobile = form.id === 'filter-form-mobile';
            const minInput = isMobile ? document.getElementById('filter-price-min-mobile') : document.getElementById('filter-price-min');
            const maxInput = isMobile ? document.getElementById('filter-price-max-mobile') : document.getElementById('filter-price-max');
            const minHidden = form.querySelector('#min_price_hidden') || form.querySelector('#min_price_hidden_mobile');
            const maxHidden = form.querySelector('#max_price_hidden') || form.querySelector('#max_price_hidden_mobile');

            // Get current values from URL to check if user changed from default
            const currentParams = new URLSearchParams(window.location.search);
            const currentMinPrice = currentParams.get('min_price');
            const currentMaxPrice = currentParams.get('max_price');

            // Default values (in triệu)
            const defaultMin = 50;
            const defaultMax = 50000;

            if (minInput && minHidden) {
                const minMillion = parseInt(minInput.value) || defaultMin;
                // Only set min_price if user changed from default OR if it was already set in URL
                if (minMillion !== defaultMin || currentMinPrice) {
                    minHidden.value = minMillion * 1_000_000;
                } else {
                    minHidden.value = ''; // Don't send default value
                }
            }

            if (maxInput && maxHidden) {
                const maxMillion = parseInt(maxInput.value) || defaultMax;
                // Only set max_price if user changed from default (unlimited) OR if it was already set in URL
                if (maxMillion < defaultMax || currentMaxPrice) {
                    if (maxMillion >= defaultMax) {
                        maxHidden.value = ''; // Unlimited
                    } else {
                        maxHidden.value = maxMillion * 1_000_000;
                    }
                } else {
                    maxHidden.value = ''; // Don't send default value (unlimited)
                }
            }

            const formData = new FormData(form);
            const params = new URLSearchParams();

            // Collect all form values, but prioritize hidden price inputs
            for (const [key, value] of formData.entries()) {
                // Skip min_price_million and max_price_million - use hidden inputs instead
                if (key === 'min_price_million' || key === 'max_price_million') {
                    continue;
                }

                if (value && value.trim() !== '') {
                    params.append(key, value);
                }
            }

            // Get price from hidden inputs (already in đồng) - only add if not empty
            if (minHidden && minHidden.value && minHidden.value.trim() !== '') {
                params.set('min_price', minHidden.value);
            } else {
                params.delete('min_price');
            }

            if (maxHidden && maxHidden.value && maxHidden.value.trim() !== '') {
                params.set('max_price', maxHidden.value);
            } else {
                params.delete('max_price');
            }

            // Update URL without reload
            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({}, '', newUrl);

            // Update active filters display
            updateActiveFilters();

            // Reload listings and update map markers
            applyFiltersAndUpdateMap();

            // Close mobile filter offcanvas if open
            const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('filter-offcanvas'));
            if (offcanvas) {
                offcanvas.hide();
            }

            // Re-initialize mobile filters after applying to ensure UI is in sync
            setTimeout(() => {
                initializeMobileFiltersFromParams();
            }, 100);
        }

        // Quick Filter Chips
        function setupQuickFilterChips() {
            document.querySelectorAll('.quick-filter-chip, .quick-filter-chip-mobile').forEach(chip => {
                chip.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.dataset?.filter;
                    const value = this.dataset?.value;
                    if (!filter || !value) return;
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
                        const vipInput = document.getElementById('filter-vip') || document.getElementById('filter-vip-mobile');
                        if (vipInput) vipInput.value = params.get('vip') || '';
                    } else {
                        // Toggle filter
                        if (params.get(filter) === value) {
                            params.delete(filter);
                            this.classList.remove('active');
                        } else {
                            params.set(filter, value);
                            this.classList.add('active');
                        }

                        if (filter === 'legal_status') {
                            const legalInput = document.getElementById('filter-legal-status') || document.getElementById('filter-legal-status-mobile');
                            if (legalInput) legalInput.value = params.get('legal_status') || '';
                        }
                    }

                    // Apply filter via AJAX (no page reload)
                    const form = document.getElementById('filter-form') || document.getElementById('filter-form-mobile');
                    if (form) {
                        // Update form values
                        if (filter === 'legal_status') {
                            const legalInput = form.querySelector('#filter-legal-status') || form.querySelector('#filter-legal-status-mobile');
                            if (legalInput) legalInput.value = params.get('legal_status') || '';
                        } else if (filter === 'vip') {
                            const vipInput = form.querySelector('#filter-vip') || form.querySelector('#filter-vip-mobile');
                            if (vipInput) vipInput.value = params.get('vip') || '';
                        }

                        // Update URL without reload
                        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                        window.history.pushState({}, '', newUrl);

                        // Update active filters display
                        updateActiveFilters();

                        // Reload listings and update map markers
                        applyFiltersAndUpdateMap();
                    } else {
                        // Fallback: redirect
                        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                        window.location.href = newUrl;
                    }
                });

                // Check if filter is active (guard dataset)
                const params = new URLSearchParams(window.location.search);
                const f = chip.dataset?.filter;
                const v = chip.dataset?.value;
                if (f && v && params.get(f) === v) {
                    chip.classList.add('active');
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

        // Initialize mobile filter values from URL params
        function initializeMobileFiltersFromParams() {
            const params = new URLSearchParams(window.location.search);

            // Initialize select dropdowns - ensure value is set correctly
            const categoryMobile = document.getElementById('filter-type-mobile');
            const cityMobile = document.getElementById('filter-city-mobile');

            if (categoryMobile) {
                const categoryId = params.get('category');
                if (categoryId) {
                    // Convert to string to match option value
                    const categoryValue = String(categoryId);
                    // Check if option exists before setting value
                    const optionExists = Array.from(categoryMobile.options).some(opt => opt.value === categoryValue);
                    if (optionExists) {
                        categoryMobile.value = categoryValue;
                        // Force UI update by setting selectedIndex
                        categoryMobile.selectedIndex = Array.from(categoryMobile.options).findIndex(opt => opt.value === categoryValue);
                    }
                } else {
                    categoryMobile.value = '';
                    categoryMobile.selectedIndex = 0; // Select first option (empty)
                }
            }

            if (cityMobile) {
                const cityId = params.get('city');
                if (cityId) {
                    // Convert to string to match option value
                    const cityValue = String(cityId);
                    // Check if option exists before setting value
                    const optionExists = Array.from(cityMobile.options).some(opt => opt.value === cityValue);
                    if (optionExists) {
                        cityMobile.value = cityValue;
                        // Force UI update by setting selectedIndex
                        cityMobile.selectedIndex = Array.from(cityMobile.options).findIndex(opt => opt.value === cityValue);
                    }
                } else {
                    cityMobile.value = '';
                    cityMobile.selectedIndex = 0; // Select first option (empty)
                }
            }

            // Initialize hidden inputs
            const vipMobile = document.getElementById('filter-vip-mobile');
            const legalMobile = document.getElementById('filter-legal-status-mobile');
            if (vipMobile) {
                const vipValue = params.get('vip') || '';
                vipMobile.value = vipValue;
            }
            if (legalMobile) {
                const legalValue = params.get('legal_status') || '';
                legalMobile.value = legalValue;
            }

            // Initialize quick filter chips active state
            document.querySelectorAll('.quick-filter-chip-mobile').forEach(chip => {
                const filter = chip.dataset?.filter;
                const value = chip.dataset?.value;
                if (filter && value) {
                    const paramValue = params.get(filter);
                    if (paramValue === value) {
                        chip.classList.add('active');
                    } else {
                        chip.classList.remove('active');
                    }
                }
            });
        }

        // Initialize price range from URL params
        function initializePriceRangeFromParams() {
            const params = new URLSearchParams(window.location.search);
            let minPrice = params.get('min_price') ? parseInt(params.get('min_price')) : null;
            let maxPrice = params.get('max_price') ? parseInt(params.get('max_price')) : null;

            // Convert from đồng to triệu
            let minPriceMillion = minPrice ? Math.round(minPrice / 1000000) : 50;
            let maxPriceMillion = maxPrice && maxPrice !== '' ? Math.round(maxPrice / 1000000) : 50000;

            // Validate and fix min/max order
            if (minPriceMillion > maxPriceMillion && maxPrice) {
                // Swap if reversed
                const temp = minPriceMillion;
                minPriceMillion = maxPriceMillion;
                maxPriceMillion = temp;
            }

            // Desktop
            const minInputDesktop = document.getElementById('filter-price-min');
            const maxInputDesktop = document.getElementById('filter-price-max');
            if (minInputDesktop && maxInputDesktop) {
                minInputDesktop.value = minPriceMillion;
                maxInputDesktop.value = maxPriceMillion;
                const displayEl = document.getElementById('price-range-display');
                const wrapper = minInputDesktop.closest('.price-range-slider-wrapper');
                const fillEl = wrapper?.querySelector('.price-range-fill');
                const minHandle = wrapper?.querySelector('.price-handle-min');
                const maxHandle = wrapper?.querySelector('.price-handle-max');
                updatePriceRange(minInputDesktop, maxInputDesktop, displayEl, fillEl, minHandle, maxHandle);
            }

            // Mobile
            const minInputMobile = document.getElementById('filter-price-min-mobile');
            const maxInputMobile = document.getElementById('filter-price-max-mobile');
            if (minInputMobile && maxInputMobile) {
                minInputMobile.value = minPriceMillion;
                maxInputMobile.value = maxPriceMillion;
                const displayEl = document.getElementById('price-range-display-mobile');
                const wrapper = minInputMobile.closest('.price-range-slider-wrapper');
                const fillEl = wrapper?.querySelector('.price-range-fill');
                const minHandle = wrapper?.querySelector('.price-handle-min');
                const maxHandle = wrapper?.querySelector('.price-handle-max');
                updatePriceRange(minInputMobile, maxInputMobile, displayEl, fillEl, minHandle, maxHandle);
            }
        }

        // Function to apply filters and update map markers
        async function applyFiltersAndUpdateMap() {
            // Call loadListings from app.js if available (it will read URL params automatically)
            if (window.loadListings) {
                await window.loadListings(); // Empty filters object means read from URL
            } else if (window.loadListingsForMap) {
                // Fallback to map-area function
                window.loadListingsForMap();
            } else {
                // Direct API call as last resort
                try {
                    const params = new URLSearchParams(window.location.search);
                    const apiUrl = new URL('/api/listings/map', window.location.origin);
                    params.forEach((value, key) => {
                        if (['city', 'category', 'min_price', 'max_price', 'vip', 'legal_status'].includes(key)) {
                            apiUrl.searchParams.append(key, value);
                        }
                    });

                    const response = await fetch(apiUrl);
                    const data = await response.json();

                    // Update markers using renderMarkers if available
                    if (window.renderMarkers) {
                        const formatPriceHelper = (price) => {
                            if (!price) return '0 triệu';
                            return new Intl.NumberFormat('vi-VN').format(price) + ' triệu';
                        };

                        const formattedListings = data.listings
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
                                price: formatPriceHelper(listing.price),
                                size: `${listing.area}m²`,
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
                                isVip: listing.is_vip || false
                            }));

                        window.renderMarkers(formattedListings);
                    }
                } catch (error) {
                    console.error('Error loading filtered listings:', error);
                    if (window.showToast) {
                        window.showToast('Có lỗi xảy ra khi tải dữ liệu', 'error', 3000);
                    }
                }
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateActiveFilters();
            setupFilterHandlers();
            setupQuickFilterChips();
            initializePriceRangeFromParams();
            initializeMobileFiltersFromParams();

            // Initialize mobile filters when offcanvas is shown
            const filterOffcanvas = document.getElementById('filter-offcanvas');
            if (filterOffcanvas) {
                filterOffcanvas.addEventListener('shown.bs.offcanvas', function() {
                    // Initialize from URL params first
                    initializeMobileFiltersFromParams();
                    // Then sync from desktop to ensure consistency
                    syncFilters();
                    // Update price range display for mobile
                    const minInputMobile = document.getElementById('filter-price-min-mobile');
                    const maxInputMobile = document.getElementById('filter-price-max-mobile');
                    if (minInputMobile && maxInputMobile) {
                        const displayEl = document.getElementById('price-range-display-mobile');
                        const wrapper = minInputMobile.closest('.price-range-slider-wrapper');
                        const fillEl = wrapper?.querySelector('.price-range-fill');
                        const minHandle = wrapper?.querySelector('.price-handle-min');
                        const maxHandle = wrapper?.querySelector('.price-handle-max');
                        updatePriceRange(minInputMobile, maxInputMobile, displayEl, fillEl, minHandle, maxHandle);
                    }
                });
            }
        });

        // Update filters when URL changes
        window.addEventListener('popstate', function() {
            updateActiveFilters();
            initializePriceRangeFromParams();
            initializeMobileFiltersFromParams();
            applyFiltersAndUpdateMap();
        });

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

