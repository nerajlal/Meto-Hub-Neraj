<section class="sf-premium-hero sf-hero-{{ $storefrontTheme['layout'] }}">
    <div class="sf-premium-cover">
        @if($sliders->isNotEmpty())
            @include('storefront.themes.components.banner')
        @elseif($collections->first()?->image)
            <img src="{{ Storage::url($collections->first()->image) }}" alt="{{ $collections->first()->name }}">
        @else
            <div class="sf-premium-monogram" aria-hidden="true">{{ mb_substr($currentTenant->name, 0, 1) }}</div>
        @endif
    </div>
    <div class="sf-premium-intro">
        <span class="sf-kicker">{{ $currentTenant->name }} / THE SIGNATURE EDIT</span>
        <h1>{{ ['template_13'=>'The art of choosing well.', 'template_14'=>'Good finds. Better stories.', 'template_15'=>'A fresh take on the everyday.'][$storefrontTheme['id']] }}</h1>
        <p>Discover the latest collections and favourite finds from {{ $currentTenant->name }}.</p>
        <a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Discover the collection →</a>
    </div>
    @if($storefrontTheme['id'] === 'template_14')<div class="sf-journal-caption"><span>THE STORE EDIT</span><span>{{ $collections->count() }} departments to explore</span></div>@endif
</section>
