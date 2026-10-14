<header class="sf-header sf-header-pantry" data-theme-header="pantry">
    <div class="sf-pantry-topline"><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.about') }}">THE STORY BEHIND OUR STORE</a>@include('storefront.themes.components.actions')</div>
    <div class="sf-pantry-masthead">@include('storefront.themes.components.brand')</div>
    <div class="sf-pantry-navigation">@include('storefront.themes.components.navigation') @include('storefront.themes.components.search')</div>
</header>
<script>
(() => {
    const masthead = document.querySelector('.sf-pantry-masthead');
    const updateHeight = () => document.body.style.setProperty('--pantry-logo-height', Math.ceil(masthead.getBoundingClientRect().height) + 'px');
    updateHeight();
    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(() => requestAnimationFrame(updateHeight)).observe(masthead);
    }
    window.addEventListener('resize', updateHeight);
})();
</script>
