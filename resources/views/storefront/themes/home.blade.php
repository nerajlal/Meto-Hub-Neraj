@extends('template_1.layouts.app')
@section('title', $currentTenant->name . ' | Your Online Store')
@section('content')
@php
    $hero = app(\App\Services\StorefrontTheme::class)->hero($currentTenant, $storefrontTheme['id']);
@endphp
<div class="sf-home sf-home-{{ $storefrontTheme['layout'] }}" data-theme-home="{{ $storefrontTheme['layout'] }}">
    @if(in_array($storefrontTheme['id'], ['template_13', 'template_14', 'template_15']))
        @include('storefront.themes.components.premium_hero')
        @include('storefront.themes.components.categories')
    @elseif(in_array($storefrontTheme['id'], ['template_7', 'template_8', 'template_9', 'template_10', 'template_11', 'template_12', 'template_13', 'template_14', 'template_15']))
        <section class="sf-new-hero sf-hero-{{ $storefrontTheme['layout'] }}">
            <div class="sf-new-hero-copy"><span class="sf-kicker">{{ $hero['eyebrow'] }}</span><h1>{{ $hero['heading'] }}</h1><p>{{ $hero['description'] }}</p><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">{{ $hero['button_label'] }} →</a></div>
            <div class="sf-new-hero-media">@if($sliders->isNotEmpty())@include('storefront.themes.components.banner')@elseif($collections->first()?->image)<img src="{{ Storage::url($collections->first()->image) }}" alt="{{ $collections->first()->name }}">@else<div class="sf-new-monogram" aria-hidden="true">{{ mb_substr($currentTenant->name, 0, 1) }}</div>@endif</div>
        </section>
        @include('storefront.themes.components.categories')
    @elseif($storefrontTheme['id'] === 'template_2')
        <section class="sf-market-intro">
            <div class="sf-market-copy"><span class="sf-kicker">{{ $hero['eyebrow'] }}</span><h1>{{ $hero['heading'] }}</h1><p>{{ $hero['description'] }}</p><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">{{ $hero['button_label'] }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a><div class="sf-market-stat"><strong>{{ $collections->count() }}</strong><span>departments to discover</span></div></div>
            <div class="sf-market-feature">@if($sliders->isNotEmpty())@include('storefront.themes.components.banner')@elseif($collections->first()?->image)<img src="{{ Storage::url($collections->first()->image) }}" alt="{{ $collections->first()->name }}">@else<div class="sf-store-monogram">{{ mb_substr($currentTenant->name, 0, 1) }}<span>{{ $currentTenant->name }}</span></div>@endif</div>
        </section>
        @include('storefront.themes.components.categories')
    @elseif($storefrontTheme['id'] === 'template_3')
        <div class="sf-quick-greeting"><div><span class="sf-kicker">{{ $hero['eyebrow'] }}</span><h1>{{ $hero['heading'] }}</h1>@if($hero['description'] !== '')<p>{{ $hero['description'] }}</p>@endif</div><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">{{ $hero['button_label'] }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
        @include('storefront.themes.components.categories')
        @include('storefront.themes.components.banner')
    @elseif($storefrontTheme['id'] === 'template_4')
        <div class="sf-pantry-intro"><span class="sf-kicker">{{ $hero['eyebrow'] }}</span><h1>{{ $hero['heading'] }}</h1><p>{{ $hero['description'] }}</p><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">{{ $hero['button_label'] }} →</a></div>
        @include('storefront.themes.components.banner')
        @include('storefront.themes.components.categories')
    @elseif($storefrontTheme['id'] === 'template_6')
        <section class="sf-pop-intro">
            <div class="sf-pop-copy"><span class="sf-kicker">{{ $hero['eyebrow'] }}</span><h1>@if($currentTenant->hero_heading){{ $hero['heading'] }}@else Everyday,<br><em>considered.</em>@endif</h1><p>{{ $hero['description'] }}</p><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">{{ $hero['button_label'] }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>
            <div class="sf-pop-feature">@if($sliders->isNotEmpty())@include('storefront.themes.components.banner')@else<div class="sf-pop-stamp"><span>THE STOREFRONT EDIT</span><strong>{{ $currentTenant->name }}</strong><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Discover the collection ↗</a></div>@endif</div>
        </section>
        @include('storefront.themes.components.categories')
    @else
        <div class="sf-corner-welcome"><div><span class="sf-kicker">{{ $hero['eyebrow'] }}</span><h1>{{ $hero['heading'] }}</h1><p>{{ $hero['description'] }}</p></div><a class="sf-primary-button" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">{{ $hero['button_label'] }} →</a></div>
        @include('storefront.themes.components.banner')
        @include('storefront.themes.components.categories')
    @endif
    @include('storefront.themes.components.shelves')
    @include('storefront.themes.components.reviews')
    @if($storefrontTheme['id'] === 'template_4' && $currentTenant->about_text)
        <section class="sf-pantry-story"><span class="sf-kicker">BEHIND THE STORE</span><h2>{{ $currentTenant->about_title ?: 'Our story' }}</h2><p>{{ \Illuminate\Support\Str::limit(strip_tags($currentTenant->about_text), 350) }}</p><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.about') }}">Get to know us →</a></section>
    @endif
</div>
@endsection
