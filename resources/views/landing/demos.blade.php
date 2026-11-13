@extends('layouts.landing')
@section('content')
<link rel="stylesheet" href="{{ asset('css/metohub-demos.css').'?v='.filemtime(public_path('css/metohub-demos.css')) }}">
<section class="demo-directory" aria-labelledby="demo-directory-title">
    <header class="demo-directory-intro"><span class="demo-directory-eyebrow">EXPLORE METOHUB</span><h1 id="demo-directory-title">Small brands.<br><em>Big possibilities.</em></h1><p>Take a look inside our sample stores. Browse products and collections, explore the layouts, and imagine your own brand here.</p><div class="demo-directory-links"><a href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}">Create your store ↗</a><a href="{{ route('landing.themes') }}">Explore all {{ count(app(\App\Services\ThemeAvailability::class)->available()) }} themes →</a></div></header>
    <p class="demo-directory-note">These are demonstration stores with sample products and illustrative imagery. Each store opens in a new tab. Prices and listings are examples, not offers from real merchants.</p>
    @php
        $images = ['mens-clothing' => 'demo-mens.jpg', 'grocery' => 'demo-grocery.jpg', 'gadgets' => 'demo-gadgets.jpg', 'jewelry' => 'demo-jewelry.jpg', 'watches' => 'demo-watches.jpg', 'womens-clothing' => 'demo-womens.jpg', 'perfume' => 'demo-perfume.jpg'];
    @endphp
    <div class="demo-directory-grid">
    @forelse($demos as $slug => $demo)
        <article class="demo-directory-card" data-demo-store="{{ $slug }}">
            <a class="demo-directory-image" href="{{ $demo['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" aria-label="Explore {{ $demo['tenant']->name }} demo store in a new tab"><img src="{{ asset('images/landing/reference/'.$images[$slug]) }}" alt="{{ $demo['category'] }} store illustration" width="800" height="600" loading="lazy"></a>
            <div class="demo-directory-card-body"><span class="demo-directory-category">{{ $demo['category'] }}</span><h2>{{ $demo['tenant']->name }}</h2><span class="demo-directory-theme">{{ config('storefront_themes.'.app(\App\Services\StorefrontTheme::class)->normalize($demo['tenant']->theme).'.name') }}</span><p>{{ $demo['workflow'] }}</p><a class="demo-directory-visit" href="{{ $demo['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer">Explore store <span aria-hidden="true">↗</span></a></div>
        </article>
    @empty
        <div class="demo-directory-empty"><h2>Our demo stores are getting ready.</h2><p>You can still explore the available designs on our Themes page.</p><a href="{{ route('landing.themes') }}">Browse themes →</a></div>
    @endforelse
    </div>
    <footer class="demo-directory-cta"><h2>Your brand could be next.</h2><p>Start with a {{ config('saas.trial_days') }}-day free trial. Personalize your theme and add your own products from the dashboard.</p><a href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}">Start your free trial ↗</a></footer>
</section>
@endsection
