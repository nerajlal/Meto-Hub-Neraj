<nav class="sf-navigation" aria-label="Shop navigation">
    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Shop all</a>
    @php($navDepartments = \App\Models\Collection::where('status', true)->orderBy('name')->get())
    @foreach($navDepartments as $department)
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.collection', ['slug' => $department->slug]) }}">{{ $department->name }}</a>
    @endforeach
    @unless($navDepartments->contains(fn ($item) => $item->isOfferCollection()))<a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combos') }}">Bundles</a>@endunless
    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.about') }}">Our story</a>
</nav>
