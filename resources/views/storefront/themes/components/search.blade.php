<form class="sf-search" action="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}" method="GET" role="search">
    @include('storefront.themes.preview_fields')
    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search {{ $currentTenant->name }}" aria-label="Search store products">
    <button type="submit" aria-label="Submit search"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
</form>
