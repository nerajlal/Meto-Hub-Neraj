<header class="sf-header sf-header-market" data-theme-header="market">
    <div class="sf-market-topline"><span>WELCOME TO {{ mb_strtoupper($currentTenant->name) }}</span><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.contact') }}">{{ $currentTenant->contact_phone ?: 'Get in touch' }} <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a></div>
    <div class="sf-header-row">@include('storefront.themes.components.brand') @include('storefront.themes.components.search') @include('storefront.themes.components.actions')</div>
    @include('storefront.themes.components.navigation')
</header>
<script>
(() => {
    const header = document.querySelector('.sf-header-market');
    const topRow = header.querySelector('.sf-header-row');
    const updateHeight = () => {
        document.body.style.setProperty('--market-header-height', Math.ceil(header.getBoundingClientRect().height) + 'px');
        document.body.style.setProperty('--market-top-row-height', Math.ceil(topRow.getBoundingClientRect().height) + 'px');
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
