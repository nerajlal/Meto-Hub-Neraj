<div class="sf-market-carousel" data-market-carousel role="region" aria-roledescription="carousel" aria-label="Store highlights">
    @include('storefront.themes.components.banner')
    @if($sliders->count() > 1)
    <div class="sf-market-carousel-controls" hidden>
        <button type="button" data-slide-prev aria-label="Previous store banner">←</button>
        <span data-slide-status aria-live="off">1 / {{ $sliders->count() }}</span>
        <button type="button" data-slide-next aria-label="Next store banner">→</button>
        <button type="button" data-slide-pause aria-label="Pause automatic slideshow">Pause</button>
    </div>
    @endif
</div>
