<footer data-marketing-footer class="border-t border-border py-12">
<div class="container-page flex flex-col justify-between gap-8 md:flex-row md:items-center">
<div>
<a class="flex items-center gap-2 text-lg font-semibold tracking-tight text-foreground" href="{{ route('landing') }}">
<span class="grid h-8 w-8 place-items-center rounded-md bg-primary font-serif text-xl italic text-primary-foreground">M</span>MetoHub</a>
<p class="mt-3 max-w-xs text-sm text-muted-foreground">Beautiful online stores for small businesses and independent sellers.</p>
<address class="home-footer-contact">
<a href="mailto:{{ config('marketing.contact.email') }}">{{ config('marketing.contact.email') }}</a>
<a href="tel:{{ config('marketing.contact.phone_uri') }}">{{ config('marketing.contact.phone') }}</a>
<span>{{ config('marketing.contact.location') }}</span>
</address>
</div>
<nav class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-muted-foreground" aria-label="Footer navigation">
<a href="{{ route('landing.features') }}" class="hover:text-foreground">Features</a>
<a href="{{ route('landing.themes') }}" class="hover:text-foreground">Themes</a>
<a href="{{ route('landing.pricing') }}" class="hover:text-foreground">Pricing</a>
<a href="{{ route('landing') }}#faq" class="hover:text-foreground">Help</a>
<a href="{{ route('landing.about') }}" class="hover:text-foreground">About us</a>
<a href="{{ route('landing.blogs') }}" class="hover:text-foreground">Blog</a>
<a href="{{ route('landing.contact') }}" class="hover:text-foreground">Contact us</a>
</nav>
</div>
<div class="container-page"><div class="home-footer-legal">
<span>© {{ date('Y') }} MetoHub. All rights reserved.</span>
<nav aria-label="Legal policies">
<a href="{{ route('landing.privacy') }}">Privacy Policy</a>
<a href="{{ route('landing.terms') }}">Terms of Service</a>
<a href="{{ route('landing.refund') }}">Refund &amp; Cancellation Policy</a>
<a href="{{ route('landing.security') }}">Security</a>
</nav>
</div>
</div>
</footer>
