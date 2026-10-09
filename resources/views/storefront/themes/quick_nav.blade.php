<nav class="sf-quick-bottom" aria-label="Mobile shopping navigation">
    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i>Home</a>
    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}"><i class="fa-solid fa-border-all" aria-hidden="true"></i>Browse</a>
    <button type="button" onclick="toggleNCart(true)"><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i>Basket</button>
    @if(auth()->check())<a href="{{ app(\App\Services\StorefrontTheme::class)->url('account.orders') }}"><i class="fa-regular fa-user" aria-hidden="true"></i>Account</a>@else<button type="button" onclick="openCustomerAuthModal()"><i class="fa-regular fa-user" aria-hidden="true"></i>Sign in</button>@endif
</nav>
