@extends('layouts.landing')
@section('page_title', 'Your online store from ₹'.config('saas.plans.base.monthly_price').'/month | MetoHub')
@section('meta_description', 'Turn your Instagram business into an online store with MetoHub. Explore demo stores, choose your look, and start a '.config('saas.trial_days').'-day free trial. Plans from ₹'.config('saas.plans.base.monthly_price').'/month.')
@section('meta_image', asset('merchant-icon-512.png'))
@section('content')
<link rel="stylesheet" href="{{ asset('css/metohub-home.css') }}?v={{ filemtime(public_path('css/metohub-home.css')) }}">
<div class="mh-home">
<section class="mh-hero mh-wrap">
    <div class="mh-hero-copy">
        <span class="mh-eyebrow"><span class="mh-dot"></span> FOR SMALL BRANDS WITH BIG PLANS</span>
        <h1>Your products.<br>Your brand.<br><em>Your own store.</em></h1>
        <p class="mh-lead">Turn “DM to order” into a link your customers can shop. Create a beautiful online store, manage your products, and keep every order in one place.</p>
        <div class="mh-offer"><strong>₹{{ number_format(config('saas.plans.base.monthly_price')) }}<span>/month</span></strong><span>for your own online store.<br>Start with {{ config('saas.trial_days') }} days free.</span></div>
        <div class="mh-actions"><a href="{{ route('landing', ['get_started'=>1]) }}" class="mh-button pricing-btn-trigger" data-plan="base">Start my free trial <span aria-hidden="true">↗</span></a><a href="#demos" class="mh-text-link">Explore demo stores <span aria-hidden="true">↓</span></a></div>
        <p class="mh-note">No coding needed · Choose a theme · Make it yours</p>
    </div>
    <div class="mh-hero-art" aria-label="An illustrated example of a MetoHub store">
        <span class="mh-art-label">A little preview of your next chapter</span>
        <div class="mh-browser"><div class="mh-browser-bar"><span aria-hidden="true">● ● ●</span><span>your brand, online</span><span aria-hidden="true">↗</span></div>
            <div class="mh-sample-brand"><span class="mh-monogram">L&G</span><strong>Loom & Grace</strong><span>Shop the edit ↗</span></div>
            <div class="mh-sample-hero"><span>THE EVERYDAY EDIT</span><h2>A little more<br>you, every day.</h2><span class="mh-sample-pill">Discover the collection →</span><div class="mh-dress" aria-hidden="true"><svg viewBox="0 0 180 230"><path d="M55 20L78 9Q90 35 102 9L125 20L145 75L119 85L113 65L110 100L150 215Q90 235 30 215L70 100L67 65L61 85L35 75Z" fill="#a6576d"/><path d="M70 100H110M80 35L90 42L100 35" stroke="#f9d8d4" fill="none" stroke-width="3"/></svg></div></div>
            <div class="mh-sample-products"><div><span class="mh-swatch mh-swatch-one"></span><strong>The linen edit</strong><small>Made for everyday</small></div><div><span class="mh-swatch mh-swatch-two"></span><strong>Easy essentials</strong><small>Your next favorite</small></div><div><span class="mh-swatch mh-swatch-three"></span><strong>Fresh arrivals</strong><small>Find your kind of style</small></div></div>
        </div><div class="mh-art-sticker"><span aria-hidden="true">✦</span> YOUR BRAND.<br> OPEN FOR BUSINESS.</div><span class="mh-example">Illustrative store preview</span>
    </div>
