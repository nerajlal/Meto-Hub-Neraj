<link rel="stylesheet" href="{{ asset('css/metohub-pricing.css') }}?v={{ filemtime(public_path('css/metohub-pricing.css')) }}">
<section class="pricing-section" id="pricing" aria-labelledby="pricing-heading">
  <div class="pricing-header">
    <span class="eyebrow">Simple plans. Room to grow.</span>
    @php($pageHeadingTag = request()->routeIs('landing.pricing') ? 'h1' : 'h2')
    <{{ $pageHeadingTag }} id="pricing-heading">Your own website from ₹{{ number_format(config('saas.plans.base.monthly_price')) }}/month.</{{ $pageHeadingTag }}>
    <p>Build your brand’s store. Start with a {{ config('saas.trial_days') }}-day free trial.</p>
  </div>
  <div class="pricing-toggle-container">
    <div class="pricing-toggle" role="group" aria-label="Compare billing periods">
      <button type="button" class="toggle-btn toggle-active" id="btn-monthly" aria-pressed="true" aria-controls="pricingGrid">Monthly</button>
      <button type="button" class="toggle-btn" id="btn-yearly" aria-pressed="false" aria-controls="pricingGrid">Yearly @if(config('saas.annual_months') < 12)<span class="save-badge">{{ 12 - config('saas.annual_months') }} month{{ 12 - config('saas.annual_months') === 1 ? '' : 's' }} free</span>@endif</button>
    </div>
  </div>
  <div class="pricing-grid" id="pricingGrid">
    @foreach(config('saas.plans') as $id => $plan)
    <div class="pricing-card {{ $id === 'pro' ? 'featured' : '' }}">
      @if($id === 'pro')<div class="featured-badge">RECOMMENDED FOR GROWING BRANDS</div>@endif
      <div class="card-top"><h3>{{ $plan['name'] }}</h3><p>{{ ['base' => 'Start your own online store.', 'pro' => 'Your own domain. Your brand on every link.', 'enterprise' => 'Pro, plus additional support.'][$id] ?? $plan['description'] }}</p></div>
      <div class="price-show-monthly"><div class="price-monthly">₹{{ number_format($plan['monthly_price']) }} <span>/month</span></div><p class="billing-info">Billed monthly after your trial</p></div>
      <div class="price-show-yearly" hidden><div class="price-monthly">₹{{ number_format($plan['monthly_price'] * config('saas.annual_months')) }} <span>/year</span></div><p class="billing-info">Billed annually after your trial</p></div>
      <div class="divider"><span>What's included</span></div>
      @php($features = match($id) {
        'base' => ['Your MetoHub store link', 'Themes, logo and brand colors', 'Products, collections, variants and stock', 'Order dashboard, invoices and packing labels', 'Discount codes and bundles'],
        'pro' => ['Everything in Base', 'Connect your own domain, like yourbrand.in', 'Advanced analytics', 'Remove MetoHub footer branding'],
        default => ['Everything in Pro', 'Additional support offering'],
      })
      <ul class="feature-list">@foreach($features as $feature)<li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m7 12 3 3 7-7"/></svg><span>{{ $feature }}</span></li>@endforeach</ul>
      @if($id === 'pro')<p class="pricing-domain-note">Connect a domain you own. Domain purchase is separate.</p>@endif
      <a href="{{ route('landing', ['get_started' => 1, 'plan' => $id]) }}" class="btn-pricing {{ $id === 'pro' ? 'btn-solid-green' : 'btn-outline-green' }}" @if(request()->routeIs('landing')) data-start="" @endif data-plan="{{ $id }}">Start {{ config('saas.trial_days') }}-day free trial</a>
      @include('landing.partials.trial-reassurance')
    </div>
    @endforeach
  </div>
  <p class="pricing-footnote">Prices shown are for monthly subscriptions or annual subscriptions as selected. Choose your paid plan and billing period in your dashboard after the trial. Domain registration is separate.</p>
  <div class="trust-bar">
    @foreach(['No coding needed' => 'Create and manage your own store', 'Mobile first' => 'A storefront for every screen', 'Your brand' => 'Your logo, colors and products', 'Order tools' => 'Invoices and packing labels'] as $title => $detail)
    <div class="trust-item"><span class="trust-icon" aria-hidden="true">✓</span><div class="trust-text"><strong>{{ $title }}</strong><p>{{ $detail }}</p></div></div>
    @endforeach
  </div>
</section>
@if(request()->routeIs('landing.pricing'))
<section class="pricing-faq container-page" id="faq" aria-labelledby="pricing-faq-heading">
  <h2 id="pricing-faq-heading">Good to know.</h2>
  @foreach(app(\App\Services\MarketingContent::class)->faqs() as $faq)
  <details><summary><h3>{{ $faq['question'] }}</h3></summary><p>{{ $faq['answer'] }}</p></details>
  @endforeach
  <p>Need a hand choosing? <a href="{{ route('landing.contact') }}">Contact the MetoHub team</a>.</p>
</section>
@endif
<script src="{{ asset('js/metohub-pricing.js') }}?v={{ filemtime(public_path('js/metohub-pricing.js')) }}" defer></script>
