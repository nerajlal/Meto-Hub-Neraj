@extends('template_1.layouts.app')
@section('title', $currentTenant->name . ' | Your Online Store')
@section('content')
<div class="sf-home sf-home-{{ $storefrontTheme['layout'] }}" data-theme-home="{{ $storefrontTheme['layout'] }}">
    @if(in_array($storefrontTheme['id'], ['template_7', 'template_8', 'template_9']))
        <section class="sf-new-hero sf-hero-{{ $storefrontTheme['layout'] }}">
            <div class="sf-new-hero-copy"><span class="sf-kicker">{{ $currentTenant->name }} / THE COLLECTION</span><h1>{{ ['template_7'=>'Find your everyday inspiration.', 'template_8'=>'Your next favourite is here.', 'template_9'=>'A collection worth discovering.'][$storefrontTheme['id']] }}</h1><p>Explore the latest collections from {{ $currentTenant->name }}.</p><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Explore the collection →</a></div>
            <div class="sf-new-hero-media">@if($sliders->isNotEmpty())@include('storefront.themes.components.banner')@elseif($collections->first()?->image)<img src="{{ Storage::url($collections->first()->image) }}" alt="{{ $collections->first()->name }}">@else<div class="sf-new-monogram" aria-hidden="true">{{ mb_substr($currentTenant->name, 0, 1) }}</div>@endif</div>
        </section>
        @include('storefront.themes.components.categories')
    @elseif($storefrontTheme['id'] === 'template_2')
        <section class="sf-market-intro">
            <div class="sf-market-copy"><span class="sf-kicker">WELCOME TO {{ mb_strtoupper($currentTenant->name) }}</span><h1>Fresh perspective.<br>Everyday favourites.</h1><p>Browse our departments and discover what’s on the shelves today.</p><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Explore the store <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a><div class="sf-market-stat"><strong>{{ $collections->count() }}</strong><span>departments to discover</span></div></div>
            <div class="sf-market-feature">@if($sliders->isNotEmpty())@include('storefront.themes.components.banner')@elseif($collections->first()?->image)<img src="{{ Storage::url($collections->first()->image) }}" alt="{{ $collections->first()->name }}">@else<div class="sf-store-monogram">{{ mb_substr($currentTenant->name, 0, 1) }}<span>{{ $currentTenant->name }}</span></div>@endif</div>
        </section>
        @include('storefront.themes.components.categories')
    @elseif($storefrontTheme['id'] === 'template_3')
        <div class="sf-quick-greeting"><div><span class="sf-kicker">YOUR DAILY EDIT</span><h1>Discover your next favourite.</h1></div><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Browse all <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
        @include('storefront.themes.components.categories')
        @include('storefront.themes.components.banner')
    @elseif($storefrontTheme['id'] === 'template_4')
        <div class="sf-pantry-intro"><span class="sf-kicker">THE {{ mb_strtoupper($currentTenant->name) }} EDIT</span><h1>A little everyday.<br>A little extraordinary.</h1><p>A thoughtful selection, ready to become part of your day.</p></div>
        @include('storefront.themes.components.banner')
        @include('storefront.themes.components.categories')
    @elseif($storefrontTheme['id'] === 'template_6')
        <section class="sf-pop-intro">
            <div class="sf-pop-copy"><span class="sf-kicker">THE {{ mb_strtoupper($currentTenant->name) }} COLLECTION</span><h1>Everyday,<br><em>considered.</em></h1><p>{{ $storefrontTheme['intro'] }}</p><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Explore the collection <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
            <div class="sf-pop-feature">@if($sliders->isNotEmpty())@include('storefront.themes.components.banner')@else<div class="sf-pop-stamp"><span>THE STOREFRONT EDIT</span><strong>{{ $currentTenant->name }}</strong><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Discover the collection ↗</a></div>@endif</div>
        </section>
        @include('storefront.themes.components.categories')
    @else
        <div class="sf-corner-welcome"><div><span class="sf-kicker">THE DEPARTMENT STORE</span><h1>Welcome to {{ $currentTenant->name }}.</h1><p>Explore the collections. Make yourself at home.</p></div><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Shop all products →</a></div>
        @include('storefront.themes.components.banner')
        @include('storefront.themes.components.categories')
    @endif
    @include('storefront.themes.components.shelves')
    @if($storefrontTheme['id'] === 'template_4' && $currentTenant->about_text)
        <section class="sf-pantry-story"><span class="sf-kicker">BEHIND THE STORE</span><h2>{{ $currentTenant->about_title ?: 'Our story' }}</h2><p>{{ \Illuminate\Support\Str::limit(strip_tags($currentTenant->about_text), 350) }}</p><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.about') }}">Get to know us →</a></section>
    @endif
</div>
@endsection
