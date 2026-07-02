<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($currentTenant->name ?? 'Fresh Grocery') . ' | Online Supermarket')</title>
    <meta name="description" content="@yield('meta_description', 'Shop fresh vegetables, fruits, dairy, bakery, and daily essentials online at ' . ($currentTenant->name ?? 'our store') . '. Fast home delivery guaranteed.')">
    <meta name="keywords" content="@yield('meta_keywords', 'online grocery, fresh vegetables, organic fruits, daily essentials, order food online')">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- CSS and Fonts -->
    <link rel="stylesheet" href="{{ asset('css/storefront.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.storeCurrencySymbol = "@currency";
    </script>
    
    <style>
        /* Grocery Colors and Accents overrides */
        :root {
            --primary-color: {{ $currentTenant->dark_color ?? '#0f172a' }};
            --accent-color: {{ $currentTenant->primary_color ?? '#10b981' }};
            --accent-hover: {{ $currentTenant->primary_color ?? '#10b981' }};
            --bg-light: {{ $currentTenant->accent_color ?? '#f8fafc' }};
            --border-color: #e2e8f0;
            --text-muted: #64748b;
            --header-height: 125px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #fff;
            color: var(--primary-color);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        /* Customize scrollbars */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Mobile specific hiding for tags */
        @media (max-width: 768px) {
            .hide-text-mobile {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    @include('template_2.partials.header')

    <main class="main-content" style="width: 100%; max-width: 1400px; margin: 0 auto; padding: 2rem 2rem; background: #fff; min-height: 70vh; box-sizing: border-box;">
        @yield('content')
    </main>

    @include('template_2.partials.footer')
    @include('template_1.partials.cart_drawer')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Cart Drawer Animations & Actions
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
            $('#cart-drawer-body-n').html('<div class="cart-loader-n text-center py-5"><i class="fa-solid fa-spinner fa-spin fa-2x text-success"></i></div>');
            $.get("{{ route('cart.fetch') }}", { theme: 'template_2' }, function(html) {
                $('#cart-drawer-body-n').html(html);
            });
        }

        window.updateNCartQty = function(key, delta) {
            const $item = $(`.n-cart-item:has(button[onclick*="${key}"])`);
            const currentQty = parseInt($item.find('.n-qty-wrap span').text() || '1');
            const newQty = currentQty + delta;
            if(newQty < 1) return;

            $.post("{{ route('cart.update') }}", {
                _token: "{{ csrf_token() }}",
                id: key,
                quantity: newQty
            }, function(response) {
                if(response.success) {
                    $('#cart-count').text(response.cartCount);
                    refreshNCart();
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
            toggleNCart(false);
        });

        // Add to Cart handler
        $(document).on('click', '.cart-add-btn', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            const defaultSize = $(this).data('default-size') || '';
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
                        btn.css('background-color', '#10B981');
                        
                        toggleNCart(true);

                        setTimeout(() => {
                            btn.html('<i class="fa-solid fa-plus"></i>');
                            btn.css('background-color', '');
                        }, 2000);
                    }
                },
                error: function() {
                    btn.removeClass('processing');
                }
            });
        });

        window.toggleWishlist = function(event, productId) {
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
                success: function(response) {
                    if (response.success) {
                        const btns = document.querySelectorAll(`button[onclick*="toggleWishlist"][onclick*="${productId}"]`);
                        btns.forEach(btn => {
                            const icon = btn.querySelector('i');
                            if (response.wishlisted) {
                                icon.className = 'fa-solid fa-heart';
                                btn.style.color = '#ef4444';
                            } else {
                                icon.className = 'fa-regular fa-heart';
                                btn.style.color = '#64748b';
                            }
                        });

                        const badge = document.getElementById('wishlist-count');
                        if (badge) {
                            badge.textContent = response.count;
                        } else if (response.count > 0) {
                            const wishlistLink = document.querySelector('a[href*="wishlist"]');
                            if (wishlistLink) {
                                const newBadge = document.createElement('span');
                                newBadge.id = 'wishlist-count';
                                newBadge.className = 'badge rounded-circle';
                                newBadge.style = 'font-size: 0.65rem; top: -5px; right: 5px; width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; border: 1.5px solid #fff; border-radius: 50%; background: #ef4444; position: absolute;';
                                newBadge.textContent = response.count;
                                wishlistLink.appendChild(newBadge);
                            }
                        }
                    }
                },
                error: function() {
                    alert('Error updating wishlist. Please try again.');
                }
            });
        };
    </script>
    @yield('scripts')
    @if(!empty($currentTenant->whatsapp_number))
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $currentTenant->whatsapp_number) }}" target="_blank" class="whatsapp-float-btn" style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; background-color: #25d366; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); z-index: 1000; text-decoration: none; transition: transform 0.3s ease;">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    @endif
</body>
</html>
