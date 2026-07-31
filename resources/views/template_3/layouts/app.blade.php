<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $currentTenant->store_name ?? 'Store')</title>
    <link rel="icon" href="{{ isset($currentTenant) && $currentTenant->favicon ? Storage::url($currentTenant->favicon) : 'data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>S</text></svg>' }}">
    
    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/template_3.css') }}">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        @media (max-width: 768px) {
            .mobile-search-toggle-btn {
                display: flex !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    
    @mobileapp
    <style>
        body, main { padding-top: 0 !important; }
    </style>
    <!-- Dynamic Mobile App Header -->
    <header id="app-dynamic-header" style="background: #fff; position: sticky; top: 0; z-index: 1000; transition: box-shadow 0.3s ease;">
        <style>
            @keyframes slideTicker {
                0%, 25% { transform: translateY(0); }
                33%, 58% { transform: translateY(-26px); }
                66%, 91% { transform: translateY(-52px); }
                100% { transform: translateY(-78px); }
            }
        </style>
        <!-- Top Row: Logo & Promo Ticker -->
        <div id="app-header-top-row" style="display: flex; justify-content: space-between; align-items: center; padding: 5px 15px 10px 15px; transition: all 0.3s ease; overflow: hidden; transform-origin: top; position: relative;">
            <a href="{{ route('v1.home') }}" class="logo" style="text-decoration: none; color: var(--text-main); font-weight: 800; display: flex; align-items: center; z-index: 2;">
                @if(isset($currentTenant) && $currentTenant->logo)
                    <img src="{{ asset('storage/' . $currentTenant->logo) }}" alt="Logo" style="height: 40px; max-width: 140px; object-fit: contain;">
                @else
                    <span style="font-size: 1.2rem;">{{ $currentTenant->name ?? 'FreshMarket' }}</span>
                @endif
            </a>
            
            <!-- Animated Promotional Ticker -->
            <div style="background: rgba(16, 185, 129, 0.1); color: var(--accent-color); padding: 0 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; height: 26px; overflow: hidden; z-index: 1;">
                <div style="animation: slideTicker 12s infinite cubic-bezier(0.4, 0, 0.2, 1); text-align: center;">
                    <div style="height: 26px; line-height: 26px; white-space: nowrap;">🚚 Free Delivery</div>
                    <div style="height: 26px; line-height: 26px; white-space: nowrap;">⏱️ Book your slot</div>
                    <div style="height: 26px; line-height: 26px; white-space: nowrap;">✨ Fresh Everyday</div>
                    <div style="height: 26px; line-height: 26px; white-space: nowrap;">🚚 Free Delivery</div>
                </div>
            </div>
        </div>

        <!-- Permanent Search Bar -->
        <div id="app-header-search-row" style="padding: 0 15px 12px 15px;">
            <form action="{{ route('v1.all-products') }}" method="GET" id="app-search-form" autocomplete="off" style="position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1.25rem; color: #9ca3af; top: 50%; transform: translateY(-50%); font-size: 1rem;"></i>
                <input
                    type="text"
                    name="q"
                    id="app-search-input"
                    placeholder="Search for groceries, vegetables, meat..."
                    value="{{ request('q') }}"
                    autocomplete="off"
                    style="width: 100%; padding: 0.85rem 1rem 0.85rem 3rem; border: 1px solid transparent; border-radius: 99px; font-size: 0.95rem; outline: none; color: var(--text-main); background: #f1f5f9; transition: all 0.3s ease; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);"
                    onfocus="this.style.borderColor='var(--accent-color)'; this.style.background='#ffffff'; this.style.boxShadow='0 0 0 4px rgba(16, 185, 129, 0.1)';"
                    onblur="this.style.borderColor='transparent'; this.style.background='#f1f5f9'; this.style.boxShadow='inset 0 2px 4px rgba(0,0,0,0.02)';"
                >
            </form>
            <div id="app-mobile-search-dropdown" class="search-dropdown" style="display:none; position: absolute; top: calc(100% - 5px); left: 15px; right: 15px; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid var(--border-color); z-index: 1000; padding: 0.5rem 0;">
                <div id="app-mobile-search-history-section">
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #9ca3af; padding: 0.5rem 1rem;">
                        <i class="fa-solid fa-clock-rotate-left"></i> Recent Searches
                    </div>
                    <ul id="app-mobile-history-list" style="list-style: none; margin: 0; padding: 0;"></ul>
                </div>
                <div id="app-mobile-suggestions-section" style="display:none;">
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #9ca3af; padding: 0.5rem 1rem;">
                        <i class="fa-solid fa-magnifying-glass"></i> Suggestions
                    </div>
                    <ul id="app-mobile-suggestions-list" style="list-style: none; margin: 0; padding: 0;"></ul>
                </div>
            </div>
        </div>
    </header>

    <script>
        // Shrink header on scroll
        document.addEventListener("DOMContentLoaded", function() {
            const topRow = document.getElementById("app-header-top-row");
            const header = document.getElementById("app-dynamic-header");
            
            if (topRow && header) {
                window.addEventListener("scroll", function() {
                    let st = window.pageYOffset || document.documentElement.scrollTop;
                    if (st > 40) {
                        // Scroll down: hide top row
                        topRow.style.height = "0px";
                        topRow.style.paddingTop = "0px";
                        topRow.style.paddingBottom = "0px";
                        topRow.style.opacity = "0";
                        header.style.boxShadow = "0 4px 15px rgba(0,0,0,0.05)";
                    } else {
                        // Scroll up to top: show top row
                        topRow.style.height = "55px";
                        topRow.style.paddingTop = "10px";
                        topRow.style.paddingBottom = "10px";
                        topRow.style.opacity = "1";
                        header.style.boxShadow = "none";
                    }
                }, { passive: true });
            }
        });
    </script>
    @else
    <!-- Glassmorphism Header (Web) -->
    <header class="store-header" style="padding-top: env(safe-area-inset-top, 0px);">
        <div class="header-container">
            <a href="{{ route('v1.home') }}" class="logo">
                @if(isset($currentTenant) && $currentTenant->logo)
                    <img src="{{ asset('storage/' . $currentTenant->logo) }}" alt="Logo" style="height: 40px;">
                @else
                    {{ $currentTenant->name ?? 'FreshMarket' }}
                @endif
            </a>
            
            <div class="search-bar" style="position: relative;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <form action="{{ route('v1.all-products') }}" method="GET" id="t3-search-form" autocomplete="off" style="width: 100%;">
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
                <button class="action-btn mobile-search-toggle-btn" onclick="toggleMobileSearchT3()" title="Search" style="display: none;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                @if(auth()->check())
                <button class="action-btn" onclick="window.location.href='{{ route('account.index') }}'" title="My Account">
                    <i class="fa-solid fa-user"></i>
                </button>
                @else
                <button class="action-btn" onclick="openCustomerAuthModal()" title="Log In">
                    <i class="fa-regular fa-user"></i>
                </button>
                @endif
                <button class="action-btn" onclick="{{ auth()->check() ? 'window.location.href=\'' . route('v1.wishlist') . '\'' : 'openCustomerAuthModal()' }}">
                    <i class="fa-regular fa-heart"></i>
                </button>
                <button class="action-btn" onclick="toggleCartSidebar()">
                    <i class="fa-solid fa-bag-shopping"></i>
                        @php
                            $cartCount = \App\Services\CartService::getCount();
                        @endphp
                    <span class="cart-count" id="cart-count-badge">{{ $cartCount }}</span>
                </button>
            </div>
        </div>
        <!-- Mobile Expandable Search Bar -->
        <div id="t3-mobile-search-container" style="display: none; padding: 10px 15px; background: #fff; border-bottom: 1px solid var(--border-color); width: 100%; position: absolute; z-index: 999; top: 100%; left: 0;">
            <form action="{{ route('v1.all-products') }}" method="GET" id="t3-mobile-search-form" autocomplete="off" style="display: flex; align-items: center; width: 100%; position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 1rem; color: #9ca3af; top: 50%; transform: translateY(-50%);"></i>
                <input
                    type="text"
                    name="q"
                    id="t3-mobile-search-input"
                    placeholder="Search for groceries, vegetables, meat..."
                    value="{{ request('q') }}"
                    autocomplete="off"
                    style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.95rem; outline: none; color: var(--text-main); background: #f8fafc;"
                >
                <button type="button" onclick="toggleMobileSearchT3()" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); border: none; background: none; color: #9ca3af; font-size: 1.25rem; padding: 0.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </form>
            <div id="t3-mobile-search-dropdown" class="search-dropdown" style="display:none; position: absolute; top: calc(100% + 5px); left: 10px; right: 10px; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: 1px solid var(--border-color); z-index: 1000; padding: 0.5rem 0;">
                <div id="t3-mobile-search-history-section">
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #9ca3af; padding: 0.5rem 1rem;">
                        <i class="fa-solid fa-clock-rotate-left"></i> Recent Searches
                    </div>
                    <ul id="t3-mobile-history-list" style="list-style: none; margin: 0; padding: 0;"></ul>
                </div>
                <div id="t3-mobile-suggestions-section" style="display:none;">
                    <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #9ca3af; padding: 0.5rem 1rem;">
                        <i class="fa-solid fa-magnifying-glass"></i> Suggestions
                    </div>
                    <ul id="t3-mobile-suggestions-list" style="list-style: none; margin: 0; padding: 0;"></ul>
                </div>
            </div>
        </div>
    </header>
    @endmobileapp
    
    <!-- Top Navigation -->
    <nav style="background: #FFFFFF; border-bottom: 1px solid var(--border-color); padding: 0.75rem 0; overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch;">
        <div class="container" style="display: flex; gap: 2rem; align-items: center;">
            <a href="{{ route('v1.home') }}" style="color: var(--text-main); font-weight: 600; text-decoration: none; font-size: 0.95rem;">Home</a>
            <a href="{{ route('v1.all-products') }}" style="color: var(--text-main); font-weight: 600; text-decoration: none; font-size: 0.95rem;">All Products</a>
            <a href="{{ route('v1.combos') }}" style="color: var(--text-main); font-weight: 600; text-decoration: none; font-size: 0.95rem;">Deals</a>
            @php
                $collections = \App\Models\Collection::where('tenant_id', $currentTenant->id ?? 1)->where('status', 1)->get();
            @endphp
            @foreach($collections as $cat)
                <a href="{{ route('v1.collection', ['slug' => $cat->slug]) }}" style="color: var(--text-muted); font-weight: 500; text-decoration: none; font-size: 0.95rem;">{{ $cat->name }}</a>
            @endforeach
        </div>
    </nav>
 
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
 
    @mobileapp
        <!-- No footer for native app -->
    @else
    <!-- Footer -->
    <footer class="store-footer">
        <div class="container footer-grid">
            <div class="footer-col">
                <a href="{{ route('v1.home') }}" class="logo" style="margin-bottom: 1rem;">
                    @if(isset($currentTenant) && $currentTenant->logo)
                        <img src="{{ asset('storage/' . $currentTenant->logo) }}" alt="Logo" style="height: 40px;">
                    @else
                        {{ $currentTenant->name ?? 'FreshMarket' }}
                    @endif
                </a>
                <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                    Freshness, Quality, and Everything in between delivered right to your doorstep.
                </p>
            </div>
            
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('v1.home') }}">Home</a></li>
                    <li><a href="{{ route('v1.all-products') }}">All Products</a></li>
                    <li><a href="#">Categories</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h3>Support</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('v1.about') }}">About Us</a></li>
                    <li><a href="{{ route('v1.contact') }}">Contact Support</a></li>
                    <li><a href="{{ route('v1.shipping-policy') }}">Shipping Policy</a></li>
                    <li><a href="{{ route('v1.return-policy') }}">Return Policy</a></li>
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
    @endmobileapp

    <!-- Cart Sidebar Component (Assuming it exists or we build it) -->
    @include('template_1.partials.cart_drawer') <!-- Reusing template_1 cart for now to save time -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const t = document.createElement('div');
            t.textContent = "{{ session('error') }}";
            t.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#EF4444;color:#fff;padding:12px 24px;border-radius:8px;font-weight:600;z-index:99999;box-shadow:0 4px 12px rgba(0,0,0,0.15);transition:opacity 0.3s;';
            document.body.appendChild(t);
            setTimeout(() => { t.style.opacity='0'; setTimeout(()=>t.remove(), 300); }, 5000);
        });
    </script>
    @endif
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const t = document.createElement('div');
            t.textContent = "{{ session('success') }}";
            t.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#10B981;color:#fff;padding:12px 24px;border-radius:8px;font-weight:600;z-index:99999;box-shadow:0 4px 12px rgba(0,0,0,0.15);transition:opacity 0.3s;';
            document.body.appendChild(t);
            setTimeout(() => { t.style.opacity='0'; setTimeout(()=>t.remove(), 300); }, 5000);
        });
    </script>
    @endif
    
    <!-- Floating Cart Pill Component -->
    @include('template_3.partials.floating_cart')

    
    

    <script>
        function toggleCartSidebar() {
            $('#cart-drawer-n').addClass('open');
            $('#cart-drawer-overlay-n').fadeIn(300);
            $('body').css('overflow', 'hidden');
            if (!window.history.state || window.history.state.drawer !== 'cart') {
                window.history.pushState({drawer: 'cart'}, "Cart", window.location.href);
            }
            refreshNCart();
        }

        $(document).on('click', '#close-cart-n, #cart-drawer-overlay-n', function () {
            $('#cart-drawer-n').removeClass('open');
            $('#cart-drawer-overlay-n').fadeOut(300);
            $('body').css('overflow', '');
            if (window.history.state && window.history.state.drawer === 'cart') {
                window.history.back();
            }
        });

        window.addEventListener('popstate', function(event) {
            if ($('#cart-drawer-n').hasClass('open')) {
                $('#cart-drawer-n').removeClass('open');
                $('#cart-drawer-overlay-n').fadeOut(300);
                $('body').css('overflow', '');
            }
        });

        function refreshNCart() {
            $('#cart-drawer-body-n').html('<div class="cart-loader-n text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-success"></i></div>');
            $.get("{{ route('cart.fetch') }}", { theme: 'template_1' }, function (html) {
                $('#cart-drawer-body-n').html(html);
            });
        }

        window.updateNCartQty = function (key, delta) {
            const $item = $(`.n-cart-item:has(button[onclick*="${key}"])`);
            const $qtySpan = $item.find('.n-qty-wrap span');
            const $buttons = $item.find('.n-qty-wrap button');
            const currentQty = parseInt($qtySpan.text() || '1');
            const newQty = currentQty + delta;
            if (newQty < 1) return;

            // Optimistic UI: update quantity immediately
            $qtySpan.text(newQty);
            $buttons.prop('disabled', true).css('opacity', '0.4');

            $.post("{{ route('cart.update') }}", {
                _token: "{{ csrf_token() }}",
                id: key,
                quantity: newQty
            }, function (response) {
                if (response.success) {
                    $('#cart-count-badge').text(response.cartCount);
                    // Silently refresh cart content without spinner
                    $.get("{{ route('cart.fetch') }}", { theme: 'template_1' }, function (html) {
                        $('#cart-drawer-body-n').html(html);
                    });
                }
            }).fail(function(xhr) {
                // Revert on failure
                $qtySpan.text(currentQty);
                $buttons.prop('disabled', false).css('opacity', '1');
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                } else {
                    alert('Could not update quantity. Please try again.');
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
                url: "{{ route('v1.wishlist.toggle') }}",
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

            function highlight(text, query) {
                const idx = text.toLowerCase().indexOf(query.toLowerCase());
                if (idx < 0) return escHtml(text);
                return escHtml(text.slice(0, idx)) + '<strong style="color:var(--accent-color);">' + escHtml(text.slice(idx, idx + query.length)) + '</strong>' + escHtml(text.slice(idx + query.length));
            }

            function escHtml(str) {
                return str.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]);
            }

            function initSearchAutocomplete(inputId, dropdownId, historySectionId, historyListId, sugSectionId, sugListId, formId) {
                const input = document.getElementById(inputId);
                const dropdown = document.getElementById(dropdownId);
                const historySection = document.getElementById(historySectionId);
                const historyList = document.getElementById(historyListId);
                const sugSection = document.getElementById(sugSectionId);
                const sugList = document.getElementById(sugListId);
                const form = document.getElementById(formId);

                if (!input || !dropdown) return;

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
                        li.innerHTML = `<i class="fa-solid fa-magnifying-glass" style="color:#9ca3af;font-size:0.85rem;"></i><span>${highlight(name, query)}</span>`;
                        li.addEventListener('mouseover', () => li.style.background = '#f3f4f6');
                        li.addEventListener('mouseout', () => li.style.background = 'transparent');
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
            }

            // Init desktop autocomplete
            initSearchAutocomplete('t3-search-input', 't3-search-dropdown', 't3-search-history-section', 't3-history-list', 't3-suggestions-section', 't3-suggestions-list', 't3-search-form');

            // Init mobile autocomplete
            initSearchAutocomplete('t3-mobile-search-input', 't3-mobile-search-dropdown', 't3-mobile-search-history-section', 't3-mobile-history-list', 't3-mobile-suggestions-section', 't3-mobile-suggestions-list', 't3-mobile-search-form');
        })();

        function toggleMobileSearchT3() {
            const container = document.getElementById('t3-mobile-search-container');
            const input = document.getElementById('t3-mobile-search-input');
            if (container && input) {
                if (container.style.display === 'none') {
                    container.style.display = 'block';
                    input.focus();
                } else {
                    container.style.display = 'none';
                }
            }
        }
    </script>

    @mobileapp
        <!-- No whatsapp float button in native app -->
    @else
        @if(!empty($currentTenant->whatsapp_number))
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $currentTenant->whatsapp_number) }}" target="_blank"
                class="whatsapp-float-btn"
                style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; background-color: #25d366; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); z-index: 1000; text-decoration: none; transition: transform 0.3s ease;">
                <i class="fa-brands fa-whatsapp"></i>
            </a>
        @endif
    @endmobileapp

    @include('template_1.partials.mobile_bottom_nav')
    @include('partials.customer_auth_modal')
    @yield('scripts')
</body>
</html>
