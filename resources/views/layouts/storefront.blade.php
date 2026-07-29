<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VESPR Perfumes | Luxury Fragrances')</title>
    <meta name="description" content="@yield('meta_description', 'Discover VESPR Perfumes - The ultimate destination for luxury artisanal fragrances, oils, and exclusive perfume collections.')">
    <meta name="keywords" content="@yield('meta_keywords', 'perfumes, luxury fragrances, artisanal oils, VESPR, buy perfumes online')">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'VESPR Perfumes | Luxury Fragrances')">
    <meta property="og:description" content="@yield('meta_description', 'Discover VESPR Perfumes - The ultimate destination for luxury artisanal fragrances, oils, and exclusive perfume collections.')">
    <meta property="og:image" content="@yield('og_image', asset('Images/hero-banner.png'))">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'VESPR Perfumes | Luxury Fragrances')">
    <meta property="twitter:description" content="@yield('meta_description', 'Discover VESPR Perfumes - The ultimate destination for luxury artisanal fragrances, oils, and exclusive perfume collections.')">
    <meta property="twitter:image" content="@yield('og_image', asset('Images/hero-banner.png'))">
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .social-proof-tag {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            color: #000;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            z-index: 2;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
        }
        .product-card:hover .social-proof-tag, .v-combo-card:hover .social-proof-tag {
            transform: translateX(-50%) translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .social-proof-tag i { color: #f59e0b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="sidebar-overlay"></div>
    @mobileapp
    @else
        @include('template_1.partials.header')
    @endmobileapp

    <div class="main-wrapper">
        <aside class="sidebar">
            <button id="mobile-menu-close" class="mobile-close-btn">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <h2 class="sidebar-title">Categories</h2>
            <ul class="sidebar-menu">
                @php $sidebarCollections = \App\Models\Collection::where('status', 1)->get(); @endphp
                <li class="menu-item">
                    <a href="{{ route('v1.all-products') }}" class="menu-link {{ request()->routeIs('v1.all-products') ? 'active' : '' }}">
                        <i class="fa-solid fa-border-all"></i> All Products
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('v1.combos') }}" class="menu-link {{ request()->routeIs('v1.combos') || request()->routeIs('v1.combo') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group"></i> Exclusive Combos
                    </a>
                </li>
                @foreach($sidebarCollections as $col)
                @php
                    $icon = 'fa-droplet';
                    $slug = strtolower($col->slug);
                    if (str_contains($slug, 'signature')) $icon = 'fa-crown';
                    elseif (str_contains($slug, 'floral')) $icon = 'fa-leaf';
                    elseif (str_contains($slug, 'fresh') || str_contains($slug, 'zesty')) $icon = 'fa-wind';
                    elseif (str_contains($slug, 'wood')) $icon = 'fa-tree';
                    elseif (str_contains($slug, 'oriental') || str_contains($slug, 'oud')) $icon = 'fa-moon';
                    elseif (str_contains($slug, 'spice') || str_contains($slug, 'warm')) $icon = 'fa-fire';
                @endphp
                <li class="menu-item">
                    <a href="{{ route('v1.collection', ['slug' => $col->slug]) }}" class="menu-link {{ request()->query('slug') == $col->slug ? 'active' : '' }}">
                        <i class="fa-solid {{ $icon }}"></i> {{ $col->name }}
                    </a>
                </li>
                @endforeach
            </ul>

            <h2 class="sidebar-title" style="margin-top: 2rem;">Shop By Gender</h2>
            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="{{ route('v1.collection', ['gender' => 'for-him']) }}" class="menu-link">
                        <i class="fa-solid fa-mars"></i> For Him
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('v1.collection', ['gender' => 'for-her']) }}" class="menu-link">
                        <i class="fa-solid fa-venus"></i> For Her
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('v1.collection', ['gender' => 'unisex']) }}" class="menu-link">
                        <i class="fa-solid fa-venus-mars"></i> Unisex
                    </a>
                </li>
            </ul>

            <h2 class="sidebar-title" style="margin-top: 2rem;">Company</h2>
            <ul class="sidebar-menu">
                <li class="menu-item">
                    <a href="{{ route('v1.about') }}" class="menu-link">
                        <i class="fa-solid fa-circle-info"></i> Our Story
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('v1.contact') }}" class="menu-link">
                        <i class="fa-solid fa-paper-plane"></i> Contact
                    </a>
                </li>
            </ul>
        </aside>

        <main class="main-content">
            <div class="content-container">
                @yield('content')
            </div>
        </main>
    </div>

    @mobileapp
    @else
        @include('template_1.partials.footer')
    @endmobileapp
    @include('template_1.partials.cart_drawer')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const $sidebar = $('.sidebar');
            const $overlay = $('.sidebar-overlay');
            const $toggle = $('#mobile-menu-toggle');
            const $close = $('#mobile-menu-close');

            function toggleMobileMenu() {
                $sidebar.toggleClass('active');
                $overlay.toggleClass('active');
                $('body').toggleClass('menu-open');
            }

            $toggle.on('click', toggleMobileMenu);
            $close.on('click', toggleMobileMenu);
            $overlay.on('click', toggleMobileMenu);

            // Close menu when clicking a link (optional, but good for SPA feel)
            $('.menu-link').on('click', function() {
                if ($(window).width() <= 1024) {
                    toggleMobileMenu();
                }
            });
        });

        // Cart Drawer Logic
        function toggleNCart(open = true) {
            if(open) {
                $('#cart-drawer-n').addClass('open');
                $('#cart-drawer-overlay-n').fadeIn(300);
                $('body').css('overflow', 'hidden');
                refreshNCart();
            } else {
                $('#cart-drawer-n').removeClass('open');
                $('#cart-drawer-overlay-n').fadeOut(300);
                $('body').css('overflow', '');
            }
        }

        function refreshNCart() {
            $('#cart-drawer-body-n').html('<div class="cart-loader-n"><i class="fa-solid fa-spinner fa-spin"></i></div>');
            $.get("{{ route('cart.fetch') }}", { theme: 'template_1' }, function(html) {
                $('#cart-drawer-body-n').html(html);
            });
        }

        function updateNCartQty(key, delta) {
            $.post("{{ route('cart.update') }}", {
                _token: "{{ csrf_token() }}",
                id: key,
                quantity: 'increment', // I'll check controller if it supports increment, but I'll use actual math
            }, function(response) {
                // Actually controller needs exact qty, I'll fetch it from UI first
            });
            // Re-implementing with exact qty
        }

        // Improved JS for Nurah
        window.updateNCartQty = function(key, delta) {
            const $item = $(`.n-cart-item:has(button[onclick*="${key}"])`);
            const $qtySpan = $item.find('.n-qty-wrap span');
            const $buttons = $item.find('.n-qty-wrap button');
            const currentQty = parseInt($qtySpan.text());
            const newQty = currentQty + delta;
            if(newQty < 1) return;

            // Optimistic UI: update quantity immediately
            $qtySpan.text(newQty);
            $buttons.prop('disabled', true).css('opacity', '0.4');

            $.post("{{ route('cart.update') }}", {
                _token: "{{ csrf_token() }}",
                id: key,
                quantity: newQty
            }, function(response) {
                if(response.success) {
                    $('#cart-count').text(response.cartCount);
                    // Silently refresh cart content without spinner
                    $.get("{{ route('cart.fetch') }}", { theme: 'template_1' }, function(html) {
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
                    alert('Error updating cart');
                }
            });
        }

        window.removeNCartItem = function(key) {
            $.post("{{ route('cart.remove') }}", {
                _token: "{{ csrf_token() }}",
                id: key
            }, function(response) {
                if(response.success) {
                    $('#cart-count').text(response.cartCount);
                    refreshNCart();
                }
            });
        }

        $(document).on('click', '#close-cart-n, #cart-drawer-overlay-n', function() {
            const isOpen = $('#cart-drawer-n').hasClass('open');
            toggleNCart(!isOpen);
        });

        // Use a more specific unbind/bind or check to prevent double-firing
        $(document).off('click', '.cart-add-btn').on('click', '.cart-add-btn', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation(); 
            
            const productId = $(this).data('product-id');
            const defaultSize = $(this).data('default-size');
            const type = $(this).data('type') || 'product';
            const btn = $(this);
            
            if(btn.hasClass('processing')) return;
            btn.addClass('processing');
            
            $.ajax({
                url: "{{ route('cart.add') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: productId,
                    quantity: 1,
                    size: defaultSize,
                    type: type
                },
                success: function(response) {
                    btn.removeClass('processing');
                    if(response.success) {
                        $('#cart-count').text(response.cartCount);
                        btn.html('<i class="fa-solid fa-check"></i>');
                        btn.css('background', '#10B981');
                        
                        // Open Drawer
                        toggleNCart(true);

                        setTimeout(() => {
                            btn.html('<i class="fa-solid fa-plus"></i>');
                            btn.css('background', '');
                        }, 2000);
                    }
                },
                error: function() {
                    btn.removeClass('processing');
                }
            });
        });
    </script>
    @yield('scripts')
    
    <!-- Login Modal -->
    <div id="login-modal" class="modal-overlay" style="display: none;" onclick="closeModalOnOverlay(event, 'login-modal')">
        <div class="modal-content">
            <button class="modal-close" onclick="closeModal('login-modal')">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="modal-header">
                <h2 class="modal-title">Welcome Back</h2>
                <p class="modal-subtitle">Log in to your VESPR account to continue.</p>
            </div>
            
            <form action="{{ route('login') }}" method="POST" class="modal-form">
                @csrf
                <div class="form-group-modal" style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--primary-color);">Email Address</label>
                    <input type="email" name="email" required placeholder="name@example.com" style="width: 100%; padding: 0.85rem 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--accent-color)'" onblur="this.style.borderColor='var(--border-color)'">
                </div>
                <div class="form-group-modal" style="margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <label style="font-size: 0.85rem; font-weight: 700; color: var(--primary-color);">Password</label>
                        <a href="#" style="font-size: 0.75rem; color: var(--accent-color); text-decoration: none; font-weight: 600;">Forgot?</a>
                    </div>
                    <input type="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 0.85rem 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none; transition: 0.3s;" onfocus="this.style.borderColor='var(--accent-color)'" onblur="this.style.borderColor='var(--border-color)'">
                </div>
                <button type="submit" class="btn-primary" style="width: 100%; border: none; margin-top: 1rem; padding: 1rem; border-radius: 1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Log In</button>
            </form>

            <div style="margin: 2rem 0; display: flex; align-items: center; gap: 1rem; color: var(--text-muted); font-size: 0.8rem;">
                <div style="flex-grow: 1; height: 1px; background: var(--border-color);"></div>
                <span>OR</span>
                <div style="flex-grow: 1; height: 1px; background: var(--border-color);"></div>
            </div>

            <a href="{{ route('google.login') }}" style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.85rem; border-radius: 1rem; border: 1.5px solid var(--border-color); background: #fff; color: var(--primary-color); text-decoration: none; font-weight: 700; font-size: 0.9rem; transition: 0.3s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="18">
                Continue with Google
            </a>

            <div class="modal-footer-box" style="margin-top: 2rem; text-align: center; font-size: 0.9rem; color: var(--text-muted);">
                <span>New to VESPR? <a href="javascript:void(0)" onclick="switchModal('login-modal', 'register-modal')" style="color: var(--accent-color); font-weight: 700; text-decoration: none;">Create an account</a></span>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="register-modal" class="modal-overlay" style="display: none;" onclick="closeModalOnOverlay(event, 'register-modal')">
        <div class="modal-content" style="max-width: 500px;">
            <button class="modal-close" onclick="closeModal('register-modal')">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="modal-header">
                <h2 class="modal-title">Join VESPR</h2>
                <p class="modal-subtitle">Experience the finest luxury fragrances today.</p>
            </div>
            
            <form action="{{ route('register') }}" method="POST" class="modal-form">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                    <div class="form-group-modal">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--primary-color);">Full Name</label>
                        <input type="text" name="name" required placeholder="John Doe" style="width: 100%; padding: 0.85rem 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none;">
                    </div>
                    <div class="form-group-modal">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--primary-color);">Phone</label>
                        <input type="text" name="phone" placeholder="+91" style="width: 100%; padding: 0.85rem 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none;">
                    </div>
                </div>

                <div class="form-group-modal" style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--primary-color);">Email Address</label>
                    <input type="email" name="email" required placeholder="name@example.com" style="width: 100%; padding: 0.85rem 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="form-group-modal">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--primary-color);">Password</label>
                        <input type="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 0.85rem 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none;">
                    </div>
                    <div class="form-group-modal">
                        <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--primary-color);">Confirm</label>
                        <input type="password" name="password_confirmation" required placeholder="••••••••" style="width: 100%; padding: 0.85rem 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none;">
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; border: none; padding: 1rem; border-radius: 1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Create Account</button>
            </form>

            <div class="modal-footer-box" style="margin-top: 2rem; text-align: center; font-size: 0.9rem; color: var(--text-muted);">
                <span>Already have an account? <a href="javascript:void(0)" onclick="switchModal('register-modal', 'login-modal')" style="color: var(--accent-color); font-weight: 700; text-decoration: none;">Log In here</a></span>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
            document.body.style.overflow = '';
        }

        function closeModalOnOverlay(event, id) {
            if (event.target.id === id) {
                closeModal(id);
            }
        }

        function switchModal(from, to) {
            closeModal(from);
            setTimeout(() => openModal(to), 10);
        }

        // Handle errors and reopening modals
        @if(session('open_login') || $errors->has('email'))
            openModal('login-modal');
        @endif

        @if(session('open_register') || session('register_error'))
            openModal('register-modal');
        @endif

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
</body>
</html>
