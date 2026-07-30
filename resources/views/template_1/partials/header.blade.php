<header class="store-header">
    <div class="header-container">
        <button id="mobile-menu-toggle" class="mobile-toggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <a href="{{ route('v1.home') }}" class="logo" style="color: var(--accent-color); font-weight: 800; font-size: 1.5rem; text-decoration: none; display: flex; align-items: center;">
            @if($currentTenant->logo)
                <img src="{{ Storage::url($currentTenant->logo) }}" alt="{{ $currentTenant->name }}" style="max-height: 45px; width: auto; object-fit: contain;">
            @else
                <i class="fa-solid fa-basket-shopping me-2"></i>{{ $currentTenant->name ?? 'Fresh Grocery' }}
            @endif
        </a>

        @mobileapp
            <!-- Mobile app hides desktop search and header actions -->
        @else
        {{-- Search Bar with autocomplete (Desktop) --}}
        <div class="search-bar hide-on-mobile" style="position: relative; flex: 1; max-width: 520px;">
            <form id="t1-search-form" action="{{ route('v3.all-products') }}" method="GET" autocomplete="off" style="display: flex; align-items: center; width: 100%; position: relative;">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input
                    type="text"
                    name="q"
                    id="t1-search-input"
                    class="search-input"
                    placeholder="Search for fresh vegetables, fruits, dairy..."
                    value="{{ request('q') }}"
                    autocomplete="off"
                    style="padding-right: 2.5rem;"
                >
                <button type="button" id="t1-search-clear" onclick="clearT1Search()" style="display: none; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); border: none; background: none; padding: 0.25rem; cursor: pointer; color: #9ca3af; font-size: 1.1rem; line-height: 1; transition: color 0.15s; z-index: 2;" onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#9ca3af'">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </form>
            <div id="t1-search-dropdown" class="search-dropdown" style="display:none;">
                <div id="t1-search-history-section">
                    <div class="search-dropdown-label"><i class="fa-solid fa-clock-rotate-left"></i> Recent Searches</div>
                    <ul id="t1-history-list" class="search-suggestion-list"></ul>
                </div>
                <div id="t1-suggestions-section" style="display:none;">
                    <div class="search-dropdown-label"><i class="fa-solid fa-magnifying-glass"></i> Suggestions</div>
                    <ul id="t1-suggestions-list" class="search-suggestion-list"></ul>
                </div>
            </div>
        </div>

        <button class="mobile-search-toggle show-on-mobile" style="background: none; border: none; font-size: 1.25rem; color: var(--primary-color); cursor: pointer; display: none;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>

        <div class="header-actions hide-on-mobile" style="display: flex; align-items: center; gap: 0.5rem;">
            @if(auth()->check())
                <div class="user-dropdown" style="position: relative;">
                    <a href="javascript:void(0)" class="action-btn text-decoration-none" onclick="document.getElementById('user-menu').classList.toggle('d-none')" style="display: flex; align-items: center;">
                        <i class="fa-solid fa-user"></i>
                        <span class="action-text">{{ explode(' ', auth()->user()->name)[0] }}</span>
                        <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; margin-left: 0.3rem;"></i>
                    </a>
                    <div id="user-menu" class="d-none" style="position: absolute; top: calc(100% + 10px); right: 0; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border-radius: 12px; min-width: 180px; z-index: 1000; overflow: hidden; border: 1px solid var(--border-color);">
                        <a href="{{ route('account.orders') }}" style="display: block; padding: 0.85rem 1.25rem; color: var(--primary-color); text-decoration: none; font-size: 0.9rem; font-weight: 600; border-bottom: 1px solid var(--border-color);">
                            <i class="fa-solid fa-box-open me-2" style="color: var(--accent-color); width: 20px;"></i> My Orders
                        </a>
                        <form action="{{ route('customer.logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 0.85rem 1.25rem; color: #ef4444; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                                <i class="fa-solid fa-arrow-right-from-bracket me-2" style="width: 20px;"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="javascript:void(0)" onclick="openCustomerAuthModal()" class="action-btn text-decoration-none" style="display: flex; align-items: center;">
                    <i class="fa-regular fa-user"></i>
                    <span class="action-text">Sign In</span>
                </a>
            @endif

            <a href="{{ auth()->check() ? route('v3.wishlist') : 'javascript:void(0)' }}" onclick="{{ auth()->check() ? '' : 'openCustomerAuthModal()' }}" class="action-btn text-decoration-none" style="display: flex; align-items: center;">
                <span style="position: relative; display: inline-flex;">
                    <i class="fa-regular fa-heart"></i>
                    @if(auth()->check())
                        @php
                            $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->where('tenant_id', $currentTenant->id ?? 2)->count();
                        @endphp
                        <span id="wishlist-count" class="badge rounded-circle position-absolute" style="font-size: 0.6rem; top: -8px; right: -8px; width: 15px; height: 15px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; border: 1.5px solid #fff; background: #ef4444; border-radius: 50%;">{{ $wishlistCount }}</span>
                    @endif
                </span>
                <span class="action-text">Wishlist</span>
            </a>

            <a href="javascript:void(0)" class="action-btn cart-btn text-decoration-none" onclick="toggleNCart(true)" style="display: flex; align-items: center;">
                <i class="fa-solid fa-cart-shopping"></i>
                @php
                    $cartCount = \App\Services\CartService::getCount();
                @endphp
                <span id="cart-count" class="cart-count" style="background-color: var(--accent-color);">{{ $cartCount }}</span>
            </a>
        </div>
    </div>
    @endmobileapp
    <!-- Mobile Expandable Search Bar -->
    <div id="mobile-search-container" style="display: none; padding: 10px 15px; background: #fff; border-bottom: 1px solid var(--border-color); width: 100%; position: absolute; z-index: 999; top: 100%; left: 0;">
        <form id="t1-mobile-search-form" action="{{ route('v3.all-products') }}" method="GET" autocomplete="off" style="display: flex; align-items: center; width: 100%; position: relative;">
            <i class="fa-solid fa-magnifying-glass search-icon" style="position: absolute; left: 1rem; color: #9ca3af;"></i>
            <input
                type="text"
                name="q"
                id="t1-mobile-search-input"
                class="search-input"
                placeholder="Search for products..."
                value="{{ request('q') }}"
                autocomplete="off"
                style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.95rem; outline: none;"
            >
            <button type="button" class="mobile-search-toggle" style="position: absolute; right: 0.75rem; border: none; background: none; color: #9ca3af; font-size: 1.25rem; padding: 0.25rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </form>
    </div>
