@php
 $integrationSettings = app(\App\Services\StorefrontTracking::class)->settings($currentTenant);
 $integrationPage = \Illuminate\Support\Str::afterLast(request()->route()?->getName() ?? '', '.');
 $integrationHome = (request()->routeIs('storefront.home') && str_ends_with(request()->getPathInfo(), '/')) || (request()->path() === '/' && request()->getHost() === $currentTenant->domain);
@endphp
@if($integrationHome && !request()->has('preview') && $integrationSettings?->gsc_enabled && $integrationSettings->gsc_token)
<meta name="google-site-verification" content="{{ $integrationSettings->gsc_token }}">
@endif
