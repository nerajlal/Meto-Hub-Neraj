<header class="store-header"
    style="display: flex; flex-direction: column; align-items: stretch; height: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.03); background: #fff; border-bottom: 1px solid var(--border-color); padding: 0.5rem 0;">
    <div class="header-container"
        style="max-width: 1400px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 2rem; gap: 2rem;">
        <a href="{{ route('velvet.home') }}" class="logo"
            style="color: var(--accent-color); font-weight: 800; font-size: 1.6rem; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
            @if($currentTenant->logo)
                <img src="{{ Storage::url($currentTenant->logo) }}" alt="{{ $currentTenant->name }}"
                    style="max-height: 45px; width: auto; object-fit: contain;">
            @else
                <i class="fa-solid fa-leaf"></i>{{ $currentTenant->name ?? 'Fresh Grocery' }}
            @endif
        </a>

        {{-- Search Bar with autocomplete (Desktop) --}}
        <div class="search-bar hide-on-mobile" style="flex-grow: 1; max-width: 600px; position: relative;">
            <form id="t2-search-form" action="{{ route('velvet.all-products') }}" method="GET" autocomplete="off"
                style="display: flex; align-items: center; width: 100%; position: relative;">
                <i class="fa-solid fa-magnifying-glass search-icon"
                    style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none;"></i>
                <input type="text" name="q" id="t2-search-input" class="search-input"
                    placeholder="Search for fresh vegetables, fruits, dairy, or essentials..."
                    value="{{ request('q') }}" autocomplete="off"
                    style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 2.75rem; border: 1.5px solid var(--border-color); border-radius: 99px; outline: none; transition: 0.2s; font-size: 0.95rem;">
                <button type="button" id="t2-search-clear" onclick="clearT2Search()"
                    style="display: none; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); border: none; background: none; padding: 0.25rem; cursor: pointer; color: #9ca3af; font-size: 1rem; line-height: 1; transition: color 0.15s; z-index: 2;"
                    onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#9ca3af'">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </form>
            <div id="t2-search-dropdown" class="t2-search-dropdown" style="display:none;">
                <div id="t2-search-history-section">
                    <div class="t2-sd-label"><i class="fa-solid fa-clock-rotate-left"></i> Recent Searches</div>
                    <ul id="t2-history-list" class="t2-sug-list"></ul>
                </div>
                <div id="t2-suggestions-section" style="display:none;">
                    <div class="t2-sd-label"><i class="fa-solid fa-magnifying-glass"></i> Suggestions</div>
                    <ul id="t2-suggestions-list" class="t2-sug-list"></ul>
                </div>
            </div>
        </div>

        <button class="mobile-search-toggle show-on-mobile"
            style="background: none; border: none; font-size: 1.25rem; color: var(--primary-color); cursor: pointer; display: none;">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>

        <div class="header-actions hide-on-mobile"
            style="display: flex; align-items: center; gap: 1.5rem; flex-shrink: 0;">
            @if(auth()->check())
                <a href="{{ route('account.index') }}" class="action-btn text-decoration-none"
                    style="display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; color: var(--primary-color); text-align: center; gap: 2px; text-transform: none; text-decoration: none; padding: 0.25rem 0.5rem; background: none; border-radius: 0;">
                    <i class="fa-solid fa-user" style="font-size: 1.25rem; color: var(--primary-color);"></i>
                    <span class="action-text" style="color: var(--primary-color);">Account</span>
                </a>
            @else
                <a href="javascript:void(0)" onclick="openCustomerAuthModal()" class="action-btn text-decoration-none"
                    style="display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; color: var(--primary-color); text-align: center; gap: 2px; text-transform: none; text-decoration: none; padding: 0.25rem 0.5rem; background: none; border-radius: 0;">
                    <i class="fa-regular fa-user" style="font-size: 1.25rem; color: var(--primary-color);"></i>
                    <span class="action-text" style="color: var(--primary-color);">Log In</span>
                </a>
            @endif

            <a href="{{ auth()->check() ? route('v3.wishlist') : 'javascript:void(0)' }}"
                onclick="{{ auth()->check() ? '' : 'openCustomerAuthModal()' }}" class="action-btn text-decoration-none"
                style="display: flex; flex-direction: column; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; color: var(--primary-color); position: relative; text-align: center; gap: 2px; text-transform: none; text-decoration: none; padding: 0.25rem 0.5rem; background: none; border-radius: 0;">
                <i class="fa-regular fa-heart" style="font-size: 1.25rem; color: var(--primary-color);"></i>
                <span class="action-text" style="color: var(--primary-color);">Wishlist</span>
                @if(auth()->check())
                    @php
                        $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->where('tenant_id', $currentTenant->id ?? 2)->count();
                    @endphp
                    <span id="wishlist-count" class="badge rounded-circle"
                        style="font-size: 0.6rem; top: 0px; right: 2px; width: 15px; height: 15px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; border: 1px solid #fff; background: #ef4444; position: absolute;">{{ $wishlistCount }}</span>
                @endif
            </a>

            <a href="javascript:void(0)" class="action-btn cart-btn text-decoration-none" onclick="toggleNCart(true)"
                style="display: flex; flex-direction: row; align-items: center; gap: 0.5rem; background: var(--primary-color); color: #fff; padding: 0.6rem 1rem; border-radius: 0.75rem; text-decoration: none; height: fit-content; margin-top: 2px;">
                <i class="fa-solid fa-cart-shopping" style="color: #fff; font-size: 1.1rem;"></i>
                @php
                    $cartCount = \App\Services\CartService::getCount();
                @endphp
                <span id="cart-count" class="cart-count"
                    style="background-color: var(--accent-color); color: var(--primary-color); font-size: 0.75rem; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700;">{{ $cartCount }}</span>
            </a>
        </div>
    </div>

    <!-- Dynamic Horizontal Top Navigation Menu Bar -->
    <nav class="top-nav-menu-bar hide-on-mobile"
        style="border-top: 1px solid var(--border-color); margin-top: 0.5rem; background: #fafafb;">
        <div
            style="max-width: 1400px; margin: 0 auto; display: flex; align-items: center; gap: 2rem; padding: 0.75rem 2rem; overflow-x: auto; white-space: nowrap;">
            <a href="{{ route('velvet.home') }}"
                style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 0.95rem; transition: 0.2s;"
                class="nav-link-item hover-green">
                <i class="fa-solid fa-house me-1"></i> Home
            </a>
            <a href="{{ route('velvet.all-products') }}"
                style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 0.95rem; transition: 0.2s;"
                class="nav-link-item hover-green">
                <i class="fa-solid fa-border-all me-1"></i> All Products
            </a>
            <a href="{{ route('velvet.combos') }}"
                style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 0.95rem; transition: 0.2s;"
                class="nav-link-item hover-green">
                <i class="fa-solid fa-tags me-1"></i> Combos
            </a>
            @php $topCollections = \App\Models\Collection::where('tenant_id', $currentTenant->id ?? 2)->where('status', 1)->get(); @endphp
            @foreach($topCollections as $col)
                <a href="{{ route('velvet.collection', ['slug' => $col->slug]) }}"
                    style="color: var(--primary-color); font-weight: 600; text-decoration: none; font-size: 0.95rem; transition: 0.2s;"
                    class="nav-link-item hover-green">
                    {{ $col->name }}
                </a>
            @endforeach
        </div>
    </nav>
    <!-- Mobile Expandable Search Bar -->
    <div id="mobile-search-container"
        style="display: none; padding: 10px 15px; background: #fff; border-bottom: 1px solid var(--border-color); width: 100%; position: absolute; z-index: 999; top: 100%; left: 0;">
        <form id="t2-mobile-search-form" action="{{ route('velvet.all-products') }}" method="GET" autocomplete="off"
            style="display: flex; align-items: center; width: 100%; position: relative;">
            <i class="fa-solid fa-magnifying-glass search-icon"
                style="position: absolute; left: 1rem; color: #9ca3af;"></i>
            <input type="text" name="q" id="t2-mobile-search-input" class="search-input"
                placeholder="Search for products..." value="{{ request('q') }}" autocomplete="off"
                style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.95rem; outline: none;">
            <button type="button" class="mobile-search-toggle"
                style="position: absolute; right: 0.75rem; border: none; background: none; color: #9ca3af; font-size: 1.25rem; padding: 0.25rem;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </form>
    </div>