</header>

<style>
.d-none {
    display: none !important;
}
@media (max-width: 768px) {
    .hide-on-mobile {
        display: none !important;
    }
    .show-on-mobile {
        display: block !important;
    }
}
.search-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1.5px solid var(--border-color, #e5e7eb);
    border-radius: 14px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    z-index: 9999;
    padding: 0.5rem 0;
    animation: sdFadeIn 0.15s ease;
}
@keyframes sdFadeIn { from { opacity:0; transform: translateY(-6px); } to { opacity:1; transform: translateY(0); } }
.search-dropdown-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: #9ca3af;
    padding: 0.5rem 1rem 0.25rem;
}
.search-suggestion-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.search-suggestion-list li {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-start !important;
    gap: 0.6rem;
    padding: 0.55rem 1rem;
    cursor: pointer;
    font-size: 0.92rem;
    color: #374151;
    transition: background 0.12s;
}
.search-suggestion-list li:hover {
    background: #f3f4f6;
}
.search-suggestion-list li .suggestion-text {
    flex-grow: 1;
    text-align: left;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.search-suggestion-list li .remove-history {
    color: #d1d5db;
    font-size: 0.75rem;
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 4px;
    transition: color 0.15s;
    flex-shrink: 0;
}
.search-suggestion-list li .remove-history:hover {
    color: #ef4444;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle mobile search
    const mobileSearchToggles = document.querySelectorAll('.mobile-search-toggle');
    const mobileSearchContainer = document.getElementById('mobile-search-container');
    const mobileSearchInput = document.getElementById('t1-mobile-search-input');
    
    mobileSearchToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            if (mobileSearchContainer.style.display === 'none') {
                mobileSearchContainer.style.display = 'block';
                mobileSearchInput.focus();
            } else {
                mobileSearchContainer.style.display = 'none';
            }
        });
    });
});

