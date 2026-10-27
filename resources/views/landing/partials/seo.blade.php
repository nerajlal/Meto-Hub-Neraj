@php
    $decode = fn ($value) => html_entity_decode(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $metadata = app(\App\Services\MarketingSeo::class)->metadata(request(), [
        'title' => $decode($__env->yieldContent('page_title')),
        'description' => $decode($__env->yieldContent('meta_description')),
        'image' => $decode($__env->yieldContent('meta_image')),
        'type' => $decode($__env->yieldContent('og_type')),
        'article' => request()->routeIs('landing.blog.show') ? ($blog ?? null) : null,
    ]);
@endphp
<title>{{ $metadata['title'] }}</title>
<meta name="description" content="{{ $metadata['description'] }}">
<meta name="robots" content="{{ $metadata['robots'] }}">
<link rel="canonical" href="{{ $metadata['url'] }}">
@if(config('search.indexable') && str_starts_with($metadata['robots'], 'index,'))
<link rel="describedby" href="{{ rtrim(config('app.url'), '/') }}/llms.txt" type="text/plain">
@endif
@if(config('marketing.seo.google_site_verification'))
<meta name="google-site-verification" content="{{ config('marketing.seo.google_site_verification') }}">
@endif
<meta name="theme-color" content="#244a3b">
<meta property="og:site_name" content="MetoHub">
<meta property="og:locale" content="en_IN">
<meta property="og:type" content="{{ $metadata['type'] }}">
@if($metadata['article'])
<meta name="author" content="{{ $blog['author_name'] }}">
@foreach(['datePublished' => 'article:published_time', 'dateModified' => 'article:modified_time'] as $field => $property)
@if(isset($metadata['article'][$field]))
<meta property="{{ $property }}" content="{{ $metadata['article'][$field] }}">
@endif
@endforeach
@endif
<meta property="og:title" content="{{ $metadata['title'] }}">
<meta property="og:description" content="{{ $metadata['description'] }}">
<meta property="og:url" content="{{ $metadata['url'] }}">
<meta property="og:image" content="{{ $metadata['image'] }}">
@if($metadata['image'] === rtrim(config('app.url'), '/').'/images/landing/social-card.png')
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@endif
<meta property="og:image:alt" content="{{ $metadata['title'] }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metadata['title'] }}">
<meta name="twitter:description" content="{{ $metadata['description'] }}">
<meta name="twitter:image" content="{{ $metadata['image'] }}">
<meta name="twitter:image:alt" content="{{ $metadata['title'] }}">
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v={{ filemtime(public_path('favicon-32.png')) }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192.png') }}?v={{ filemtime(public_path('favicon-192.png')) }}">
<link rel="apple-touch-icon" href="{{ asset('favicon-192.png') }}?v={{ filemtime(public_path('favicon-192.png')) }}">
<script type="application/ld+json">{!! json_encode($metadata['schema'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
