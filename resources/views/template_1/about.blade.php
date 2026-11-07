@extends('template_1.layouts.app')

@section('title', 'About ' . $currentTenant->name)

@section('styles')
<link rel="stylesheet" href="{{ asset('css/store-about.css').'?v='.filemtime(public_path('css/store-about.css')) }}">
@endsection

@section('content')
@php
    $aboutHeading = trim((string) $currentTenant->about_title);
    if ($aboutHeading === '' || ($aboutHeading === 'Cultivating Health & Happiness' && blank($currentTenant->about_text))) {
        $aboutHeading = 'Welcome to '.$currentTenant->name;
    }
@endphp
<div class="store-about">
    <nav class="store-about-breadcrumb" aria-label="Breadcrumb"><a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.home') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">Our story</span></nav>
    <section class="store-about-hero" aria-labelledby="store-about-title">
        <div class="store-about-intro">
        <p class="store-about-eyebrow">About {{ $currentTenant->name }}</p>
        <h1 id="store-about-title">{{ $aboutHeading }}</h1>
        <div class="store-about-rule" aria-hidden="true"></div>
        <div class="store-about-copy">
            @if(filled($currentTenant->about_text))
                {!! nl2br(e($currentTenant->about_text)) !!}
            @else
                <p>Welcome to {{ $currentTenant->name }}. Browse our products online, or contact us if you have a question about an item or your order.</p>
            @endif
        </div>
        <div class="store-about-links">
            <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Explore products</a>
            <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.contact') }}">Contact the store</a>
        </div>
        </div>
        <div class="store-about-brand" aria-label="{{ $currentTenant->name }} brand">
            <div class="store-about-brand-art">
                @if($currentTenant->about_image)
                    <img src="{{ app(\App\Services\StorefrontProduct::class)->imageUrl($currentTenant->about_image) }}" alt="{{ $currentTenant->name }} store story" width="480" height="360" class="store-about-story-image">
                @elseif($currentTenant->logo)
                    <img src="{{ Storage::url($currentTenant->logo) }}" alt="{{ $currentTenant->name }}" width="240" height="240">
                @else
                    <span class="store-about-monogram" aria-hidden="true">{{ mb_substr($currentTenant->name, 0, 1) }}</span>
                @endif
            </div>
            <div class="store-about-brand-caption"><span>{{ $currentTenant->name }}</span><span>Our story <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></span></div>
        </div>
    </section>
    <div class="store-about-bottom">
    @if(filled($currentTenant->contact_address) || filled($currentTenant->contact_phone) || filled($currentTenant->contact_email))
        <section class="store-about-contact" aria-labelledby="store-about-contact-title">
            <p class="store-about-eyebrow">A direct connection</p>
            <h2 id="store-about-contact-title">Let’s talk.</h2>
            @if(filled($currentTenant->contact_address))<address>{{ $currentTenant->contact_address }}</address>@endif
            @if(filled($currentTenant->contact_phone))<a href="tel:{{ preg_replace('/[^+0-9]/', '', $currentTenant->contact_phone) }}">{{ $currentTenant->contact_phone }}</a>@endif
            @if(filled($currentTenant->contact_email))<a href="mailto:{{ $currentTenant->contact_email }}">{{ $currentTenant->contact_email }}</a>@endif
        </section>
    @endif
    <section class="store-about-help" aria-labelledby="store-about-help-title">
        <p class="store-about-eyebrow">Before you order</p>
        <h2 id="store-about-help-title">The details that matter.</h2>
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.shipping-policy') }}"><span>Shipping & delivery</span><span aria-hidden="true">↗</span></a>
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.return-policy') }}"><span>Returns policy</span><span aria-hidden="true">↗</span></a>
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.contact') }}"><span>Ask us a question</span><span aria-hidden="true">↗</span></a>
    </section>
    </div>
</div>
@endsection
