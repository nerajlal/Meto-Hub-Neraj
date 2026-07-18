<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Velvet | High-End Fragrance')</title>
    <link rel="stylesheet" href="{{ asset('css/velvet.css') }}?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .social-proof-tag {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(4px);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 9px;
            font-weight: 700;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            z-index: 5;
            border: 1px solid rgba(0, 0, 0, 0.03);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        .v-card:hover .social-proof-tag, .v-combo-card:hover .social-proof-tag {
            transform: translateX(-50%) translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            background: #fff;
        }
        .social-proof-tag i { color: #c5a059; font-size: 10px; }
    </style>
</head>
<body>
    <header class="velvet-header">
        <div class="header-container-v">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button class="menu-toggle-v" id="menu-toggle-v"><i class="fa-solid fa-bars"></i></button>
                <a href="{{ route('velvet.home') }}" class="logo-v">{{ strtoupper($currentTenant->name ?? 'VELVET') }}</a>
            </div>
            
            <div class="search-bar-v">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search the collection...">
            </div>

            <div class="header-actions-v">
                <button class="action-btn-v"><i class="fa-solid fa-user"></i></button>
                <button class="action-btn-v" id="cart-trigger-v" style="position: relative;">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="cart-count-v">0</span>
                </button>
            </div>
        </div>
    </header>

    <div class="velvet-layout">
        <!-- Sidebar -->
        <aside class="velvet-sidebar">
            <div class="sidebar-section-v">
                <h3 class="sidebar-title-v">Luxury Catalog</h3>
                <ul class="sidebar-links-v">
                    <li><a href="{{ route('velvet.all-products') }}" class="{{ request()->routeIs('velvet.all-products') ? 'active' : '' }}"><i class="fa-solid fa-gem"></i> All Products</a></li>
                    <li><a href="{{ route('velvet.combos') }}" class="{{ request()->routeIs('velvet.combos') || request()->routeIs('velvet.combo') ? 'active' : '' }}"><i class="fa-solid fa-layer-group"></i> Exclusive Combos</a></li>
                </ul>
            </div>

            <div class="sidebar-section-v">
                <h3 class="sidebar-title-v">Our Collections</h3>
                <ul class="sidebar-links-v">
                    @foreach($collections ?? [] as $col)
                        <li>
                            <a href="{{ route('velvet.collection', ['slug' => $col->slug]) }}" 
                               class="{{ (isset($collection) && $collection->id == $col->id) ? 'active' : '' }}">
                                {{ $col->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="sidebar-footer-v">
                <a href="#"><i class="fa-solid fa-circle-info"></i> Our Legacy</a>
                <a href="#"><i class="fa-solid fa-headset"></i> Concierge</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="velvet-main-content">
            @yield('content')
            
            <footer class="velvet-mini-footer">
                <p>&copy; {{ date('Y') }} {{ strtoupper($currentTenant->name ?? 'VELVET') }}. LUXURY DEFINED.</p>
            </footer>
        </main>
    </div>

    @include('velvet.partials.cart_drawer')
    <div class="menu-drawer-overlay" id="menu-drawer-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); backdrop-filter: blur(2px); z-index: 1990;"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleDrawer(open = true) {
            if(open) {
                $('#cart-drawer-v').addClass('open');
                $('#cart-drawer-overlay').fadeIn(300);
                $('body').css('overflow', 'hidden');
                refreshDrawer();
            } else {
                $('#cart-drawer-v').removeClass('open');
                $('#cart-drawer-overlay').fadeOut(300);
                $('body').css('overflow', '');
            }
        }

        function refreshDrawer() {
            $('#cart-drawer-body').html('<div class="cart-loader-v"><i class="fa-solid fa-spinner fa-spin"></i></div>');
            $.get("{{ route('cart.fetch') }}", function(html) {
                $('#cart-drawer-body').html(html);
            });
        }

        function updateDrawerQty(key, delta) {
            const currentQty = parseInt($(`.cart-item-v[data-key="${key}"] .qty-val-v`).text());
            const newQty = currentQty + delta;
            if(newQty < 1) return;

            $.post("{{ route('cart.update') }}", {
                _token: "{{ csrf_token() }}",
                id: key,
                quantity: newQty
            }, function(response) {
                if(response.success) {
                    $('.cart-count-v').text(response.cartCount);
                    refreshDrawer();
                }
            }).fail(function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    alert(xhr.responseJSON.message);
                } else {
                    alert('Error updating cart');
                }
            });
        }

        function removeDrawerItem(key) {
            $.post("{{ route('cart.remove') }}", {
                _token: "{{ csrf_token() }}",
                id: key
            }, function(response) {
                if(response.success) {
                    $('.cart-count-v').text(response.cartCount);
                    refreshDrawer();
                }
            });
        }

        $(document).ready(function() {
            $('#cart-trigger-v, #cart-drawer-overlay, #close-cart-v').on('click', function() {
                const isOpen = $('#cart-drawer-v').hasClass('open');
                toggleDrawer(!isOpen);
            });

            // Mobile Menu Toggle
            $('#menu-toggle-v, #menu-drawer-overlay').on('click', function() {
                const sidebar = $('.velvet-sidebar');
                const overlay = $('#menu-drawer-overlay');
                const isOpen = sidebar.hasClass('open');
                
                if (isOpen) {
                    sidebar.removeClass('open');
                    overlay.fadeOut(300);
                    $('#menu-toggle-v i').removeClass('fa-xmark').addClass('fa-bars');
                } else {
                    sidebar.addClass('open');
                    overlay.fadeIn(300);
                    $('#menu-toggle-v i').removeClass('fa-bars').addClass('fa-xmark');
                }
            });

            // Close menu if a link is clicked
            $('.sidebar-links-v a').on('click', function() {
                if ($(window).width() <= 1024) {
                    $('.velvet-sidebar').removeClass('open');
                    $('#menu-drawer-overlay').fadeOut(300);
                    $('#menu-toggle-v i').removeClass('fa-xmark').addClass('fa-bars');
                }
            });
        });

        function quickAdd(productId, size, variantId) {
            const btn = event.currentTarget;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            btn.disabled = true;

            $.ajax({
                url: "{{ route('cart.add') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: productId,
                    quantity: 1,
                    size: size,
                    variant_id: variantId
                },
                success: function(response) {
                    if(response.success) {
                        $('.cart-count-v').text(response.cartCount);
                        btn.innerHTML = '<i class="fa-solid fa-check"></i>';
                        btn.style.background = '#10B981';
                        btn.style.color = '#fff';
                        
                        // Open Drawer
                        toggleDrawer(true);

                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                            btn.style.background = '';
                            btn.style.color = '';
                            btn.disabled = false;
                        }, 2000);
                    } else {
                        alert(response.message || 'Error adding item');
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert('Error adding item');
                    }
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            });
        }

        // Handle order success for guests
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('order_success')) {
                alert('Thank you! Your order has been placed successfully.');
                // Clean up the URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
