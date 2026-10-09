<header class="sf-header sf-header-corner" data-theme-header="corner">
    <div class="sf-header-row">@include('storefront.themes.components.brand') @include('storefront.themes.components.search') @include('storefront.themes.components.actions')</div>
    <div class="sf-corner-status"><span><i class="fa-solid fa-store" aria-hidden="true"></i> THE COLLECTIONS, ALL IN ONE PLACE</span><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.contact') }}">Contact the store →</a></div>
</header>
