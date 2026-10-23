@extends('layouts.admin')
@section('title', 'Settings')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/metohub-settings.css') }}?v={{ filemtime(public_path('css/metohub-settings.css')) }}">
@endsection
@section('content')
<div class="settings-overview">
    <header class="settings-overview-heading"><p class="settings-eyebrow">YOUR STORE, YOUR WAY</p><h1>Settings</h1><p>Manage your storefront, account and preferences in one place.</p></header>
    <section class="settings-identity" aria-label="Store identity">
        <div class="settings-avatar">@if($tenant->logo)<img src="{{ Storage::url($tenant->logo) }}" alt="{{ $tenant->name }} logo" width="64" height="64">@else<span aria-hidden="true">{{ mb_substr($tenant->name, 0, 1) }}</span>@endif</div>
        <h2>{{ $tenant->name }}</h2><p>{{ auth()->user()->email }}</p>
    </section>
    <section class="settings-subscription" aria-labelledby="settings-subscription-title">
        <div class="settings-subscription-top"><span class="settings-status settings-status-{{ $statusTone }}">{{ $status }}</span>
            @if($paid)
                <p>@if($subscription?->paid_through)Paid access through {{ $subscription->paid_through->format('d M Y') }}.@elseYour recorded subscription currently provides paid access.@endif</p>
            @elseif($trial)
                <p>{{ (int) ceil(now()->diffInSeconds($trialEnds) / 86400) }} trial day(s) remaining · Ends {{ $trialEnds->format('d M Y, g:i a') }} ({{ config('app.timezone') }}).</p>
            @else
                <p>Open Billing to review your subscription and activate paid access.</p>
            @endif
        </div>
        <div class="settings-subscription-bottom"><span class="settings-row-icon" aria-hidden="true"><i class="fa-solid fa-star"></i></span><div class="settings-row-copy"><h2 id="settings-subscription-title">{{ $planName }}</h2><p>Manage your plan, payments and subscription.</p></div><a class="settings-primary" href="{{ route('admin.billing', ['tenant' => $tenant->id]) }}">{{ $paid ? 'Manage billing' : 'Choose a plan' }}</a></div>
        @if($subscription)<p class="settings-recorded-status">Last recorded provider status: {{ ucfirst($subscription->status) }}@if($subscription->synced_at) · Synced {{ $subscription->synced_at->format('d M Y, g:i a') }}@endif. Refresh from Billing for the latest status.</p>@endif
    </section>
    <section class="settings-group" aria-labelledby="settings-domain-heading"><h2 id="settings-domain-heading">Store address & domain</h2><div class="settings-group-card">
        <a class="settings-row" href="{{ $tenant->storefrontUrl() }}" target="_blank" rel="noopener noreferrer"><span class="settings-row-icon" aria-hidden="true"><i class="fa-solid fa-arrow-up-right-from-square"></i></span><span class="settings-row-copy"><strong>Visit your store</strong><small>{{ $tenant->storefrontUrl() }}</small></span><span class="settings-chevron" aria-hidden="true">↗</span><span class="visually-hidden"> (opens in a new tab)</span></a>
        <a class="settings-row" href="{{ route('admin.settings.domain', ['tenant' => $tenant->id]) }}"><span class="settings-row-icon" aria-hidden="true"><i class="fa-solid fa-globe"></i></span><span class="settings-row-copy"><strong>Custom domain</strong><small>{{ $tenant->domain ?: 'Connect a domain you own' }}</small></span>@if(!$access->allows($tenant, 'custom_domain'))<span class="settings-row-badge">Pro / Enterprise</span>@endif<span class="settings-chevron" aria-hidden="true">›</span></a>
    </div></section>
    @foreach($groups as $heading => $rows)
    <section class="settings-group" aria-labelledby="settings-group-{{ $loop->index }}"><h2 id="settings-group-{{ $loop->index }}">{{ $heading }}</h2><div class="settings-group-card">
        @foreach($rows as $row)
        @php($destination = isset($row['route']) ? route($row['route'], str_starts_with($row['route'], 'admin.') ? ['tenant' => $tenant->id] : []).(isset($row['fragment']) ? '#'.$row['fragment'] : '') : ($row['url'] ?? '#'))
        <a class="settings-row {{ !empty($row['danger']) ? 'settings-row-danger' : '' }}" href="{{ $destination }}" @if(!empty($row['onclick'])) onclick="{{ $row['onclick'] }}" @endif><span class="settings-row-icon" aria-hidden="true"><i class="fa-solid fa-{{ $row['icon'] }}"></i></span><span class="settings-row-copy"><strong>{{ $row['label'] }}</strong><small>{{ $row['description'] }}</small></span>@if(!empty($row['badge']))<span class="settings-row-badge">{{ $row['badge'] }}</span>@endif<span class="settings-chevron" aria-hidden="true">›</span></a>
        @endforeach
    </div>@if($heading === 'Search & marketing')<p class="settings-group-note">Configured means setup is saved. It does not confirm provider verification or event delivery. Tracking also depends on consent, installation mode and domain eligibility.</p>@endif</section>
    @endforeach
    <p class="settings-overview-footnote">Your account: {{ auth()->user()->email }} · Need help? <a href="mailto:{{ config('marketing.contact.email') }}">{{ config('marketing.contact.email') }}</a></p>
</div>
@endsection
