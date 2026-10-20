<!doctype html>
<html lang="en" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('landing.partials.seo')
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/metohub-reference.css') }}?v={{ filemtime(public_path('css/metohub-reference.css')) }}">
<link rel="stylesheet" href="{{ asset('css/metohub-home.css') }}?v={{ filemtime(public_path('css/metohub-home.css')) }}">
</head>
<body>
<div class="min-h-screen bg-background">
@include('landing.partials.header')
<main id="main-content" tabindex="-1">
<section class="home-hero" id="hero" aria-labelledby="hero-title">
<div class="container-page hero-layout">
<div class="hero-copy animate-rise">
<p class="eyebrow hero-eyebrow"><span aria-hidden="true"></span> For creators with something to sell</p>
<h1 id="hero-title">Out of the DMs.<br>Into a real<br><span>storefront.</span></h1>
<p class="hero-description">Stop juggling screenshots and endless “Price?” chats. Give your brand one beautiful link where customers can browse, choose their size, and place an order. You get the details in one dashboard.</p>
<div class="hero-offer"><span class="hero-price">₹{{ number_format(config('saas.plans.base.monthly_price')) }}<small>/month</small></span><span class="hero-offer-copy">Plans for your next chapter.<br>Start with a free trial.</span></div>
<div class="hero-actions">
<a class="btn-primary" href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}" data-start="" data-plan="base">Start my store today <span aria-hidden="true">↗</span></a>
<a href="#demos" class="btn-ghost">Explore demo stores <span aria-hidden="true">→</span></a>
</div>

