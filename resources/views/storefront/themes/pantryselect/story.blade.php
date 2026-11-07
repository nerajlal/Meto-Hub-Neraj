@php
    $storyTitle = trim((string) $currentTenant->about_title);
    if ($storyTitle === '' || ($storyTitle === 'Cultivating Health & Happiness' && blank($currentTenant->about_text))) $storyTitle = 'The story behind '.$currentTenant->name;
    $storyImage = $currentTenant->about_image ?: $currentTenant->logo;
@endphp
<section class="ps-story" aria-labelledby="ps-story-title">
    <div class="ps-story-visual {{ $currentTenant->about_image ? 'has-story-image' : 'has-brand-mark' }}">
        @if($storyImage)<img src="{{ app(\App\Services\StorefrontProduct::class)->imageUrl($storyImage) }}" alt="{{ $currentTenant->name }} store story" loading="lazy">@else<span class="ps-story-monogram" aria-hidden="true">{{ mb_substr($currentTenant->name, 0, 1) }}</span>@endif
        <span class="ps-story-quote" aria-hidden="true">“</span>
    </div>
    <div class="ps-story-copy">
        <span class="sf-kicker">OUR STORY · {{ $currentTenant->name }}</span>
        <h2 id="ps-story-title">{{ $storyTitle }}</h2>
        <p>{{ filled($currentTenant->about_text) ? Str::limit(strip_tags($currentTenant->about_text), 650) : 'Get to know '.$currentTenant->name.' and explore what’s in store. Visit our About page or get in touch to learn more.' }}</p>
        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.about') }}">Discover our story <span aria-hidden="true">↗</span></a>
    </div>
</section>
