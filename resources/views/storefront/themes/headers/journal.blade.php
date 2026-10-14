<header class="sf-header sf-header-new sf-header-journal" data-theme-header="journal">
    <div class="sf-header-row">@include('storefront.themes.components.brand') @include('storefront.themes.components.actions')</div>
    <div class="sf-journal-motto"><span>THE {{ mb_strtoupper($currentTenant->name) }} JOURNAL</span><span>COLLECTIONS / STORIES / EVERYDAY FINDS</span></div>
    <div class="sf-new-navigation">@include('storefront.themes.components.navigation') @include('storefront.themes.components.search')</div>
</header>
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
