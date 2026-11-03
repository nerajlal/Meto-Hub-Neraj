<header class="sf-header sf-header-new sf-header-harvest" data-theme-header="harvest"><div class="sf-header-row">@include('storefront.themes.components.brand')@include('storefront.themes.components.search')@include('storefront.themes.components.actions')</div><div class="sf-new-navigation"><span class="rb-header-caption">SHOP DEPARTMENTS</span>@include('storefront.themes.components.navigation')</div></header>
<script>
(() => {
    const header = document.querySelector('.sf-header-new');
    const row = header.querySelector('.sf-header-row');
    const update = () => document.body.style.setProperty('--new-header-row-height', Math.ceil(row.getBoundingClientRect().height) + 'px');
    update();
    if (typeof ResizeObserver !== 'undefined') new ResizeObserver(() => requestAnimationFrame(update)).observe(row);
    window.addEventListener('resize', update);
})();
</script>
