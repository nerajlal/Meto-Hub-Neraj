@if($collections->isNotEmpty())
<section class="sf-category-section"><div class="sf-section-heading"><span>EXPLORE THE STORE</span><h2>{{ $storefrontTheme['id'] === 'template_4' ? 'A collection for every day.' : 'Find your department' }}</h2></div>
    <div class="sf-category-grid">
        @foreach($collections as $collection)
            <a class="sf-category" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.collection', ['slug' => $collection->slug]) }}">
                @if($collection->image)<img src="{{ Storage::url($collection->image) }}" alt="" loading="lazy">@else<span class="sf-category-monogram">{{ mb_substr($collection->name, 0, 1) }}</span>@endif
                <span>{{ $collection->name }}<small>{{ $collection->products->count() }} items to explore</small></span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            </a>
        @endforeach
    </div>
</section>
@endif
