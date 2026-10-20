<a class="marketing-skip-link" href="#main-content">Skip to content</a>
<header data-marketing-header class="sticky top-0 z-50 border-b border-border/70 bg-background/85 backdrop-blur-md">
<div class="container-page flex h-16 items-center justify-between gap-4">
<a class="flex items-center gap-2 text-lg font-semibold tracking-tight text-foreground" href="{{ route('landing') }}">
<span class="grid h-8 w-8 place-items-center rounded-md bg-primary font-serif text-xl italic text-primary-foreground">M</span>MetoHub</a>
<nav class="hidden items-center gap-8 text-sm text-muted-foreground lg:flex">
<a href="{{ route('landing') }}#demos" class="transition hover:text-foreground">Explore stores</a>
<a href="{{ route('landing') }}#how-it-works" class="transition hover:text-foreground">How it works</a>
<a href="{{ route('landing.pricing') }}" class="transition hover:text-foreground">Pricing</a>
<a href="{{ route('landing') }}#faq" class="transition hover:text-foreground">FAQ</a>
</nav>
<div class="flex items-center gap-2">
<a class="hidden px-3 text-sm font-medium text-foreground hover:text-bronze sm:inline" href="{{ route('admin.common.login') }}">Log in</a>
<a class="btn-primary !px-4 !py-2.5 text-sm" href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}" @if(request()->routeIs('landing')) data-start="" data-plan="base" @endif>Get started</a>
<button class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-border lg:hidden" aria-expanded="false" aria-label="Open menu" id="menuBtn" aria-controls="mm">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-4 w-4" aria-hidden="true">
<path d="M4 5h16">
</path>
<path d="M4 12h16">
</path>
<path d="M4 19h16">
</path>
</svg>
</button>
</div>
</div>
<nav id="mm" class="mobile-menu" aria-label="Mobile navigation" hidden="">
<a href="{{ route('landing') }}#demos">Explore stores</a>
<a href="{{ route('landing') }}#how-it-works">How it works</a>
<a href="{{ route('landing.pricing') }}">Pricing</a>
<a href="{{ route('landing') }}#faq">FAQ</a>
<a href="{{ route('admin.common.login') }}">Log in</a>
</nav>
</header>
