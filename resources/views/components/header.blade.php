<!-- HEADER -->
<nav class="navbar navbar-light bg-white shadow-sm px-4 py-2 sticky-top top-bar">
    <div class="d-flex align-items-center gap-3">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Đất Việt" height="48">
            <div>
                <div class="fw-bold text-primary mb-0">Đất Việt</div>
                <small class="text-muted">Bản đồ bất động sản địa phương</small>
            </div>
        </a>
    </div>

    <form class="d-none d-md-flex col-5 position-relative" action="{{ route('search') }}" method="GET" id="header-search-form">
        <div class="input-group search-box position-relative">
            <span class="input-group-text bg-white border-end-0 search-icon-wrapper" style="padding: 12px 16px;">
                <i class="bi bi-search search-icon-animated" style="color: #335793;"></i>
            </span>
            <input
                class="form-control border-start-0"
                type="text"
                name="q"
                id="header-search-input"
                value="{{ request('q') }}"
                placeholder="Tìm địa chỉ, từ khóa, sổ đỏ, tên chủ đất..."
                style="padding: 12px 16px; font-size: 14px;"
                aria-label="Tìm kiếm"
                autocomplete="off">
            @if(request('q'))
            <button type="button" class="btn btn-link text-muted p-0 position-absolute end-0 top-50 translate-middle-y me-2"
                    onclick="document.getElementById('header-search-input').value=''; document.getElementById('header-search-form').submit();"
                    style="text-decoration: none; z-index: 10;">
                <i class="bi bi-x-circle"></i>
            </button>
            @endif
        </div>
        <!-- Search suggestions dropdown -->
        <div id="search-suggestions" class="search-suggestions" style="display: none;">
            <div class="search-suggestions-header">
                <small class="text-muted fw-semibold">Gợi ý tìm kiếm</small>
            </div>
            <div class="search-suggestions-list" id="search-suggestions-list"></div>
            <div class="search-suggestions-footer" id="search-recent" style="display: none;">
                <small class="text-muted fw-semibold">Tìm kiếm gần đây</small>
                <div id="search-recent-list"></div>
            </div>
        </div>
    </form>

    @push('styles')
    <style>
    /* Search Icon Animation */
    .search-icon-wrapper {
        position: relative;
    }
    
    .search-icon-animated {
        font-size: 18px;
        color: #335793 !important;
        animation: searchPulse 2s ease-in-out infinite;
        transition: all 0.3s ease;
        display: inline-block;
    }
    
    .search-icon-wrapper:hover .search-icon-animated {
        color: #1e3a5f !important;
        transform: scale(1.15);
        animation: searchBounce 0.6s ease-in-out;
    }
    
    @keyframes searchPulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(1.05);
        }
    }
    
    @keyframes searchBounce {
        0%, 100% {
            transform: scale(1.15);
        }
        50% {
            transform: scale(1.3);
        }
    }
    
    /* Search box focus effect */
    .search-box:focus-within .search-icon-animated {
        color: #1e3a5f !important;
        animation: searchBounce 0.6s ease-in-out;
    }
    
    /* Main dropdown container */
    .search-suggestions {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.05);
        margin-top: 0;
        max-height: 480px;
        overflow: hidden;
        z-index: 1050;
        border: 1px solid rgba(0, 0, 0, 0.08);
        animation: slideDownFade 0.2s ease-out;
    }

    @keyframes slideDownFade {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Custom scrollbar */
    .search-suggestions::-webkit-scrollbar {
        width: 6px;
    }

    .search-suggestions::-webkit-scrollbar-track {
        background: transparent;
    }

    .search-suggestions::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 3px;
    }

    .search-suggestions::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.3);
    }

    /* Header section */
    .search-suggestions-header {
        padding: 12px 16px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        background: #fafbfc;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .search-suggestions-header small {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        display: block;
    }

    /* Suggestions list */
    .search-suggestions-list {
        padding: 4px 0;
        max-height: 360px;
        overflow-y: auto;
    }

    /* Suggestion item */
    .search-suggestion-item {
        padding: 12px 16px;
        cursor: pointer;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        transition: background-color 0.15s ease;
        position: relative;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        user-select: none;
    }

    .search-suggestion-item:last-child {
        border-bottom: none;
    }

    .search-suggestion-item:hover {
        background-color: #f8f9fa;
    }

    .search-suggestion-item:active {
        background-color: #f1f3f5;
    }

    /* Icon */
    .search-suggestion-item i {
        color: #64748b;
        font-size: 18px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 8px;
        flex-shrink: 0;
        transition: all 0.15s ease;
        margin-top: 2px;
    }

    .search-suggestion-item:hover i {
        background: #e2e8f0;
        color: #475569;
    }

    /* Icon colors by type */
    .search-suggestion-item i.bi-geo-alt,
    .search-suggestion-item i.bi-geo-alt-fill {
        color: #0ea5e9;
        background: #e0f2fe;
    }

    .search-suggestion-item:hover i.bi-geo-alt,
    .search-suggestion-item:hover i.bi-geo-alt-fill {
        background: #bae6fd;
    }

    .search-suggestion-item i.bi-tag {
        color: #8b5cf6;
        background: #f3e8ff;
    }

    .search-suggestion-item:hover i.bi-tag {
        background: #e9d5ff;
    }

    .search-suggestion-item i.bi-building {
        color: #06b6d4;
        background: #cffafe;
    }

    .search-suggestion-item:hover i.bi-building {
        background: #a5f3fc;
    }

    .search-suggestion-item i.bi-house-door {
        color: #10b981;
        background: #d1fae5;
    }

    .search-suggestion-item:hover i.bi-house-door {
        background: #a7f3d0;
    }

    /* Content */
    .search-suggestion-item-content {
        flex: 1;
        min-width: 0;
        padding-top: 2px;
    }

    .search-suggestion-item-title {
        font-weight: 500;
        font-size: 14px;
        color: #1e293b;
        margin-bottom: 4px;
        line-height: 1.5;
        word-break: break-word;
    }

    .search-suggestion-item:hover .search-suggestion-item-title {
        color: #0f172a;
    }

    .search-suggestion-item-subtitle {
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
        word-break: break-word;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .search-suggestion-item-subtitle::before {
        content: '';
        width: 3px;
        height: 3px;
        background: #cbd5e1;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Footer section */
    .search-suggestions-footer {
        padding: 12px 16px;
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        background: #fafbfc;
        position: sticky;
        bottom: 0;
    }

    .search-suggestions-footer small {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        display: block;
        margin-bottom: 8px;
    }

    /* Recent search items */
    .search-recent-item {
        padding: 10px 12px;
        margin: 4px 0;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.15s ease;
        position: relative;
    }

    .search-recent-item:hover {
        background: #f8f9fa;
        border-color: rgba(0, 0, 0, 0.1);
    }

    .search-recent-item-text {
        font-size: 13px;
        font-weight: 400;
        color: #475569;
        flex: 1;
        position: relative;
        z-index: 1;
        line-height: 1.4;
    }

    .search-recent-item:hover .search-recent-item-text {
        color: #334155;
        font-weight: 500;
    }

    /* Remove button */
    .search-recent-item-remove {
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        font-size: 12px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.15s ease;
        flex-shrink: 0;
        position: relative;
        z-index: 2;
        opacity: 0.6;
    }

    .search-recent-item:hover .search-recent-item-remove {
        opacity: 1;
    }

    .search-recent-item-remove:hover {
        color: #ffffff;
        background: #ef4444;
        opacity: 1;
    }

    .search-recent-item-remove:active {
        transform: scale(0.95);
    }

    .search-recent-item-remove i {
        font-size: 12px;
        line-height: 1;
    }

    /* Empty state */
    .search-suggestion-item.empty-state {
        padding: 32px 20px;
        text-align: center;
        cursor: default;
        border-bottom: none;
    }

    .search-suggestion-item.empty-state:hover {
        background: transparent;
    }

    .search-suggestion-item.empty-state .text-muted {
        color: #94a3b8;
        font-style: normal;
        font-size: 13px;
    }

    /* Highlight matching text */
    .search-suggestion-item-title mark {
        background: #fef3c7;
        color: #92400e;
        padding: 1px 3px;
        border-radius: 3px;
        font-weight: 600;
    }
    </style>
    @endpush

    @push('scripts')
    <script>
    // Search Autocomplete
    (function() {
        const searchInput = document.getElementById('header-search-input');
        const suggestionsDiv = document.getElementById('search-suggestions');
        const suggestionsList = document.getElementById('search-suggestions-list');
        const recentDiv = document.getElementById('search-recent');
        const recentList = document.getElementById('search-recent-list');
        let searchTimeout;
        let currentSuggestions = [];
        let isDropdownFocused = false;

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Escape for HTML attributes
        function escapeAttr(text) {
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#x27;');
        }

        // Highlight matching text safely
        function highlightText(text, query) {
            if (!query || query.length === 0) return escapeHtml(text);
            const escapedText = escapeHtml(text);
            const escapedQuery = escapeHtml(query);
            const regex = new RegExp(`(${escapedQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return escapedText.replace(regex, '<mark>$1</mark>');
        }

        // Load recent searches from localStorage
        function loadRecentSearches() {
            try {
                const recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                if (Array.isArray(recent) && recent.length > 0) {
                    recentDiv.style.display = 'block';
                    recentList.innerHTML = '';
                    recent.slice(0, 5).forEach(term => {
                        if (!term || typeof term !== 'string') return;
                        const item = document.createElement('div');
                        item.className = 'search-recent-item';
                        const escapedTerm = escapeHtml(term);
                        const escapedAttr = escapeAttr(term);
                        
                        item.innerHTML = `
                            <span class="search-recent-item-text">${escapedTerm}</span>
                            <button type="button" class="search-recent-item-remove" data-term="${escapedAttr}" aria-label="Xóa tìm kiếm">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        `;
                        
                        // Handle remove button click
                        const removeBtn = item.querySelector('.search-recent-item-remove');
                        removeBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            removeRecentSearch(term);
                        });
                        
                        // Handle item click
                        item.addEventListener('click', (e) => {
                            if (e.target.closest('.search-recent-item-remove')) return;
                            searchInput.value = term;
                            suggestionsDiv.style.display = 'none';
                            document.getElementById('header-search-form').submit();
                        });
                        
                        recentList.appendChild(item);
                    });
                } else {
                    recentDiv.style.display = 'none';
                }
            } catch (error) {
                console.error('Error loading recent searches:', error);
                recentDiv.style.display = 'none';
            }
        }

        // Save search to recent
        function saveRecentSearch(term) {
            if (!term || typeof term !== 'string' || term.trim().length < 2) return;
            try {
                const trimmedTerm = term.trim();
                let recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                if (!Array.isArray(recent)) recent = [];
                recent = recent.filter(t => t !== trimmedTerm && t && typeof t === 'string');
                recent.unshift(trimmedTerm);
                recent = recent.slice(0, 10);
                localStorage.setItem('recentSearches', JSON.stringify(recent));
            } catch (error) {
                console.error('Error saving recent search:', error);
            }
        }

        // Remove recent search
        window.removeRecentSearch = function(term) {
            if (!term || typeof term !== 'string') return;
            try {
                let recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                if (!Array.isArray(recent)) return;
                recent = recent.filter(t => t !== term);
                localStorage.setItem('recentSearches', JSON.stringify(recent));
                loadRecentSearches();
                // If dropdown is visible and no suggestions, keep it open
                if (suggestionsDiv.style.display === 'block' && currentSuggestions.length === 0) {
                    suggestionsDiv.style.display = 'block';
                }
            } catch (error) {
                console.error('Error removing recent search:', error);
            }
        };

        // Fetch search suggestions
        function fetchSuggestions(query) {
            if (!query || query.length < 2) {
                suggestionsDiv.style.display = 'none';
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    currentSuggestions = Array.isArray(data.suggestions) ? data.suggestions : [];
                    renderSuggestions();
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    currentSuggestions = [];
                    renderSuggestions();
                });
            }, 300);
        }

        // Render suggestions
        function renderSuggestions() {
            suggestionsList.innerHTML = '';
            
            if (currentSuggestions.length === 0) {
                const emptyItem = document.createElement('div');
                emptyItem.className = 'search-suggestion-item empty-state';
                emptyItem.innerHTML = '<span class="text-muted">Không tìm thấy gợi ý</span>';
                suggestionsList.appendChild(emptyItem);
            } else {
                currentSuggestions.slice(0, 8).forEach(suggestion => {
                    if (!suggestion) return;
                    
                    const item = document.createElement('div');
                    item.className = 'search-suggestion-item';

                    // Choose icon based on type
                    let iconClass = 'bi-geo-alt';
                    if (suggestion.type === 'category') {
                        iconClass = 'bi-tag';
                    } else if (suggestion.type === 'city') {
                        iconClass = 'bi-building';
                    } else if (suggestion.type === 'district') {
                        iconClass = 'bi-geo-alt-fill';
                    } else if (suggestion.type === 'listing') {
                        iconClass = 'bi-house-door';
                    }

                    // Get title and subtitle safely
                    const title = suggestion.title || suggestion.text || '';
                    const subtitle = suggestion.subtitle || '';
                    const query = searchInput.value.trim();

                    // Highlight matching text
                    const highlightedTitle = highlightText(title, query);

                    // Build HTML safely
                    item.innerHTML = `
                        <i class="bi ${iconClass}"></i>
                        <div class="search-suggestion-item-content">
                            <div class="search-suggestion-item-title">${highlightedTitle}</div>
                            ${subtitle ? `<div class="search-suggestion-item-subtitle">${escapeHtml(subtitle)}</div>` : ''}
                        </div>
                    `;
                    
                    // Handle click
                    item.addEventListener('click', () => {
                        searchInput.value = title;
                        saveRecentSearch(title);
                        suggestionsDiv.style.display = 'none';
                        document.getElementById('header-search-form').submit();
                    });
                    
                    suggestionsList.appendChild(item);
                });
            }
            
            // Show/hide recent searches based on suggestions
            if (currentSuggestions.length > 0) {
                recentDiv.style.display = 'none';
            } else {
                loadRecentSearches();
            }
            
            suggestionsDiv.style.display = 'block';
        }

        // Event listeners
        if (searchInput) {
            // Track dropdown focus
            suggestionsDiv.addEventListener('mouseenter', () => {
                isDropdownFocused = true;
            });
            
            suggestionsDiv.addEventListener('mouseleave', () => {
                isDropdownFocused = false;
            });

            // Input event
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                if (query.length >= 2) {
                    fetchSuggestions(query);
                } else {
                    currentSuggestions = [];
                    suggestionsList.innerHTML = '';
                    if (query.length === 0) {
                        loadRecentSearches();
                        suggestionsDiv.style.display = 'block';
                    } else {
                        suggestionsDiv.style.display = 'none';
                    }
                }
            });

            // Focus event
            searchInput.addEventListener('focus', function() {
                const query = this.value.trim();
                if (query.length < 2) {
                    loadRecentSearches();
                    suggestionsDiv.style.display = 'block';
                } else if (currentSuggestions.length > 0) {
                    suggestionsDiv.style.display = 'block';
                }
            });

            // Blur event - delay to allow clicks
            searchInput.addEventListener('blur', function() {
                setTimeout(() => {
                    if (!isDropdownFocused) {
                        suggestionsDiv.style.display = 'none';
                    }
                }, 200);
            });

            // Submit form
            const searchForm = document.getElementById('header-search-form');
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    const query = searchInput.value.trim();
                    if (query.length >= 2) {
                        saveRecentSearch(query);
                    }
                });
            }

            // Keyboard navigation
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    suggestionsDiv.style.display = 'none';
                    searchInput.blur();
                }
            });
        }

        // Load recent searches on page load
        loadRecentSearches();
    })();
    </script>
    @endpush

    <div class="d-flex align-items-center gap-2">
        @auth('partner')
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle"></i> {{ Auth::guard('partner')->user()->name ?? Auth::guard('partner')->user()->phone }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('listings.my-listings') }}">
                        <i class="bi bi-house-door"></i> Tin của tôi
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('partner.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="bi bi-box-arrow-right"></i> Đăng xuất
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @else
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                Đăng nhập
            </button>
        @endauth

        @auth('partner')
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postModal">
            <i class="bi bi-plus-circle"></i> Đăng tin rao bán
        </button>
        @else
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal" onclick="setRedirectAfterLogin('postModal')">
            <i class="bi bi-plus-circle"></i> Đăng tin rao bán
        </button>
        @endauth
    </div>
</nav>

