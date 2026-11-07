<div class="ps-product-layout" data-theme-product-composition="pantryselect">
    @include('storefront.themes.product.gallery')
    <div class="ps-product-order">@include('storefront.themes.product.identity')@include('storefront.themes.product.purchase')<div class="ps-product-help"><i class="fa-regular fa-circle-question" aria-hidden="true"></i><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.contact') }}">Have a question? Contact the store</a></div></div>
</div>
<div class="ps-product-information">@include('storefront.themes.product.information')</div>
