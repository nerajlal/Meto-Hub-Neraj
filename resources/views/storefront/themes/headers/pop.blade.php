<header class="sf-header sf-header-pop" data-theme-header="pop">
    <div class="sf-pop-top"><span>{{ $currentTenant->name }} — THE STOREFRONT EDIT</span><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.contact') }}">{{ $currentTenant->contact_phone ?: 'Contact the store' }} ↗</a></div>
    <div class="sf-header-row">@include('storefront.themes.components.brand') @include('storefront.themes.components.actions')</div>
    <div class="sf-pop-navigation">@include('storefront.themes.components.navigation') @include('storefront.themes.components.search')</div>
</header>
