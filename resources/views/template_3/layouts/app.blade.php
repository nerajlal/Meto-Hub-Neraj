<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $currentTenant->store_name ?? 'Store')</title>
    
    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/template_3.css') }}">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @yield('styles')
</head>
<body>
    
    <!-- Glassmorphism Header -->
    <header class="store-header">
        <div class="header-container">
            <a href="{{ route('v3.home') }}" class="logo">
                @if(isset($currentTenant) && $currentTenant->logo)
                    <img src="{{ asset('storage/' . $currentTenant->logo) }}" alt="Logo" style="height: 40px;">
                @else
                    <i class="fa-solid fa-leaf"></i> {{ $currentTenant->store_name ?? 'FreshMarket' }}
                @endif
            </a>
            
            <div class="search-bar" style="position: relative;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <form action="{{ route('v3.all-products') }}" method="GET" id="t3-search-form" autocomplete="off" style="width: 100%;">
                    <input type="text" name="q" id="t3-search-input" placeholder="Search for groceries, vegetables, meat..." value="{{ request('q') }}">
                </form>
                <div id="t3-search-dropdown" class="search-dropdown" style="display:none; position: absolute; top: calc(100% + 5px); left: 0; right: 0; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid var(--border-color); z-index: 1000; padding: 0.5rem 0;">
                    <div id="t3-search-history-section">
                        <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #9ca3af; padding: 0.5rem 1rem;">
                            <i class="fa-solid fa-clock-rotate-left"></i> Recent Searches
                        </div>
                        <ul id="t3-history-list" style="list-style: none; margin: 0; padding: 0;"></ul>
                    </div>
                    <div id="t3-suggestions-section" style="display:none;">
                        <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #9ca3af; padding: 0.5rem 1rem;">
                            <i class="fa-solid fa-magnifying-glass"></i> Suggestions
                        </div>
                        <ul id="t3-suggestions-list" style="list-style: none; margin: 0; padding: 0;"></ul>
                    </div>
                </div>
            </div>
            
            <div class="header-actions">
                @auth
                <button class="action-btn" onclick="window.location.href='{{ route('account.index') }}'" title="My Account">
                    <i class="fa-regular fa-user"></i>
                </button>
                @else
                <button class="action-btn" onclick="window.location.href='{{ route('login') }}'" title="Log In">
                    <i class="fa-regular fa-user"></i>
                </button>
                @endauth
                <button class="action-btn" onclick="window.location.href='{{ route('v3.wishlist') }}'">
                    <i class="fa-regular fa-heart"></i>
                </button>
                <button class="action-btn" onclick="toggleCartSidebar()">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="cart-count" id="cart-count-badge">0</span>
                </button>
            </div>
        </div>
    </header>
    
    <!-- Top Navigation -->
    <nav style="background: #FFFFFF; border-bottom: 1px solid var(--border-color); padding: 0.75rem 0; overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch;">
        <div class="container" style="display: flex; gap: 2rem; align-items: center;">
            <a href="{{ route('v3.home') }}" style="color: var(--text-main); font-weight: 600; text-decoration: none; font-size: 0.95rem;">Home</a>
            <a href="{{ route('v3.all-products') }}" style="color: var(--text-main); font-weight: 600; text-decoration: none; font-size: 0.95rem;">All Products</a>
            <a href="{{ route('v3.combos') }}" style="color: var(--text-main); font-weight: 600; text-decoration: none; font-size: 0.95rem;">Weekly Deals</a>
            @php
                $collections = \App\Models\Collection::where('tenant_id', session('tenant_id', 1))->where('status', 1)->get();
            @endphp
            @foreach($collections as $cat)
                <a href="{{ route('v3.collection', ['slug' => $cat->slug]) }}" style="color: var(--text-muted); font-weight: 500; text-decoration: none; font-size: 0.95rem;">{{ $cat->name }}</a>
            @endforeach
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="store-footer">
        <div class="container footer-grid">
            <div class="footer-col">
                <a href="{{ route('v3.home') }}" class="logo" style="margin-bottom: 1rem;">
                    @if(isset($currentTenant) && $currentTenant->logo)
                        <img src="{{ asset('storage/' . $currentTenant->logo) }}" alt="Logo" style="height: 40px;">
                    @else
                        <i class="fa-solid fa-leaf"></i> {{ $currentTenant->store_name ?? 'FreshMarket' }}
                    @endif
                </a>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                    Freshness, Quality, and Everything in between delivered right to your doorstep.
                </p>
            </div>
            
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('v3.home') }}">Home</a></li>
                    <li><a href="{{ route('v3.all-products') }}">All Products</a></li>
                    <li><a href="#">Categories</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Support</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('v3.about') }}">About Us</a></li>
                    <li><a href="{{ route('v3.contact') }}">Contact Support</a></li>
                    <li><a href="{{ route('v3.shipping-policy') }}">Shipping Policy</a></li>
                    <li><a href="{{ route('v3.return-policy') }}">Return Policy</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Newsletter</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem;">Subscribe to get updates on our latest offers.</p>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="email" placeholder="Your email address" style="flex-grow: 1; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    <button style="padding: 0.75rem 1rem; background: var(--primary-color); color: white; border: none; border-radius: 0.5rem; cursor: pointer;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Sidebar Component (Assuming it exists or we build it) -->
    @include('template_1.partials.cart_drawer') <!-- Reusing template_1 cart for now to save time -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Floating Cart Pill Component -->
    @include('template_3.partials.floating_cart')

    <script>
        function toggleCartSidebar() {
            $('#cart-drawer-n').addClass('open');
            $('#cart-drawer-overlay-n').fadeIn(300);
            $('body').css('overflow', 'hidden');
            refreshNCart();
        }

        $(document).on('click', '#close-cart-n, #cart-drawer-overlay-n', function () {
            $('#cart-drawer-n').removeClass('open');
            $('#cart-drawer-overlay-n').fadeOut(300);
            $('body').css('overflow', '');
        });

        function refreshNCart() {
            $('#cart-drawer-body-n').html('<div class="cart-loader-n text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-success"></i></div>');
            $.get("{{ route('cart.fetch') }}", { theme: 'template_1' }, function (html) {
                $('#cart-drawer-body-n').html(html);
            });
        }

        window.updateNCartQty = function (key, delta) {
            const $item = $(`.n-cart-item:has(button[onclick*="${key}"])`);
            const currentQty = parseInt($item.find('.n-qty-wrap span').text() || '1');
            const newQty = currentQty + delta;
            if (newQty < 1) return;

            $.post("{{ route('cart.update') }}", {
                _token: "{{ csrf_token() }}",
                id: key,
                quantity: newQty
            }, function (response) {
                if (response.success) {
                    $('#cart-count-badge').text(response.cartCount);
                    refreshNCart();
                }
            });
        }

        window.removeNCartItem = function (key) {
            $.post("{{ route('cart.remove') }}", {
                _token: "{{ csrf_token() }}",
                id: key
            }, function (response) {
                if (response.success) {
                    $('#cart-count-badge').text(response.cartCount);
                    refreshNCart();
                }
            });
        }
        


        window.showCartToast = function(msg = 'Added to Cart successfully') {
            const toast = document.createElement('div');
            toast.textContent = msg;
            toast.style.cssText = 'position: fixed; bottom: 80px; left: 50%; transform: translateX(-50%); background: #10B981; color: white; padding: 10px 20px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; z-index: 9999; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: opacity 0.3s;';
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 2000);
        };
        
        window.toggleWishlist = function (event, productId) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            @if(!auth()->check())
                window.location.href = "{{ route('login') }}";
                return;
            @endif

            $.ajax({
                url: "{{ route('v3.wishlist.toggle') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: productId
                },
                success: function (response) {
                    if (response.success) {
                        const btns = document.querySelectorAll(`button[onclick*="toggleWishlist"][onclick*="${productId}"]`);
                        btns.forEach(btn => {
                            const icon = btn.querySelector('i');
                            if (response.wishlisted) {
                                icon.className = 'fa-solid fa-heart';
                                btn.classList.add('active');
                            } else {
                                icon.className = 'fa-regular fa-heart';
                                btn.classList.remove('active');
                            }
                        });
                    }
                },
                error: function () {
                    alert('Error updating wishlist. Please try again.');
                }
            });
        };
        // Search Auto-complete and History Logic
        (function () {
            const HISTORY_KEY = 't3_search_history';
            const MAX_HISTORY = 8;
            const input = document.getElementById('t3-search-input');
            const dropdown = document.getElementById('t3-search-dropdown');
            const historySection = document.getElementById('t3-search-history-section');
            const historyList = document.getElementById('t3-history-list');
            const sugSection = document.getElementById('t3-suggestions-section');
            const sugList = document.getElementById('t3-suggestions-list');
            const form = document.getElementById('t3-search-form');
            
            if (!input || !dropdown) return;

            // Optional: get products if available, or just use history for now
            @php
                $tenantId = session('active_tenant_id') ?? 1;
                $productTitles = \App\Models\Product::where('tenant_id', $tenantId)->where('status', 'active')->pluck('title');
            @endphp
            const allProductNames = @json($productTitles);

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
                    li.style.cssText = "display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 1rem; cursor: pointer; transition: background 0.15s; font-size: 0.95rem; color: #374151;";
                    li.innerHTML = `<div style="flex-grow: 1; display: flex; align-items: center; gap: 0.75rem;"><i class="fa-solid fa-clock-rotate-left" style="color:#9ca3af; font-size:0.85rem;"></i><span class="suggestion-text">${escHtml(term)}</span></div><span class="remove-history" style="color:#d1d5db; padding:2px; font-size: 0.8rem;"><i class="fa-solid fa-xmark"></i></span>`;
                    
                    li.addEventListener('mouseover', () => li.style.background = '#f3f4f6');
                    li.addEventListener('mouseout', () => li.style.background = 'transparent');

                    li.querySelector('.suggestion-text').addEventListener('click', (e) => { e.stopPropagation(); doSearch(term); });
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
                    li.style.cssText = "display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1rem; cursor: pointer; transition: background 0.15s; font-size: 0.95rem; color: #374151;";
                    li.innerHTML = `<i class="fa-solid fa-magnifying-glass" style="color:#9ca3af; font-size:0.85rem;"></i><span>${highlight(name, query)}</span>`;
                    li.addEventListener('mouseover', () => li.style.background = '#f3f4f6');
                    li.addEventListener('mouseout', () => li.style.background = 'transparent');
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
        })();
    </script>
    @yield('scripts')
</body>
</html>
