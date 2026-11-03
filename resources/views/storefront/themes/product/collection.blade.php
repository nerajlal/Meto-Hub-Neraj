<div class="pd-collection-layout pd-collection-{{ $storefrontTheme['layout'] }}">
    <div class="pd-collection-identity">@include('storefront.themes.product.identity')</div>
    @include('storefront.themes.product.gallery')
    <div class="pd-collection-purchase">@include('storefront.themes.product.purchase')</div>
    <div class="pd-collection-story">@include('storefront.themes.product.information')</div>
</div>