<p class="hero-reassurance"><span><span aria-hidden="true">✓</span> {{ config('saas.trial_days') }}-day free trial</span><span><span aria-hidden="true">✓</span> No coding needed</span></p>
<p class="hero-payment-note">Simple setup · Cash on delivery checkout</p>
@if($demos->isNotEmpty())
<a class="hero-demo-proof" href="#demos">
<span class="hero-demo-thumbnails" aria-hidden="true">
@foreach($demos->take(3) as $slug => $demo)
<img src="{{ asset('images/landing/reference/demo-'.(['mens-clothing' => 'mens', 'womens-clothing' => 'womens'][$slug] ?? $slug).'.jpg') }}" alt="" width="38" height="44">
@endforeach
</span>
<span><strong>{{ $demos->count() }} demo {{ $demos->count() === 1 ? 'store' : 'stores' }}. Find your look.</strong><small>Browse the products. Picture your brand here.</small></span>
<span class="hero-proof-arrow" aria-hidden="true">↗</span>
</a>
@endif
</div>
<figure class="hero-showcase animate-rise">
<div class="hero-phone-stage">
<div class="hero-phone" aria-label="Illustrative mobile storefront">
<div class="hero-phone-screen">
<div class="hero-phone-status" aria-hidden="true"><span>9:41</span><span class="hero-phone-island"></span><span>▮▮▮ <svg width="15" height="11" viewBox="0 0 18 12" fill="none"><rect x="1" y="1" width="13" height="10" rx="2" stroke="currentColor"/><path d="M16 4v4" stroke="currentColor" stroke-width="2"/><rect x="3" y="3" width="9" height="6" rx="1" fill="currentColor"/></svg></span></div>
<div class="hero-phone-address"><svg aria-hidden="true" width="10" height="12" viewBox="0 0 12 14" fill="none" stroke="currentColor"><rect x="1" y="6" width="10" height="7" rx="2"/><path d="M3 6V4a3 3 0 0 1 6 0v2"/></svg><span>{{ request()->getHttpHost() }}/store/your-brand</span></div>
<div class="hero-store-announcement">THOUGHTFULLY MADE. BEAUTIFULLY YOURS.</div>
<div class="hero-store-heading"><span class="hero-store-monogram" aria-hidden="true">L.</span><span>Lumière <small>THE EVERYDAY, ELEVATED</small></span><span class="hero-store-bag" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 7h14l1 14H4L5 7Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg></span></div>
<div class="hero-store-links" aria-hidden="true"><span>Discover</span><span>Fragrance</span><span>Essentials</span><span>Our story</span></div>
<div class="hero-product-photo"><img src="{{ asset('images/landing/reference/hero.jpg') }}" alt="Perfume and linen in an illustrative mobile Lumière store" width="1024" height="1280" fetchpriority="high"><span class="hero-product-label">THE ART OF EVERYDAY LIVING<br><em>Little luxuries.<br>Lasting impressions.</em></span></div>
<div class="hero-product-details"><div><p class="hero-product-type">THE SIGNATURE COLLECTION</p><p class="hero-product-name">Lumière Eau de Parfum</p><p class="hero-product-price">₹2,450 <span>· In stock</span></p></div><span class="hero-sample-button" aria-hidden="true">+</span></div>
<div class="hero-store-bottom"><span>Considered essentials, made for you.</span><span aria-hidden="true">↗</span></div>
<div class="hero-phone-home" aria-hidden="true"></div>
</div>
</div>
<span class="hero-phone-annotation" aria-hidden="true">Your next<br><em>big beginning.</em><span>↙</span></span>
</div>
<figcaption class="hero-showcase-footer"><span class="hero-style-note"><span class="hero-swatches" aria-hidden="true"><i></i><i></i><i></i></span>Your brand. Your style.</span><span>Illustrative store preview</span></figcaption>
</figure>
</div>
</section>
<div class="creator-businesses">
<div class="container-page"><p>A home for independent brands</p><div><span>Handmade jewelry</span><span>Streetwear</span><span>Aesthetic decor</span><span>Artisan skincare</span><span>Signature scents</span></div></div>
</div>
<section class="py-10" aria-labelledby="platform-summary-heading">
<div class="container-page">
<h2 id="platform-summary-heading" class="text-lg font-medium">What is MetoHub?</h2>
<p class="mt-3 leading-relaxed text-muted-foreground">{{ app(\App\Services\MarketingContent::class)->summary() }}</p>
</div>
</section>
<section id="demos" class="py-20 md:py-28">
<div class="container-page">
<div class="max-w-2xl">
<p class="eyebrow">Demo stores</p>
<h2 class="mt-4 text-4xl font-medium tracking-[-0.02em] md:text-5xl">Small brands. Big possibilities.<br>
<span class="font-serif italic font-normal text-bronze">Find your kind of store.</span>
</h2>
<p class="mt-5 text-muted-foreground md:text-lg">Your handmade pieces. Your next clothing drop. Your signature collection. Explore these sample stores and picture a space that feels like you.</p>
</div>
@if($demos->isEmpty())<div class="mt-12 rounded-xl border border-border bg-card p-8"><h3 class="text-xl font-medium">Your brand deserves its own space.</h3><p class="mt-3 text-muted-foreground">Explore our themes and choose your store’s look.</p><a class="btn-primary mt-6" href="{{ route('landing.themes') }}">Explore themes</a></div>@endif
<div class="demo-carousel mt-12" id="demoCarousel">
<div class="demo-carousel-controls" id="demoCarouselControls" hidden>
<span class="text-sm text-muted-foreground">Explore the collection · swipe or scroll</span>
<div class="demo-carousel-buttons">
<button type="button" id="demoPrevious" aria-label="Previous demo store" aria-controls="demoViewport">←</button>
<button type="button" id="demoMotion" aria-label="Pause automatic demo scrolling" aria-pressed="false" aria-controls="demoViewport">Pause</button>
<button type="button" id="demoNext" aria-label="Next demo store" aria-controls="demoViewport">→</button>
</div>
</div>
<div class="demo-carousel-viewport" id="demoViewport" role="region" aria-roledescription="carousel" aria-label="Demo stores" tabindex="0">
<div class="demo-carousel-track" id="demoTrack">@if($demos->has('mens-clothing'))
<a href="{{ $demos['mens-clothing']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" class="group block overflow-hidden rounded-xl border border-border bg-card transition hover:-translate-y-1 hover:shadow-lift focus-visible:outline-2 focus-visible:outline-ring " data-demo="mens-clothing">
<div class="overflow-hidden border-b border-border">
<img src="{{ asset('images/landing/reference/demo-mens.jpg') }}" alt="Illustrative storefront design for {{ $demos['mens-clothing']['tenant']->name }}" loading="lazy" width="1280" height="800" class="w-full object-cover object-top transition duration-700 group-hover:scale-[1.03] aspect-[16/10]">
</div>
<div class="flex items-center justify-between gap-4 p-5">
<div>
<p class="text-xs font-semibold uppercase tracking-[0.14em] text-bronze">Men’s clothing</p>
<p class="mt-1 text-lg font-semibold text-foreground">{{ $demos['mens-clothing']['tenant']->name }}</p>
<p class="text-sm text-muted-foreground">{{ config('storefront_themes.'.$demos['mens-clothing']['tenant']->theme.'.name', 'MetoHub') }} theme</p>
<p class="demo-workflow">{{ $demos['mens-clothing']['workflow'] ?? '' }}</p>
</div>
<span class="flex items-center gap-1 text-sm font-semibold text-foreground">Explore the store <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-right h-4 w-4 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true">
<path d="M7 7h10v10">
</path>
<path d="M7 17 17 7">
</path>
</svg>
<span class="sr-only">(opens in a new tab)</span>
</span>
</div>
</a>
@endif
@if($demos->has('grocery'))
<a href="{{ $demos['grocery']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" class="group block overflow-hidden rounded-xl border border-border bg-card transition hover:-translate-y-1 hover:shadow-lift focus-visible:outline-2 focus-visible:outline-ring " data-demo="grocery">
<div class="overflow-hidden border-b border-border">
<img src="{{ asset('images/landing/reference/demo-grocery.jpg') }}" alt="Illustrative storefront design for {{ $demos['grocery']['tenant']->name }}" loading="lazy" width="1280" height="800" class="w-full object-cover object-top transition duration-700 group-hover:scale-[1.03] aspect-[16/10]">
</div>
<div class="flex items-center justify-between gap-4 p-5">
<div>
<p class="text-xs font-semibold uppercase tracking-[0.14em] text-bronze">Grocery</p>
<p class="mt-1 text-lg font-semibold text-foreground">{{ $demos['grocery']['tenant']->name }}</p>
<p class="text-sm text-muted-foreground">{{ config('storefront_themes.'.$demos['grocery']['tenant']->theme.'.name', 'MetoHub') }} theme</p>
<p class="demo-workflow">{{ $demos['grocery']['workflow'] ?? '' }}</p>
</div>
<span class="flex items-center gap-1 text-sm font-semibold text-foreground">Explore the store <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-right h-4 w-4 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true">
<path d="M7 7h10v10">
</path>
<path d="M7 17 17 7">
</path>
</svg>
<span class="sr-only">(opens in a new tab)</span>
</span>
</div>
</a>
@endif
@if($demos->has('gadgets'))
<a href="{{ $demos['gadgets']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" class="group block overflow-hidden rounded-xl border border-border bg-card transition hover:-translate-y-1 hover:shadow-lift focus-visible:outline-2 focus-visible:outline-ring " data-demo="gadgets">
<div class="overflow-hidden border-b border-border">
<img src="{{ asset('images/landing/reference/demo-gadgets.jpg') }}" alt="Illustrative storefront design for {{ $demos['gadgets']['tenant']->name }}" loading="lazy" width="1280" height="800" class="w-full object-cover object-top transition duration-700 group-hover:scale-[1.03] aspect-[16/10]">
</div>
<div class="flex items-center justify-between gap-4 p-5">
<div>
<p class="text-xs font-semibold uppercase tracking-[0.14em] text-bronze">Gadgets</p>
<p class="mt-1 text-lg font-semibold text-foreground">{{ $demos['gadgets']['tenant']->name }}</p>
<p class="text-sm text-muted-foreground">{{ config('storefront_themes.'.$demos['gadgets']['tenant']->theme.'.name', 'MetoHub') }} theme</p>
<p class="demo-workflow">{{ $demos['gadgets']['workflow'] ?? '' }}</p>
</div>
<span class="flex items-center gap-1 text-sm font-semibold text-foreground">Explore the store <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-right h-4 w-4 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true">
<path d="M7 7h10v10">
</path>
<path d="M7 17 17 7">
</path>
</svg>
<span class="sr-only">(opens in a new tab)</span>
</span>
</div>
</a>
@endif
@if($demos->has('jewelry'))
<a href="{{ $demos['jewelry']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" class="group block overflow-hidden rounded-xl border border-border bg-card transition hover:-translate-y-1 hover:shadow-lift focus-visible:outline-2 focus-visible:outline-ring " data-demo="jewelry">
<div class="overflow-hidden border-b border-border">
<img src="{{ asset('images/landing/reference/demo-jewelry.jpg') }}" alt="Illustrative storefront design for {{ $demos['jewelry']['tenant']->name }}" loading="lazy" width="1280" height="800" class="w-full object-cover object-top transition duration-700 group-hover:scale-[1.03] aspect-[16/10]">
</div>
<div class="flex items-center justify-between gap-4 p-5">
<div>
<p class="text-xs font-semibold uppercase tracking-[0.14em] text-bronze">Jewelry</p>
<p class="mt-1 text-lg font-semibold text-foreground">{{ $demos['jewelry']['tenant']->name }}</p>
<p class="text-sm text-muted-foreground">{{ config('storefront_themes.'.$demos['jewelry']['tenant']->theme.'.name', 'MetoHub') }} theme</p>
<p class="demo-workflow">{{ $demos['jewelry']['workflow'] ?? '' }}</p>
</div>
<span class="flex items-center gap-1 text-sm font-semibold text-foreground">Explore the store <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-right h-4 w-4 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true">
<path d="M7 7h10v10">
</path>
<path d="M7 17 17 7">
</path>
</svg>
<span class="sr-only">(opens in a new tab)</span>
</span>
</div>
</a>
@endif
@if($demos->has('watches'))
<a href="{{ $demos['watches']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" class="group block overflow-hidden rounded-xl border border-border bg-card transition hover:-translate-y-1 hover:shadow-lift focus-visible:outline-2 focus-visible:outline-ring " data-demo="watches">
<div class="overflow-hidden border-b border-border">
<img src="{{ asset('images/landing/reference/demo-watches.jpg') }}" alt="Illustrative storefront design for {{ $demos['watches']['tenant']->name }}" loading="lazy" width="1280" height="800" class="w-full object-cover object-top transition duration-700 group-hover:scale-[1.03] aspect-[16/10]">
</div>
<div class="flex items-center justify-between gap-4 p-5">
<div>
<p class="text-xs font-semibold uppercase tracking-[0.14em] text-bronze">Watches</p>
<p class="mt-1 text-lg font-semibold text-foreground">{{ $demos['watches']['tenant']->name }}</p>
<p class="text-sm text-muted-foreground">{{ config('storefront_themes.'.$demos['watches']['tenant']->theme.'.name', 'MetoHub') }} theme</p>
<p class="demo-workflow">{{ $demos['watches']['workflow'] ?? '' }}</p>
</div>
<span class="flex items-center gap-1 text-sm font-semibold text-foreground">Explore the store <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-right h-4 w-4 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true">
<path d="M7 7h10v10">
</path>
<path d="M7 17 17 7">
</path>
</svg>
<span class="sr-only">(opens in a new tab)</span>
</span>
</div>
</a>
@endif
@if($demos->has('womens-clothing'))
<a href="{{ $demos['womens-clothing']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" class="group block overflow-hidden rounded-xl border border-border bg-card transition hover:-translate-y-1 hover:shadow-lift focus-visible:outline-2 focus-visible:outline-ring " data-demo="womens-clothing">
<div class="overflow-hidden border-b border-border">
<img src="{{ asset('images/landing/reference/demo-womens.jpg') }}" alt="Illustrative storefront design for {{ $demos['womens-clothing']['tenant']->name }}" loading="lazy" width="1280" height="800" class="w-full object-cover object-top transition duration-700 group-hover:scale-[1.03] aspect-[16/10]">
</div>
<div class="flex items-center justify-between gap-4 p-5">
<div>
<p class="text-xs font-semibold uppercase tracking-[0.14em] text-bronze">Women’s clothing</p>
<p class="mt-1 text-lg font-semibold text-foreground">{{ $demos['womens-clothing']['tenant']->name }}</p>
<p class="text-sm text-muted-foreground">{{ config('storefront_themes.'.$demos['womens-clothing']['tenant']->theme.'.name', 'MetoHub') }} theme</p>
<p class="demo-workflow">{{ $demos['womens-clothing']['workflow'] ?? '' }}</p>
</div>
<span class="flex items-center gap-1 text-sm font-semibold text-foreground">Explore the store <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-right h-4 w-4 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true">
<path d="M7 7h10v10">
</path>
<path d="M7 17 17 7">
</path>
</svg>
<span class="sr-only">(opens in a new tab)</span>
</span>
</div>
</a>
@endif
@if($demos->has('perfume'))
<a href="{{ $demos['perfume']['tenant']->storefrontUrl() }}" target="_blank" rel="noopener noreferrer" class="group block overflow-hidden rounded-xl border border-border bg-card transition hover:-translate-y-1 hover:shadow-lift focus-visible:outline-2 focus-visible:outline-ring md:col-span-2" data-demo="perfume">
<div class="overflow-hidden border-b border-border">
<img src="{{ asset('images/landing/reference/demo-perfume.jpg') }}" alt="Illustrative storefront design for {{ $demos['perfume']['tenant']->name }}" loading="lazy" width="1280" height="800" class="w-full object-cover object-top transition duration-700 group-hover:scale-[1.03] aspect-[16/7]">
</div>
<div class="flex items-center justify-between gap-4 p-5">
<div>
<p class="text-xs font-semibold uppercase tracking-[0.14em] text-bronze">Perfume</p>
<p class="mt-1 text-lg font-semibold text-foreground">{{ $demos['perfume']['tenant']->name }}</p>
<p class="text-sm text-muted-foreground">{{ config('storefront_themes.'.$demos['perfume']['tenant']->theme.'.name', 'MetoHub') }} theme</p>
<p class="demo-workflow">{{ $demos['perfume']['workflow'] ?? '' }}</p>
</div>
<span class="flex items-center gap-1 text-sm font-semibold text-foreground">Explore the store <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-right h-4 w-4 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true">
<path d="M7 7h10v10">
</path>
<path d="M7 17 17 7">
</path>
</svg>
<span class="sr-only">(opens in a new tab)</span>
</span>
</div>
</a>
@endif
</div>
</div>
</div>
<p class="mt-6 text-sm text-muted-foreground">These are sample stores with demo products. Previews are illustrative. Each opens in a new tab.</p>
</div>
</section>
<section id="how-it-works" class="bg-surface-stone py-20 md:py-28">
<div class="container-page">
<p class="eyebrow">How it works</p>
<h2 class="mt-4 max-w-2xl text-4xl font-medium tracking-[-0.02em] md:text-5xl">Three steps to your own store.</h2>
<div class="mt-12 grid gap-px overflow-hidden rounded-xl border border-border bg-border md:grid-cols-3">
<div class="bg-card p-8">
<span class="font-serif text-4xl italic text-bronze">01</span>
<h3 class="mt-6 text-xl font-semibold">Create your store</h3>
<p class="mt-3 leading-relaxed text-muted-foreground">Enter your name, email, business details and a password. Verify your email to open your dashboard.</p>
</div>
<div class="bg-card p-8">
<span class="font-serif text-4xl italic text-bronze">02</span>
<h3 class="mt-6 text-xl font-semibold">Make it yours</h3>
<p class="mt-3 leading-relaxed text-muted-foreground">Choose a theme, add your logo and colors, and upload products with prices, variants and stock.</p>
</div>
<div class="bg-card p-8">
<span class="font-serif text-4xl italic text-bronze">03</span>
<h3 class="mt-6 text-xl font-semibold">Share your store link</h3>
<p class="mt-3 leading-relaxed text-muted-foreground">Put the link in your Instagram bio or share it on WhatsApp. Manage every order from one dashboard.</p>
</div>
</div>
<div class="mt-10">
<a class="btn-primary" href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}" data-start="" data-plan="base">Create your store <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right h-4 w-4" aria-hidden="true">
<path d="M5 12h14">
</path>
<path d="m12 5 7 7-7 7">
</path>
</svg>
</a>
</div>
</div>
</section>
<section id="store-tools" class="py-20 md:py-28">
<div class="container-page grid gap-12 lg:grid-cols-[1fr_1.6fr] section-with-sticky-heading">
<div class="section-sticky-heading">
<p class="eyebrow">Everything you need</p>
<h2 class="mt-4 text-4xl font-medium tracking-[-0.02em] md:text-5xl">A storefront <span class="font-serif italic font-normal text-bronze">and</span> the tools to run it.</h2>
</div>
<div class="grid gap-x-10 gap-y-10 sm:grid-cols-2 feature-grid">
<div class="border-t border-border pt-5">
<h3 class="text-lg font-semibold"><span class="feature-check" aria-hidden="true">✓</span>Make it unmistakably you</h3>
<p class="mt-2 leading-relaxed text-muted-foreground">Themes, logo, colors and editable homepage content.</p>
<span class="feature-micro">Logo · Colors · Themes</span>
</div>
<div class="border-t border-border pt-5">
<h3 class="text-lg font-semibold"><span class="feature-check" aria-hidden="true">✓</span>Every size. Every piece.</h3>
<p class="mt-2 leading-relaxed text-muted-foreground">Products, collections, variants, sizes and stock in one place.</p>
<span class="feature-micro">Sizes · Variants · Stock</span>
</div>
<div class="border-t border-border pt-5">
<h3 class="text-lg font-semibold"><span class="feature-check" aria-hidden="true">✓</span>Make the next drop irresistible</h3>
<p class="mt-2 leading-relaxed text-muted-foreground">Create bundles and discount codes for your customers.</p>
<span class="feature-micro">Bundles · Discount codes</span>
</div>
<div class="border-t border-border pt-5">
<h3 class="text-lg font-semibold"><span class="feature-check" aria-hidden="true">✓</span>Orders without the screenshot hunt</h3>
<p class="mt-2 leading-relaxed text-muted-foreground">Order details, fulfillment status and payment recording.</p>
<span class="feature-micro">New → Confirmed → Dispatched</span>
</div>
<div class="border-t border-border pt-5">
<h3 class="text-lg font-semibold"><span class="feature-check" aria-hidden="true">✓</span>Packing labels &amp; invoices</h3>
<p class="mt-2 leading-relaxed text-muted-foreground">Order invoices and store-generated packing labels.</p>
<span class="feature-micro">Invoice ✓ · Packing label ✓</span>
</div>
<div class="border-t border-border pt-5">
<h3 class="text-lg font-semibold"><span class="feature-check" aria-hidden="true">✓</span>A new order? You’ll know.</h3>
<p class="mt-2 leading-relaxed text-muted-foreground">Order emails, an in-dashboard notification bell and optional device alerts.</p>
<span class="feature-micro">Email · Dashboard bell · Device alerts</span>
</div>
</div>
</div>
</section>
@if($reviews->isNotEmpty())
<section id="client-reviews" class="client-reviews py-20 md:py-28" aria-labelledby="reviews-title">
<div class="container-page">
@if($reviews->every(fn ($review) => ($review['sample'] ?? false) === true))
<p class="eyebrow">Imagining your next chapter</p>
<h2 id="reviews-title" class="mt-4 text-4xl font-medium tracking-[-0.02em] md:text-5xl">Small brands. <span class="font-serif italic font-normal text-bronze">Real ambitions.</span></h2>
<p class="reviews-sample-note">Illustrative seller stories for this design preview. Names and quotes are fictional examples, not MetoHub client reviews.</p>
@else
<p class="eyebrow">From the people behind the brands</p>
<h2 id="reviews-title" class="mt-4 text-4xl font-medium tracking-[-0.02em] md:text-5xl">Their stores. <span class="font-serif italic font-normal text-bronze">Their words.</span></h2>
@endif
<div class="review-carousel" id="reviewCarousel">
<div class="demo-carousel-controls" id="reviewCarouselControls" hidden>
<span class="text-sm text-muted-foreground">Seller stories · swipe or scroll</span>
<div class="demo-carousel-buttons">
<button type="button" id="reviewPrevious" aria-label="Previous seller story" aria-controls="reviewViewport">←</button>
<button type="button" id="reviewMotion" aria-label="Pause automatic review scrolling" aria-pressed="false" aria-controls="reviewViewport">Pause</button>
<button type="button" id="reviewNext" aria-label="Next seller story" aria-controls="reviewViewport">→</button>
</div>
</div>
<div class="demo-carousel-viewport" id="reviewViewport" role="region" aria-roledescription="carousel" aria-label="Seller stories" tabindex="0">
<div class="review-track" id="reviewTrack">
@foreach($reviews as $review)
<figure class="review-card" data-review @if(($review['sample'] ?? false) === true) data-nosnippet @endif>@if(($review['sample'] ?? false) === true)<span class="review-sample-label">Illustrative example</span>@endif<span class="review-mark" aria-hidden="true">“</span><blockquote>{{ $review['quote'] }}</blockquote><figcaption><span class="review-initial" aria-hidden="true">{{ mb_substr(trim($review['name']), 0, 1) }}</span><span><strong>{{ $review['name'] }}</strong>@if(!empty($review['store']))<small>{{ $review['store'] }}</small>@endif</span></figcaption></figure>
@endforeach
</div>
</div>
</div>
</div>
</section>
@endif
<section id="pricing" class="bg-surface-champagne py-20 md:py-28">
<div class="container-page">
<div class="mx-auto max-w-2xl text-center">
<p class="eyebrow">Pricing</p>
<h2 class="mt-4 text-4xl font-medium tracking-[-0.02em] md:text-5xl">Your own website from ₹{{ number_format(config('saas.plans.base.monthly_price')) }}/month.</h2>
<p class="mt-4 text-muted-foreground md:text-lg">Try your store before choosing a paid plan.<br>Every plan starts with a {{ config('saas.trial_days') }}-day free trial.</p>
</div>
<div class="mt-12 grid gap-6 lg:grid-cols-3">
<div class="relative flex flex-col rounded-xl border bg-card p-8 border-border shadow-soft">
<h3 class="text-lg font-semibold">{{ config('saas.plans.base.name') }}</h3>
<p class="mt-1 text-sm text-muted-foreground">Start your own online store</p>
<p class="mt-6">
<span class="text-5xl font-semibold tracking-tight">₹{{ number_format(config('saas.plans.base.monthly_price')) }}</span>
<span class="text-muted-foreground">/month</span>
</p>
<ul class="mt-6 flex-1 space-y-3 text-sm">
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Your MetoHub store link</li>
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Themes, logo and brand colors</li>
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Products, collections, variants and stock</li>
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Order dashboard, invoices and packing labels</li>
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Discount codes and bundles</li>
</ul>
<a class="mt-8 btn-ghost" href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}" data-start="" data-plan="base">Start {{ config('saas.trial_days') }}-day free trial</a>
@include('landing.partials.trial-reassurance')
</div>
<div class="relative flex flex-col rounded-xl border bg-card p-8 pricing-recommended">
<span class="pricing-recommended-badge">Recommended for growing brands</span>
<h3 class="text-lg font-semibold">{{ config('saas.plans.pro.name') }}</h3>
<p class="mt-1 text-sm text-muted-foreground">Your own domain. Your name on every link.</p>
<p class="mt-6">
<span class="text-5xl font-semibold tracking-tight">₹{{ number_format(config('saas.plans.pro.monthly_price')) }}</span>
<span class="text-muted-foreground">/month</span>
</p>
<ul class="mt-6 flex-1 space-y-3 text-sm">
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Everything in Base</li>
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Connect your own domain, like yourbrand.in</li>
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Advanced analytics</li>
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Remove MetoHub footer branding</li>
</ul>
<p class="pricing-domain-note">Connect a domain you own. Domain purchase is separate.</p>
<a class="mt-8 btn-primary" href="{{ route('landing', ['get_started' => 1, 'plan' => 'pro']) }}" data-start="" data-plan="pro">Start {{ config('saas.trial_days') }}-day free trial</a>
@include('landing.partials.trial-reassurance')
</div>
<div class="relative flex flex-col rounded-xl border bg-card p-8 border-border shadow-soft">
<h3 class="text-lg font-semibold">{{ config('saas.plans.enterprise.name') }}</h3>
<p class="mt-1 text-sm text-muted-foreground">Pro, plus additional support</p>
<p class="mt-6">
<span class="text-5xl font-semibold tracking-tight">₹{{ number_format(config('saas.plans.enterprise.monthly_price')) }}</span>
<span class="text-muted-foreground">/month</span>
</p>
<ul class="mt-6 flex-1 space-y-3 text-sm">
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Everything in Pro</li>
<li class="flex gap-2.5">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check mt-0.5 h-4 w-4 shrink-0 text-bronze" aria-hidden="true">
<path d="M20 6 9 17l-5-5">
</path>
</svg>Additional support offering</li>
</ul>
<a class="mt-8 btn-ghost" href="{{ route('landing', ['get_started' => 1, 'plan' => 'enterprise']) }}" data-start="" data-plan="enterprise">Start {{ config('saas.trial_days') }}-day free trial</a>
@include('landing.partials.trial-reassurance')
</div>
</div>
<p class="mx-auto mt-8 max-w-2xl text-center text-sm text-muted-foreground">Prices shown are for monthly subscriptions. A paid plan is required after the trial. Domain registration is separate from connecting a domain.</p>
</div>
</section>
<section id="faq" class="py-20 md:py-28">
<div class="container-page grid gap-12 lg:grid-cols-[1fr_1.6fr] section-with-sticky-heading">
<div class="section-sticky-heading">
<p class="eyebrow">Questions</p>
<h2 class="mt-4 text-4xl font-medium tracking-[-0.02em] md:text-5xl">Good to know.</h2>
<p class="faq-intro">The practical details, before your big beginning. Find answers about setting up, choosing a plan, and taking your first orders.</p>
<div class="faq-help-card">
<span class="faq-help-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5H4l-3 2v-9.5A8.5 8.5 0 0 1 9.5 4h3A8.5 8.5 0 0 1 21 11.5Z"/><path d="M7 12h8M7 16h5"/></svg></span>
<h3>Still have a question?</h3>
<p>Tell us about your store and what you’d like to build.</p>
<a href="{{ route('landing.contact') }}">Let’s talk <span aria-hidden="true">↗</span></a>
</div>
</div>
<div class="divide-y divide-border border-y border-border">
@foreach(app(\App\Services\MarketingContent::class)->faqs() as $faq)
<details class="group py-5">
<summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-lg font-medium"><h3 style="font: inherit; margin: 0">{{ $faq['question'] }}</h3><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-5 w-5 shrink-0 text-muted-foreground transition group-open:rotate-180" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg></summary>
<p class="mt-3 leading-relaxed text-muted-foreground">{{ $faq['answer'] }}</p>
</details>
@endforeach
</div>
</div>
</section>
<section class="bg-surface-hero py-24 md:py-32">
<div class="container-page text-center">
<h2 class="text-4xl font-medium tracking-[-0.03em] md:text-6xl">You built the brand.<br>
<span class="font-serif italic font-normal text-bronze">Now give it a home.</span>
</h2>
<p class="mx-auto mt-6 max-w-md text-muted-foreground md:text-lg">Your own online store from ₹{{ number_format(config('saas.plans.base.monthly_price')) }}/month. Start with a {{ config('saas.trial_days') }}-day free trial.</p>
<a class="btn-primary mt-10 !px-8 !py-4 text-base" href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}" data-start="" data-plan="base">Create your store <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right h-4 w-4" aria-hidden="true">
<path d="M5 12h14">
</path>
<path d="m12 5 7 7-7 7">
</path>
</svg>
</a>
@include('landing.partials.trial-reassurance')
</div>
</section>
</main>
@include('landing.partials.footer')
</div>
<dialog class="wizard" id="wizard" aria-labelledby="wt">
  <div class="w-head"><h2 id="wt">Create your store</h2><button class="w-close" type="button" id="wClose" aria-label="Close registration">✕</button></div>
  <div class="prog"><div class="bar" aria-hidden="true"><i id="wBar"></i></div><small id="wLabel" aria-live="polite">Step 1 of 3: About you</small></div>
  <div class="w-body">
    <form id="wForm" method="POST" action="{{ route('saas.register') }}" novalidate>
      @csrf
      <div class="chosen"><span>Plan: <strong id="planName">{{ config('saas.plans.base.name') }}, ₹{{ config('saas.plans.base.monthly_price') }}/month</strong><small class="trial-note">{{ config('saas.trial_days') }}-day trial. Choose your paid subscription in Billing after verification.</small></span>
        <select id="planSel" name="plan" aria-label="Change plan">@foreach(config('saas.plans') as $id=>$plan)<option value="{{ $id }}">{{ $plan['name'] }}</option>@endforeach</select></div>
      <p class="form-status" id="formStatus" role="alert" hidden></p>
      <div class="pane active" data-step="1">
        <div class="signup-field"><label for="f_name">Your name</label><input id="f_name" name="name" autocomplete="name" required maxlength="255" aria-describedby="err_name"><div class="err" id="err_name"></div></div>
        <div class="signup-field"><label for="f_email">Email</label><input id="f_email" name="email" type="email" autocomplete="email" required maxlength="255" aria-describedby="err_email"><div class="err" id="err_email"></div></div>
        <div class="signup-field"><label for="f_phone">Phone or WhatsApp number</label><input id="f_phone" name="whatsapp" type="tel" inputmode="tel" autocomplete="tel" required maxlength="30" placeholder="+91 98765 43210" aria-describedby="err_whatsapp"><div class="err" id="err_whatsapp"></div></div>
      </div>
      <div class="pane" data-step="2">
        <div class="signup-field"><label for="f_biz">Business name</label><input id="f_biz" name="business" autocomplete="organization" required maxlength="255" aria-describedby="err_business"><div class="err" id="err_business"></div></div>
        <div class="signup-field"><label for="f_country">Country</label><select id="f_country" name="country" required autocomplete="country-name" aria-describedby="err_country"><option value="">Choose your country</option>@foreach(['India','United Arab Emirates','Saudi Arabia','United Kingdom','United States','France','Singapore','Australia','Other'] as $country)<option value="{{ $country }}">{{ $country }}</option>@endforeach</select><div class="err" id="err_country"></div></div>
        <p class="fine">You can personalize your storefront and add products after verifying your email.</p>
      </div>
      <div class="pane" data-step="3">
        <div class="two">
          <div class="signup-field"><label for="f_pw">Password</label><input id="f_pw" name="password" type="password" autocomplete="new-password" required minlength="8" aria-describedby="err_password"><div class="err" id="err_password"></div></div>
          <div class="signup-field"><label for="f_pw2">Confirm password</label><input id="f_pw2" name="password_confirmation" type="password" autocomplete="new-password" required aria-describedby="err_password_confirmation"><div class="err" id="err_password_confirmation"></div></div>
        </div>
        <fieldset style="border:0"><legend style="font-size:.9375rem;font-weight:500;color:var(--text);margin-bottom:8px">Pick a starting theme. You can change it later.</legend>
          <div class="themes">@foreach(config('storefront_themes') as $id=>$theme)<label><input type="radio" name="theme" value="{{ $id }}" {{ $id==='template_7'?'checked':'' }}> {{ $theme['name'] }}</label>@endforeach</div><p id="err_theme" class="theme-errors" role="alert"></p>
        </fieldset>
        <p class="fine">After your account is created, verify your email to open your dashboard. <a href="{{ route('landing.terms') }}" target="_blank" rel="noopener">Terms</a> · <a href="{{ route('landing.privacy') }}" target="_blank" rel="noopener">Privacy</a></p>
      </div>
      <div class="w-foot"><button class="btn btn-ghost" type="button" id="wBack" hidden>Back</button><button class="btn btn-primary" type="submit" id="wNext" style="margin-left:auto">Continue</button></div>
    </form>
    <div class="done" id="wDone" tabindex="-1" hidden><div class="tick" aria-hidden="true">✓</div><h3 id="doneTitle">Your account is created</h3><p id="doneMessage" role="status"></p><p class="done-email" id="doneEmail"></p><p class="fine">After verification, sign in to choose your products, customize your theme, and share your store.</p><div class="done-actions"><button class="btn btn-ghost" id="resendVerification" type="button">Resend verification link</button><a class="btn btn-primary" href="{{ route('admin.common.login') }}">Go to merchant login</a></div><p class="fine" id="resendStatus" role="status"></p></div>
  </div>
</dialog>
<script id="landing-config" type="application/json">@json($landingConfig)</script>
<script src="{{ asset('js/metohub-home.js') }}?v={{ filemtime(public_path('js/metohub-home.js')) }}" defer></script>
</body>
</html>
