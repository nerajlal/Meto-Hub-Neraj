@php($seo = app(\App\Services\StorefrontSeo::class)->data($currentTenant, get_defined_vars()))
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
<meta name="robots" content="{{ $seo['noindex'] ? 'noindex, follow' : 'index, follow' }}">
<link rel="canonical" href="{{ $seo['canonical'] }}">
<meta property="og:type" content="{{ $seo['type'] }}">
<meta property="og:site_name" content="{{ $currentTenant->name }}">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:url" content="{{ $seo['canonical'] }}">
<meta name="twitter:card" content="{{ $seo['image'] ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $seo['title'] }}">
<meta name="twitter:description" content="{{ $seo['description'] }}">
@if($seo['image'])
<meta property="og:image" content="{{ $seo['image'] }}">
<meta name="twitter:image" content="{{ $seo['image'] }}">
@endif
<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org', '@graph'=>$seo['graph']], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