</section>
<div class="mh-strip"><span>CLOTHING</span><span aria-hidden="true">✦</span><span>GROCERY</span><span aria-hidden="true">✦</span><span>GADGETS</span><span aria-hidden="true">✦</span><span>JEWELRY</span><span aria-hidden="true">✦</span><span>WATCHES</span><span aria-hidden="true">✦</span><span>PERFUME</span></div>
<section class="mh-section mh-wrap" id="demos">
    <div class="mh-section-heading"><div><span class="mh-eyebrow">TAKE A LOOK AROUND</span><h2>Different businesses.<br>Different looks. <em>All yours.</em></h2></div><p>Open a demo, browse the products, and imagine your brand here. Every store has its own theme, colors, and personality.</p></div>
    @if($demos->isNotEmpty())
    <div class="mh-demo-grid">
        @foreach($demos as $slug => $demo)
        @php($store = $demo['tenant'])
        <a class="mh-demo-card" href="{{ $store->storefrontUrl() }}" target="_blank" rel="noopener" style="--demo-color:{{ $demo['color'] }}" aria-label="Explore {{ $demo['category'] }} demo: {{ $store->name }} (opens in a new tab)">
            <div class="mh-demo-preview"><div class="mh-demo-nav">@if($store->logo)<img src="{{ Storage::url($store->logo) }}" width="28" height="28" alt="" loading="lazy">@endif<strong>{{ $store->name }}</strong><span aria-hidden="true">↗</span></div><div class="mh-demo-welcome"><small>{{ $demo['category'] }}</small><h3>{{ $store->hero_heading }}</h3></div><div class="mh-demo-products">@foreach($demo['products'] as $product)<div>@if($product->main_image_url)<img src="{{ $product->main_image_url }}" alt="{{ $product->title }}" loading="lazy" width="160" height="175">@endif<span>{{ $product->title }}</span></div>@endforeach</div></div>
            <div class="mh-demo-caption"><div><h3>{{ $demo['category'] }}</h3><p>{{ config('storefront_themes.'.$store->theme.'.name', 'MetoHub theme') }}</p></div><span class="mh-circle-arrow" aria-hidden="true">↗</span></div>
        </a>
        @endforeach
        <div class="mh-demo-invite"><span class="mh-eyebrow">NEXT UP: YOUR BRAND</span><h3>A store that<br>feels like you.</h3><p>Choose from {{ count(config('storefront_themes')) }} themes. Add your colors, your story, and the products you love.</p><a href="{{ route('landing', ['get_started'=>1]) }}" class="mh-button pricing-btn-trigger" data-plan="base">Create my store ↗</a></div>
    </div>
    <p class="mh-note">These are sample stores with demo products. Explore freely; no real purchases or deliveries are made.</p>
    @else
    <div class="mh-empty"><h3>Your brand deserves its own space.</h3><p>Explore our theme collection and choose the look for your store.</p><a class="mh-button" href="{{ route('landing.themes') }}">Explore themes ↗</a></div>
    @endif
