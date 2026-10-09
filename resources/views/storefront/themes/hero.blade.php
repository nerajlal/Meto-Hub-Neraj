<section class="theme-welcome theme-welcome-{{ $storefrontTheme['layout'] }}" aria-label="Welcome to {{ $currentTenant->name }}">
    <div class="theme-welcome-copy">
        <span class="theme-eyebrow">{{ $storefrontTheme['eyebrow'] }}</span>
        <h1>{{ $storefrontTheme['headline'] }}</h1>
        <p>{{ $storefrontTheme['intro'] }}</p>
        <div class="theme-welcome-actions">
            <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}" class="theme-shop-button">Shop the shelves <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combos') }}" class="theme-secondary-button">Explore bundles</a>
        </div>
    </div>
    <div class="theme-welcome-art" aria-hidden="true">
        <img src="{{ asset('images/themes/grocery-basket.svg') }}" alt="" width="420" height="320">
        <span class="theme-art-label">{{ $currentTenant->name }}</span>
    </div>
</section>
@if($storefrontTheme['id'] === 'template_2')
    <div class="theme-market-strip"><span><i class="fa-solid fa-basket-shopping" aria-hidden="true"></i> Browse your favourites</span><span><i class="fa-solid fa-layer-group" aria-hidden="true"></i> Discover our departments</span><span><i class="fa-solid fa-box-open" aria-hidden="true"></i> Build your everyday basket</span></div>
@endif