</header>

<style>
    @media (max-width: 768px) {
        .hide-on-mobile {
            display: none !important;
        }

        .show-on-mobile {
            display: block !important;
        }
    }

    .hover-green:hover {
        color: var(--accent-color) !important;
    }

    .header-actions .action-btn {
        background: none !important;
        border: none !important;
        flex-direction: column !important;
        padding: 0.25rem 0.5rem !important;
        border-radius: 0 !important;
        gap: 2px !important;
    }

    .header-actions .action-btn:hover {
        background: none !important;
        color: var(--accent-color) !important;
    }

    .header-actions .action-btn:hover i {
        color: var(--accent-color) !important;
    }

    .header-actions .cart-btn {
        background: var(--primary-color) !important;
        color: #fff !important;
        flex-direction: row !important;
        padding: 0.6rem 1rem !important;
        border-radius: 0.75rem !important;
        gap: 0.5rem !important;
    }

    /* Search Dropdown */
    .t2-search-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1.5px solid var(--border-color, #e5e7eb);
        border-radius: 18px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
        z-index: 9999;
        padding: 0.5rem 0;
        animation: t2sdFade 0.15s ease;
    }

    @keyframes t2sdFade {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .t2-sd-label {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        color: #9ca3af;
        padding: 0.5rem 1.25rem 0.2rem;
    }

    .t2-sug-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .t2-sug-list li {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 0.65rem;
        padding: 0.55rem 1.25rem;
        cursor: pointer;
        font-size: 0.93rem;
        color: #374151;
        transition: background 0.12s;
        border-radius: 8px;
        margin: 0 0.25rem;
    }

    .t2-sug-list li:hover {
        background: #f3f4f6;
    }

    .t2-sug-list li .sug-text {
        flex-grow: 1;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .t2-sug-list li .remove-h {
        color: #d1d5db;
        font-size: 0.75rem;
        cursor: pointer;
        padding: 2px 5px;
        border-radius: 4px;
        transition: color 0.15s;
        flex-shrink: 0;
    }

    .t2-sug-list li .remove-h:hover {
        color: #ef4444;
    }

    #t2-search-input:focus {
        border-color: var(--accent-color) !important;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle mobile search
        const mobileSearchToggles = document.querySelectorAll('.mobile-search-toggle');
        const mobileSearchContainer = document.getElementById('mobile-search-container');
        const mobileSearchInput = document.getElementById('t2-mobile-search-input');

        mobileSearchToggles.forEach(toggle => {
            toggle.addEventListener('click', function () {
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
        const HISTORY_KEY = 't2_search_history';
        const MAX_HISTORY = 8;
        const input = document.getElementById('t2-search-input');
        const dropdown = document.getElementById('t2-search-dropdown');
        const histSec = document.getElementById('t2-search-history-section');
        const histList = document.getElementById('t2-history-list');
        const sugSec = document.getElementById('t2-suggestions-section');
        const sugList = document.getElementById('t2-suggestions-list');
        const form = document.getElementById('t2-search-form');

        const allProductNames = @json(\App\Models\Product::where('tenant_id', $currentTenant->id ?? 2)->where('status', 'active')->pluck('title'));

        function getHistory() {
            try { return JSON.parse(localStorage.getItem(HISTORY_KEY)) || []; } catch (e) { return []; }
        }
        function saveHistory(arr) { localStorage.setItem(HISTORY_KEY, JSON.stringify(arr)); }
        function addToHistory(term) {
            if (!term.trim()) return;
            let h = getHistory().filter(i => i.toLowerCase() !== term.toLowerCase());
            h.unshift(term.trim());
            saveHistory(h.slice(0, MAX_HISTORY));
        }

        function esc(s) { return s.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m]); }
        function hl(text, q) {
            const i = text.toLowerCase().indexOf(q.toLowerCase());
            if (i < 0) return esc(text);
            return esc(text.slice(0, i)) + '<strong style="color:var(--accent-color);">' + esc(text.slice(i, i + q.length)) + '</strong>' + esc(text.slice(i + q.length));
        }

        function renderHistory() {
            const h = getHistory();
            histList.innerHTML = '';
            if (h.length === 0) { histSec.style.display = 'none'; return; }
            histSec.style.display = '';
            h.forEach(term => {
                const li = document.createElement('li');
                li.innerHTML = `<i class="fa-solid fa-clock-rotate-left" style="color:#9ca3af;font-size:0.8rem;"></i><span class="sug-text">${esc(term)}</span><span class="remove-h" title="Remove"><i class="fa-solid fa-xmark"></i></span>`;
                li.querySelector('.sug-text').addEventListener('click', () => doSearch(term));
                li.querySelector('.remove-h').addEventListener('click', e => {
                    e.stopPropagation();
                    const f = getHistory().filter(i => i !== term);
                    saveHistory(f);
                    renderHistory();
                    if (f.length === 0 && sugList.children.length === 0) hideDropdown();
                });
                histList.appendChild(li);
            });
        }

        function renderSuggestions(q) {
            sugList.innerHTML = '';
            if (!q.trim()) { sugSec.style.display = 'none'; return; }
            const matches = allProductNames.filter(n => n.toLowerCase().includes(q.toLowerCase())).slice(0, 6);
            if (!matches.length) { sugSec.style.display = 'none'; return; }
            sugSec.style.display = '';
            matches.forEach(name => {
                const li = document.createElement('li');
                li.innerHTML = `<i class="fa-solid fa-magnifying-glass" style="color:#9ca3af;font-size:0.8rem;"></i><span>${hl(name, q)}</span>`;
                li.addEventListener('click', () => doSearch(name));
                sugList.appendChild(li);
            });
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
            if (getHistory().length > 0 || sugList.children.length > 0) showDropdown();
        });

        input.addEventListener('input', () => {
            renderSuggestions(input.value);
            renderHistory();
            if (getHistory().length > 0 || sugList.children.length > 0) showDropdown(); else hideDropdown();
        });

        form.addEventListener('submit', () => { if (input.value.trim()) addToHistory(input.value.trim()); });
        document.addEventListener('click', e => { if (!dropdown.contains(e.target) && e.target !== input) hideDropdown(); });
        input.addEventListener('keydown', e => { if (e.key === 'Escape') hideDropdown(); });

        // Show/hide clear button based on input content
        const clearBtn = document.getElementById('t2-search-clear');
        function updateClearBtn() {
            if (clearBtn) clearBtn.style.display = input.value.trim() ? '' : 'none';
        }
        input.addEventListener('input', updateClearBtn);
        input.addEventListener('focus', updateClearBtn);
        updateClearBtn();

        window.clearT2Search = function () {
            input.value = '';
            updateClearBtn();
            hideDropdown();
            input.focus();
        };
    })();
</script>