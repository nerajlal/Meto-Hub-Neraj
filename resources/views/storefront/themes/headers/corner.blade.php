<header class="sf-header sf-header-corner" data-theme-header="corner">
    <div class="sf-header-row">@include('storefront.themes.components.brand') @include('storefront.themes.components.search') @include('storefront.themes.components.actions')</div>
    <div class="sf-corner-status"><span><i class="fa-solid fa-store" aria-hidden="true"></i> THE COLLECTIONS, ALL IN ONE PLACE</span><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.contact') }}">Contact the store →</a></div>
</header>
<script>
(() => {
    const header = document.querySelector('.sf-header-corner');
    const topRow = header.querySelector('.sf-header-row');
    const updateHeight = () => {
        document.body.style.setProperty('--corner-header-height', Math.ceil(header.getBoundingClientRect().height) + 'px');
        document.body.style.setProperty('--corner-top-row-height', Math.ceil(topRow.getBoundingClientRect().height) + 'px');
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
