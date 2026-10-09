<nav class="sf-navigation" aria-label="Shop navigation">
    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Shop all</a>
    @foreach(\App\Models\Collection::where('status', true)->orderBy('name')->get() as $department)
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.collection', ['slug' => $department->slug]) }}">{{ $department->name }}</a>
    @endforeach
    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.combos') }}">Bundles</a>
    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.about') }}">Our story</a>
</nav>
