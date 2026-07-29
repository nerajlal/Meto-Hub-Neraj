@mobileapp
<style>
    .mobile-bottom-nav {
        display: none;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #fff;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        z-index: 1000;
        border-top: 1px solid var(--border-color);
        padding: 0.5rem 0.5rem;
        padding-bottom: calc(0.5rem + env(safe-area-inset-bottom));
    }
    
    .mobile-bottom-nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: space-around;
        align-items: center;
    }
    
    .mobile-bottom-nav .nav-item {
        flex: 1;
        text-align: center;
    }
    
    .mobile-bottom-nav .nav-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: var(--text-muted, #64748b);
        font-size: 0.65rem;
        font-weight: 600;
        gap: 0.25rem;
        transition: color 0.2s ease;
        position: relative;
    }
    
    .mobile-bottom-nav .nav-link i {
        font-size: 1.2rem;
    }
    
    .mobile-bottom-nav .nav-link.active {
        color: var(--primary-color, #0f172a);
    }
    
    .mobile-bottom-nav .nav-badge {
        position: absolute;
        top: -5px;
        right: 15%;
        background-color: #ef4444;
        color: white;
        font-size: 0.55rem;
        font-weight: 700;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #fff;
    }
    
    @media (max-width: 768px) {
        .mobile-bottom-nav {
            display: block;
        }
        /* Add padding to body so content isn't hidden behind the sticky footer */
        body {
            padding-bottom: 70px !important;
        }
        /* Also push up the whatsapp float button if it exists */
        .whatsapp-float-btn {
            bottom: 80px !important;
        }
    }
</style>

<nav class="mobile-bottom-nav">
    <ul>
        <!-- Home -->
        <li class="nav-item">
            <a href="{{ route('velvet.home') }}" class="nav-link {{ request()->routeIs('v3.home') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>
        </li>
        
        <!-- Search (formerly Categories) -->
        <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link" id="mobile-bottom-menu-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Search</span>
            </a>
        </li>
        
        <!-- Wishlist -->
        <li class="nav-item">
            @if(auth()->check())
                <a href="{{ route('v3.wishlist') }}" class="nav-link {{ request()->routeIs('v3.wishlist') ? 'active' : '' }}">
            @else
                <a href="javascript:void(0)" onclick="typeof openCustomerAuthModal === 'function' ? openCustomerAuthModal() : (typeof openModal === 'function' ? openModal('login-modal') : window.location.href='{{ route('login') }}')" class="nav-link">
            @endif
                <i class="fa-regular fa-heart"></i>
                <span>Wishlist</span>
                @auth
                    @php
                        $tenantId = $currentTenant->id ?? 2;
                        $bottomWishlistCount = \App\Models\Wishlist::where('tenant_id', $tenantId)->where('user_id', auth()->id())->count();
                    @endphp
                    @if($bottomWishlistCount > 0)
                        <span class="nav-badge" id="bottom-wishlist-count">{{ $bottomWishlistCount }}</span>
                    @endif
                @endauth
            </a>
        </li>
        
        <!-- Cart -->
        <li class="nav-item">
            <a href="javascript:void(0)" class="nav-link" onclick="toggleNCart(true)">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Cart</span>
                @php
                    $cartCount = \App\Services\CartService::getCount();
                @endphp
                @if($cartCount > 0)
                    <span class="nav-badge" id="bottom-cart-count">{{ $cartCount }}</span>
                @endif
            </a>
        </li>
        
        <!-- You / Profile -->
        <li class="nav-item">
            @if(auth()->check())
                <a href="{{ route('account.index') }}" class="nav-link {{ request()->routeIs('account.index') ? 'active' : '' }}">
            @else
                <a href="javascript:void(0)" onclick="typeof openCustomerAuthModal === 'function' ? openCustomerAuthModal() : (typeof openModal === 'function' ? openModal('login-modal') : window.location.href='{{ route('login') }}')" class="nav-link">
            @endif
                <i class="fa-regular fa-user"></i>
                <span>You</span>
            </a>
        </li>
    </ul>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bottomMenuBtn = document.getElementById('mobile-bottom-menu-btn');
        if(bottomMenuBtn) {
            bottomMenuBtn.addEventListener('click', function() {
                // Toggle mobile search containers
                let t1Container = document.getElementById('mobile-search-container');
                let t1Input = document.getElementById('t1-mobile-search-input');
                let t3Container = document.getElementById('t3-mobile-search-container');
                let t3Input = document.getElementById('t3-mobile-search-input');
                let t2Container = document.querySelector('.mobile-search-bar'); // if template_2 uses this
                
                let container = t1Container || t3Container || t2Container;
                let input = t1Input || t3Input || (t2Container ? t2Container.querySelector('input') : null);
                
                if (container) {
                    if (container.style.display === 'none' || container.style.display === '') {
                        container.style.display = 'block';
                        if (input) input.focus();
                    } else {
                        container.style.display = 'none';
                    }
                } else {
                    // Fallback to opening sidebar if no search container is found
                    let sidebar = document.querySelector('.sidebar');
                    let overlay = document.querySelector('.sidebar-overlay');
                    if (sidebar && overlay) {
                        sidebar.classList.add('active');
                        overlay.classList.add('active');
                    }
                }
            });
        }
        
        // Sync cart count with the bottom bar
        const topCartCount = document.getElementById('cart-count') || document.getElementById('cart-count-badge');
        if (topCartCount) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' || mutation.type === 'characterData') {
                        const newCount = topCartCount.textContent;
                        let bottomBadge = document.getElementById('bottom-cart-count');
                        
                        if (parseInt(newCount) > 0) {
                            if (!bottomBadge) {
                                const cartLink = document.querySelector('.mobile-bottom-nav .fa-cart-shopping').parentElement;
                                bottomBadge = document.createElement('span');
                                bottomBadge.className = 'nav-badge';
                                bottomBadge.id = 'bottom-cart-count';
                                cartLink.appendChild(bottomBadge);
                            }
                            bottomBadge.textContent = newCount;
                        } else if (bottomBadge) {
                            bottomBadge.remove();
                        }
                    }
                });
            });
            observer.observe(topCartCount, { childList: true, characterData: true, subtree: true });
        }
        // Sync wishlist count with the bottom bar
        const topWishlistCount = document.getElementById('wishlist-count');
        if (topWishlistCount) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' || mutation.type === 'characterData') {
                        const newCount = topWishlistCount.textContent;
                        let bottomBadge = document.getElementById('bottom-wishlist-count');
                        
                        if (parseInt(newCount) > 0) {
                            if (!bottomBadge) {
                                const wishlistLink = document.querySelector('.mobile-bottom-nav .fa-heart').parentElement;
                                bottomBadge = document.createElement('span');
                                bottomBadge.className = 'nav-badge';
                                bottomBadge.id = 'bottom-wishlist-count';
                                wishlistLink.appendChild(bottomBadge);
                            }
                            bottomBadge.textContent = newCount;
                        } else if (bottomBadge) {
                            bottomBadge.remove();
                        }
                    }
                });
            });
            observer.observe(topWishlistCount, { childList: true, characterData: true, subtree: true });
        }
    });
</script>
@endmobileapp
