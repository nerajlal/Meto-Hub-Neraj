<header class="sf-header sf-header-quick" data-theme-header="quick">
    <div class="sf-header-row"><div class="sf-quick-brand">@include('storefront.themes.components.brand')<span>The everyday collection</span></div>@include('storefront.themes.components.actions')</div>
    <div class="sf-quick-search">@include('storefront.themes.components.search')</div>
    @include('storefront.themes.components.navigation')
</header>
<script>
(() => {
    const header = document.querySelector('.sf-header-quick');
    const topRow = header.querySelector('.sf-header-row');
    const updateHeight = () => {
        document.body.style.setProperty('--quick-header-height', Math.ceil(header.getBoundingClientRect().height) + 'px');
        document.body.style.setProperty('--quick-top-row-height', Math.ceil(topRow.getBoundingClientRect().height) + 'px');
    };
    updateHeight();
    if (typeof ResizeObserver !== 'undefined') {
        const observer = new ResizeObserver(() => requestAnimationFrame(updateHeight));
        observer.observe(header);
        observer.observe(topRow);
    }
    window.addEventListener('resize', updateHeight);
})();
</script>
