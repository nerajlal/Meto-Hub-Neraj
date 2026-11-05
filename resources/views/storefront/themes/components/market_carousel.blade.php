<div class="sf-market-carousel" data-market-carousel role="region" aria-roledescription="carousel" aria-label="Store highlights">
    @include('storefront.themes.components.banner')
    @if($sliders->count() > 1)
    <div class="sf-market-carousel-controls" hidden>
        @foreach($sliders as $slider)
        <button type="button" data-slide-dot="{{ $loop->index }}" aria-label="Show store banner {{ $loop->iteration }}" aria-current="{{ $loop->first ? 'true' : 'false' }}"><span></span></button>
        @endforeach
    </div>
    @endif
</div>
