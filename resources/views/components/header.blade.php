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
            <span class="input-group-text bg-white border-end-0" style="padding: 12px 16px;">
                <i class="bi bi-search" style="color: #64748b;"></i>
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
    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        margin-top: 8px;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }
    
    .search-suggestions-header {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f3f5;
    }
    
    .search-suggestions-list {
        padding: 8px 0;
    }
    
    .search-suggestion-item {
        padding: 12px 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: background 0.2s;
    }
    
    .search-suggestion-item:hover {
        background: #f8f9fa;
    }
    
    .search-suggestion-item i {
        color: #64748b;
        font-size: 18px;
    }
    
    .search-suggestion-item-content {
        flex: 1;
    }
    
    .search-suggestion-item-title {
        font-weight: 600;
        color: #1a202c;
        margin-bottom: 2px;
    }
    
    .search-suggestion-item-subtitle {
        font-size: 12px;
        color: #64748b;
    }
    
    .search-suggestions-footer {
        padding: 12px 16px;
        border-top: 1px solid #f1f3f5;
    }
    
    .search-recent-item {
        padding: 8px 12px;
        margin: 4px 0;
        background: #f8f9fa;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.2s;
    }
    
    .search-recent-item:hover {
        background: #e9ecef;
    }
    
    .search-recent-item-text {
        font-size: 13px;
        color: #4a5568;
    }
    
    .search-recent-item-remove {
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 0;
        font-size: 14px;
    }
    
    .search-recent-item-remove:hover {
        color: #ef4444;
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
        
        // Load recent searches from localStorage
        function loadRecentSearches() {
            const recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
            if (recent.length > 0) {
                recentDiv.style.display = 'block';
                recentList.innerHTML = '';
                recent.slice(0, 5).forEach(term => {
                    const item = document.createElement('div');
                    item.className = 'search-recent-item';
                    item.innerHTML = `
                        <span class="search-recent-item-text">${term}</span>
                        <button type="button" class="search-recent-item-remove" onclick="removeRecentSearch('${term}')">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                    item.onclick = (e) => {
                        if (e.target.closest('.search-recent-item-remove')) return;
                        searchInput.value = term;
                        document.getElementById('header-search-form').submit();
                    };
                    recentList.appendChild(item);
                });
            } else {
                recentDiv.style.display = 'none';
            }
        }
        
        // Save search to recent
        function saveRecentSearch(term) {
            if (!term || term.trim().length < 2) return;
            let recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
            recent = recent.filter(t => t !== term);
            recent.unshift(term);
            recent = recent.slice(0, 10);
            localStorage.setItem('recentSearches', JSON.stringify(recent));
        }
        
        // Remove recent search
        window.removeRecentSearch = function(term) {
            let recent = JSON.parse(localStorage.getItem('recentSearches') || '[]');
            recent = recent.filter(t => t !== term);
            localStorage.setItem('recentSearches', JSON.stringify(recent));
            loadRecentSearches();
        };
        
        // Fetch search suggestions
        function fetchSuggestions(query) {
            if (query.length < 2) {
                suggestionsDiv.style.display = 'none';
                return;
            }
            
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    currentSuggestions = data.suggestions || [];
                    renderSuggestions();
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    suggestionsDiv.style.display = 'none';
                });
            }, 300);
        }
        
        // Render suggestions
        function renderSuggestions() {
            if (currentSuggestions.length === 0) {
                suggestionsList.innerHTML = '<div class="search-suggestion-item"><span class="text-muted">Không tìm thấy gợi ý</span></div>';
            } else {
                suggestionsList.innerHTML = '';
                currentSuggestions.slice(0, 5).forEach(suggestion => {
                    const item = document.createElement('div');
                    item.className = 'search-suggestion-item';
                    item.innerHTML = `
                        <i class="bi bi-geo-alt"></i>
                        <div class="search-suggestion-item-content">
                            <div class="search-suggestion-item-title">${suggestion.title || suggestion.text}</div>
                            ${suggestion.subtitle ? `<div class="search-suggestion-item-subtitle">${suggestion.subtitle}</div>` : ''}
                        </div>
                    `;
                    item.onclick = () => {
                        searchInput.value = suggestion.title || suggestion.text;
                        saveRecentSearch(suggestion.title || suggestion.text);
                        document.getElementById('header-search-form').submit();
                    };
                    suggestionsList.appendChild(item);
                });
            }
            suggestionsDiv.style.display = 'block';
        }
        
        // Event listeners
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                if (query.length >= 2) {
                    fetchSuggestions(query);
                } else {
                    suggestionsDiv.style.display = 'none';
                    loadRecentSearches();
                }
            });
            
            searchInput.addEventListener('focus', function() {
                if (this.value.trim().length < 2) {
                    loadRecentSearches();
                    suggestionsDiv.style.display = 'block';
                }
            });
            
            searchInput.addEventListener('blur', function() {
                // Delay to allow click on suggestions
                setTimeout(() => {
                    suggestionsDiv.style.display = 'none';
                }, 200);
            });
            
            // Submit form
            document.getElementById('header-search-form')?.addEventListener('submit', function(e) {
                const query = searchInput.value.trim();
                if (query.length >= 2) {
                    saveRecentSearch(query);
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

