<header class="sf-header sf-header-quick" data-theme-header="quick">
    <div class="sf-header-row"><div class="sf-quick-brand">@include('storefront.themes.components.brand')<span>The everyday collection</span></div>@include('storefront.themes.components.actions')</div>
    <div class="sf-quick-search">@include('storefront.themes.components.search')</div>
    @include('storefront.themes.components.navigation')
</header>
<script>
(() => {
    const header = document.querySelector('.sf-header-quick');
    const updateHeight = () => document.body.style.setProperty('--quick-header-height', Math.ceil(header.getBoundingClientRect().height) + 'px');
    updateHeight();
    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(() => requestAnimationFrame(updateHeight)).observe(header);
    }
    window.addEventListener('resize', updateHeight);
})();
</script>
