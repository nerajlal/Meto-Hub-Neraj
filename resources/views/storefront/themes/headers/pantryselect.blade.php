<header class="sf-header sf-header-new ps-header" data-theme-header="pantryselect">
    <div class="sf-header-row">@include('storefront.themes.components.brand')@include('storefront.themes.components.search')@include('storefront.themes.components.actions')</div>
    <div class="ps-welcome"><span>{{ $currentTenant->name }}</span><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.contact') }}">Contact the store <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
    <div class="sf-new-navigation ps-navigation">@include('storefront.themes.components.navigation')</div>
</header>
<script>
(() => {
    const row = document.querySelector('.ps-header>.sf-header-row');
    const update = () => document.body.style.setProperty('--new-header-row-height', Math.ceil(row.getBoundingClientRect().height) + 'px');
    update();
    if (typeof ResizeObserver !== 'undefined') new ResizeObserver(() => requestAnimationFrame(update)).observe(row);
    window.addEventListener('resize', update);
})();
</script>
