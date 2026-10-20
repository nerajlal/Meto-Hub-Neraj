<link rel="stylesheet" href="{{ asset('css/metohub-themes.css') }}?v={{ filemtime(public_path('css/metohub-themes.css')) }}">
<section class="public-themes" aria-labelledby="themes-heading">
  <div class="public-themes-intro">
    <span class="eyebrow">Your brand, your storefront</span>
    @php($headingTag = request()->routeIs('landing.themes') ? 'h1' : 'h2')
    <{{ $headingTag }} id="themes-heading">A different look.<br><em>The same possibilities.</em></{{ $headingTag }}>
    <p>Explore {{ count(config('storefront_themes')) }} customizable designs for clothing, grocery, gadgets, jewelry and more. Your products, brand colors and store tools work across every theme.</p>
    <a href="{{ route('landing.pricing') }}">From ₹{{ number_format(config('saas.plans.base.monthly_price')) }}/month · {{ config('saas.trial_days') }}-day free trial ↗</a>
  </div>
  <div class="public-themes-note">Layout illustrations below show each design’s style. Live previews use a demo store’s products; your own logo, colors and content make it yours.</div>
  <div class="public-themes-grid">
    @foreach($themes ?? config('storefront_themes') as $id => $theme)
    <article class="public-theme-card" data-public-theme="{{ $id }}" style="--theme-background:{{ $theme['background'] }}">
      <div class="public-theme-art"><img src="{{ asset('images/themes/'.$id.'.svg') }}" alt="{{ $theme['name'] }} layout illustration" width="640" height="400" loading="lazy" decoding="async"></div>
      <div class="public-theme-content">
        <span class="public-theme-edition">{{ $theme['version'] }}</span>
        <h2>{{ $theme['name'] }}</h2>
        <p>{{ $theme['description'] }}</p>
        <div class="public-theme-actions">
          @if(!empty($theme['preview_url']))
          <a class="public-theme-preview" href="{{ $theme['preview_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Preview {{ $theme['name'] }} in a new tab">Live preview ↗</a>
          @else
          <span class="public-theme-unavailable">Live demo coming soon</span>
          @endif
          <a class="public-theme-start" href="{{ route('landing', ['get_started' => 1, 'plan' => 'base', 'theme' => $id]) }}" aria-label="Start with {{ $theme['name'] }}">Start with this theme →</a>
        </div>
      </div>
    </article>
    @endforeach
  </div>
  <div class="public-themes-bottom"><h2>Make it feel like you.</h2><p>Customize your logo, brand colors and homepage content in your dashboard. You can change themes later.</p><a href="{{ route('landing.pricing') }}">Compare plans</a><a href="{{ route('landing.contact') }}">Ask us a question</a></div>
</section>
