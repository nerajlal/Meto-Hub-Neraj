<div class="sf-actions">
    @if(auth()->check())
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('account.orders') }}" aria-label="My account"><i class="fa-regular fa-user" aria-hidden="true"></i><span>{{ explode(' ', auth()->user()->name)[0] }}</span></a>
    @else
        <button type="button" onclick="openCustomerAuthModal()" aria-label="Sign in"><i class="fa-regular fa-user" aria-hidden="true"></i><span>Sign in</span></button>
    @endif
    @if(auth()->check())
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.wishlist') }}" aria-label="Wishlist"><i class="fa-regular fa-heart" aria-hidden="true"></i></a>
    @else
        <button type="button" onclick="openCustomerAuthModal()" aria-label="Wishlist"><i class="fa-regular fa-heart" aria-hidden="true"></i></button>
    @endif
    <button type="button" class="sf-cart" onclick="toggleNCart(true)" aria-label="Open cart"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i><span id="cart-count">{{ \App\Services\CartService::getCount() }}</span></button>
</div>