(function () {
    const HISTORY_KEY = 't1_search_history';
    const MAX_HISTORY = 8;
    const input = document.getElementById('t1-search-input');
    const dropdown = document.getElementById('t1-search-dropdown');
    const historySection = document.getElementById('t1-search-history-section');
    const historyList = document.getElementById('t1-history-list');
    const sugSection = document.getElementById('t1-suggestions-section');
    const sugList = document.getElementById('t1-suggestions-list');
    const form = document.getElementById('t1-search-form');

    // --- Products data from blade for suggestions ---
    const allProductNames = @json(\App\Models\Product::where('tenant_id', $currentTenant->id ?? 2)->where('status','active')->pluck('title'));

    function getHistory() {
        try { return JSON.parse(localStorage.getItem(HISTORY_KEY)) || []; } catch(e) { return []; }
    }
    function saveHistory(arr) {
        localStorage.setItem(HISTORY_KEY, JSON.stringify(arr));
    }
    function addToHistory(term) {
        if (!term.trim()) return;
        let h = getHistory().filter(i => i.toLowerCase() !== term.toLowerCase());
        h.unshift(term.trim());
        if (h.length > MAX_HISTORY) h = h.slice(0, MAX_HISTORY);
        saveHistory(h);
    }

    function renderHistory() {
        const h = getHistory();
        historyList.innerHTML = '';
        if (h.length === 0) { historySection.style.display = 'none'; return; }
        historySection.style.display = '';
        h.forEach(term => {
            const li = document.createElement('li');
            li.innerHTML = `<i class="fa-solid fa-clock-rotate-left" style="color:#9ca3af;font-size:0.8rem;"></i><span class="suggestion-text">${escHtml(term)}</span><span class="remove-history" title="Remove"><i class="fa-solid fa-xmark"></i></span>`;
            li.querySelector('.suggestion-text').addEventListener('click', () => doSearch(term));
            li.querySelector('.remove-history').addEventListener('click', (e) => {
                e.stopPropagation();
                const filtered = getHistory().filter(i => i !== term);
                saveHistory(filtered);
                renderHistory();
                if (filtered.length === 0 && sugList.children.length === 0) hideDropdown();
            });
            historyList.appendChild(li);
        });
    }

    function renderSuggestions(query) {
        sugList.innerHTML = '';
        if (!query.trim()) { sugSection.style.display = 'none'; return; }
        const matches = allProductNames.filter(n => n.toLowerCase().includes(query.toLowerCase())).slice(0, 6);
        if (matches.length === 0) { sugSection.style.display = 'none'; return; }
        sugSection.style.display = '';
        matches.forEach(name => {
            const li = document.createElement('li');
            li.innerHTML = `<i class="fa-solid fa-magnifying-glass" style="color:#9ca3af;font-size:0.8rem;"></i><span>${highlight(name, query)}</span>`;
            li.addEventListener('click', () => doSearch(name));
            sugList.appendChild(li);
        });
    }

    function highlight(text, query) {
        const idx = text.toLowerCase().indexOf(query.toLowerCase());
        if (idx < 0) return escHtml(text);
        return escHtml(text.slice(0, idx)) + '<strong style="color:var(--accent-color);">' + escHtml(text.slice(idx, idx + query.length)) + '</strong>' + escHtml(text.slice(idx + query.length));
    }

    function escHtml(str) {
        return str.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]);
    }

    function showDropdown() { dropdown.style.display = ''; }
    function hideDropdown() { dropdown.style.display = 'none'; }

    function doSearch(term) {
        addToHistory(term);
        input.value = term;
        form.submit();
    }

    input.addEventListener('focus', () => {
        renderHistory();
        renderSuggestions(input.value);
        const hasContent = getHistory().length > 0 || (input.value.trim() && allProductNames.some(n => n.toLowerCase().includes(input.value.toLowerCase())));
        if (hasContent) showDropdown();
    });

    input.addEventListener('input', () => {
        const q = input.value.trim();
        renderSuggestions(q);
        renderHistory();
        const hasHistory = getHistory().length > 0;
        const hasSug = sugList.children.length > 0;
        if (hasHistory || hasSug) showDropdown(); else hideDropdown();
    });

    form.addEventListener('submit', (e) => {
        const q = input.value.trim();
        if (q) addToHistory(q);
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && e.target !== input) hideDropdown();
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') hideDropdown();
    });

    // Show/hide clear button based on input content
    const clearBtn = document.getElementById('t1-search-clear');
    function updateClearBtn() {
        if (clearBtn) clearBtn.style.display = input.value.trim() ? '' : 'none';
    }
    input.addEventListener('input', updateClearBtn);
    input.addEventListener('focus', updateClearBtn);
    updateClearBtn();

    window.clearT1Search = function() {
        input.value = '';
        updateClearBtn();
        hideDropdown();
        input.focus();
    };
})();
</script>
