<footer class="sf-footer sf-footer-{{ $storefrontTheme['layout'] }}">
    <div class="sf-footer-brand">@include('storefront.themes.components.brand')<p>{{ $currentTenant->contact_address ?: 'Your everyday favourites, all in one place.' }}</p></div>
    <div class="sf-footer-links">
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.about') }}">About our store</a>
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.contact') }}">Contact</a>
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.shipping-policy') }}">Delivery</a>
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.return-policy') }}">Returns</a>
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.terms-of-service') }}">Terms</a>
    </div>
    <div class="sf-footer-contact">
        @if($currentTenant->contact_phone)<a href="tel:{{ preg_replace('/[^+0-9]/', '', $currentTenant->contact_phone) }}">{{ $currentTenant->contact_phone }}</a>@endif
        @if($currentTenant->contact_email)<a href="mailto:{{ $currentTenant->contact_email }}">{{ $currentTenant->contact_email }}</a>@endif
        <span>© {{ now()->year }} {{ $currentTenant->name }}</span>
    </div>
</footer>