</section>
<section class="mh-how" id="how-it-works"><div class="mh-wrap mh-section"><div class="mh-section-heading"><div><span class="mh-eyebrow">FROM AN IDEA TO A STORE LINK</span><h2>You bring the products.<br>We bring <em>the storefront.</em></h2></div><p>A simple setup you can manage yourself, even if this is your first website.</p></div><div class="mh-steps"><article><span>01 / MAKE IT OFFICIAL</span><h3>Create your store</h3><p>Enter your details and store name. Verify your email to access your merchant dashboard.</p></article><article><span>02 / MAKE IT YOURS</span><h3>Add your personal touch</h3><p>Pick a theme, add your logo and colors, then upload products with prices, sizes, and stock.</p></article><article><span>03 / SHARE IT EVERYWHERE</span><h3>Put your link in your bio</h3><p>Share your store with customers on Instagram and WhatsApp. Receive orders and manage them from your dashboard.</p></article></div></div></section>
<section class="mh-section mh-wrap" id="features"><div class="mh-section-heading"><div><span class="mh-eyebrow">LESS CHASING. MORE SELLING.</span><h2>Your business has a lot<br>of moving parts. <em>Bring them together.</em></h2></div></div><div class="mh-features"><article><span aria-hidden="true">◈</span><h3>A look of your own</h3><p>{{ count(config('storefront_themes')) }} themes with editable brand colors, homepage copy, and mobile layouts.</p></article><article><span aria-hidden="true">▦</span><h3>A catalog that stays organized</h3><p>Products, collections, sizes, stock, bundles, and discount codes, managed in one dashboard.</p></article><article><span aria-hidden="true">↗</span><h3>Orders you can follow</h3><p>Manage fulfillment, record payments received, and print order invoices and packing labels.</p></article><article><span aria-hidden="true">◎</span><h3>Stay in the loop</h3><p>New-order emails and an in-site notification bell. Enable device alerts on supported browsers.</p></article><article><span aria-hidden="true">⌕</span><h3>Ready to be discovered</h3><p>Edit search titles and descriptions, share product links, and connect supported analytics tools.</p></article><article><span aria-hidden="true">◇</span><h3>Start simple, grow your way</h3><p>Begin with a MetoHub store link. Upgrade to Pro when you want to connect your own domain.</p></article></div><p class="mh-note">Customer checkout currently supports cash on delivery. Online customer payments are not included in this offer.</p></section>
<section class="mh-pricing" id="pricing"><div class="mh-wrap mh-section"><div class="mh-section-heading"><div><span class="mh-eyebrow">A SMALL PRICE FOR YOUR NEXT BIG STEP</span><h2>Your website.<br>From <em>₹{{ number_format(config('saas.plans.base.monthly_price')) }}/month.</em></h2></div><p>Try MetoHub free for {{ config('saas.trial_days') }} days. Choose a subscription to keep your store active after the trial.</p></div><div class="mh-plan-grid">
@foreach(config('saas.plans') as $id => $plan)
<article class="mh-plan {{ $id === 'base' ? 'mh-plan-featured' : '' }}">@if($id==='base')<span class="mh-plan-label">A GREAT PLACE TO START</span>@endif<h3>{{ $plan['name'] }}</h3><p>{{ $id==='base'?'Your first online store, with the essentials to get going.':($id==='pro'?'A more personal home for your growing brand.':'For businesses looking for closer support.') }}</p><div class="mh-price">₹{{ number_format($plan['monthly_price']) }}<span>/month</span></div><a href="{{ route('landing', ['get_started'=>1]) }}" class="mh-button {{ $id!=='base'?'mh-button-light':'' }} pricing-btn-trigger" data-plan="{{ $id }}">Start {{ config('saas.trial_days') }}-day free trial ↗</a><ul>
@foreach($id==='base'?['Your own online store','Unlimited products and orders','All '.count(config('storefront_themes')).' storefront themes','A shareable MetoHub store link','Product, stock and order management'] : ($id==='pro'?['Everything in Base','Connect your own domain','Advanced analytics and reports','Remove MetoHub footer branding']:['Everything in Pro','Priority support coordination','Dedicated account manager']) as $feature)<li><span aria-hidden="true">✓</span> {{ $feature }}</li>@endforeach
</ul></article>
@endforeach
</div><p class="mh-note">Prices shown are for monthly subscriptions. Domain registration is separate. A paid plan is required after the free trial.</p></div></section>
<section class="mh-section mh-wrap mh-faq" id="faq"><div><span class="mh-eyebrow">GOOD QUESTIONS</span><h2>A few things<br>you might <em>be wondering.</em></h2><a href="{{ route('landing.contact') }}" class="mh-text-link">Talk to us ↗</a></div><div class="mh-questions">
@foreach([
'Is it really ₹'.config('saas.plans.base.monthly_price').' per month?' => 'Yes. The Base plan costs ₹'.config('saas.plans.base.monthly_price').' per month. Pro and Enterprise have different prices, shown above. You can start with a '.config('saas.trial_days').'-day free trial.',
'Do I need to know how to code?' => 'No. You can choose a theme, upload your products, and edit your store details in the merchant dashboard.',
'Can I sell something other than clothing?' => 'Yes. Explore the grocery, gadgets, jewelry, watches, and perfume demos above. You can organize your own catalog into collections and variants.',
'How do customers pay for their orders?' => 'Customer checkout currently supports cash on delivery. You can record a payment as received in your dashboard. The Razorpay integration collects your MetoHub subscription, not customer order payments.',
'Can I use my own domain?' => 'Yes, with Pro or Enterprise. You will need to own the domain and connect it in your store settings. Domain registration is purchased separately.',
'What happens after my trial?' => 'Choose a paid subscription in Billing to keep managing your store. If your trial or paid period ends, editing is restricted, while historical records remain available.'
] as $question=>$answer)<details><summary>{{ $question }}<span aria-hidden="true">+</span></summary><p>{{ $answer }}</p></details>@endforeach
</div></section>
<section class="mh-final"><div class="mh-wrap"><span class="mh-eyebrow">YOUR NEXT CHAPTER STARTS WITH A LINK</span><h2>You built the brand.<br>Now give it <em>a home.</em></h2><p>Your own online store from ₹{{ config('saas.plans.base.monthly_price') }}/month.<br>Take the first step with a {{ config('saas.trial_days') }}-day free trial.</p><a href="{{ route('landing', ['get_started'=>1]) }}" class="mh-button pricing-btn-trigger" data-plan="base">Let’s create my store ↗</a></div></section>
</div>
@endsection
@section('footer')
<footer class="mh-footer"><div class="mh-wrap"><div><a href="{{ route('landing') }}" class="mh-footer-brand">MetoHub<span>✦</span></a><p>A home for independent businesses.</p></div><nav aria-label="Footer"><a href="{{ route('admin.common.login') }}">Merchant login</a><a href="{{ route('landing.contact') }}">Contact</a><a href="{{ route('landing.privacy') }}">Privacy</a><a href="{{ route('landing.terms') }}">Terms</a><a href="{{ route('landing.refund') }}">Refund policy</a></nav><small>© {{ date('Y') }} MetoHub</small></div></footer>
@endsection
