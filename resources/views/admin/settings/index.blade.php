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
        <a class="settings-row {{ !empty($row['danger']) ? 'settings-row-danger' : '' }}" 
           href="{{ $destination }}" 
           @if(!empty($row['modal'])) data-bs-toggle="modal" data-bs-target="#{{ $row['modal'] }}" @endif
           @if(!empty($row['onclick'])) onclick="{{ $row['onclick'] }}" @endif>
            <span class="settings-row-icon" aria-hidden="true"><i class="fa-solid fa-{{ $row['icon'] }}"></i></span>
            <span class="settings-row-copy">
                <strong>{{ $row['label'] }}</strong>
                <small>{{ $row['description'] }}</small>
            </span>
            @if(!empty($row['badge']))<span class="settings-row-badge {{ !empty($row['danger']) && $row['badge'] === 'Active' ? 'bg-danger text-white border-danger' : '' }}">{{ $row['badge'] }}</span>@endif
            <span class="settings-chevron" aria-hidden="true">›</span>
        </a>
        @endforeach
    </div>@if($heading === 'Search & marketing')<p class="settings-group-note">Configured means setup is saved. It does not confirm provider verification or event delivery. Tracking also depends on consent, installation mode and domain eligibility.</p>@endif</section>
    @endforeach
    <p class="settings-overview-footnote">Your account: {{ auth()->user()->email }} · Need help? <a href="mailto:{{ config('marketing.contact.email') }}">{{ config('marketing.contact.email') }}</a></p>
</div>

<!-- Modal: Store Maintenance -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill bg-danger-subtle text-danger p-2"><i class="fa-solid fa-store fa-fw"></i></span>
                    <h5 class="modal-title fw-bold fs-6 mb-0" id="maintenanceModalLabel">Store Maintenance Mode</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.settings.maintenance.update', ['tenant' => $tenant->id]) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-secondary small mb-3">When enabled, customers visiting your storefront will see a friendly "Store Under Maintenance" notice. As an admin, you can still view your dashboard and browse the store normally.</p>
                    
                    <div class="form-check form-switch mb-3 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between">
                        <label class="form-check-label fw-semibold cursor-pointer mb-0" for="maintenance_mode_switch">
                            Enable Maintenance Page
                        </label>
                        <input type="hidden" name="maintenance_mode" value="0">
                        <input class="form-check-input ms-0 fs-5" type="checkbox" role="switch" id="maintenance_mode_switch" name="maintenance_mode" value="1" {{ $tenant->maintenance_mode ? 'checked' : '' }}>
                    </div>
                    
                    <div class="mb-2">
                        <label for="maintenance_message" class="form-label small fw-semibold text-secondary">Customer notice message (optional)</label>
                        <textarea class="form-control form-control-sm" id="maintenance_message" name="maintenance_message" rows="3" placeholder="We are currently upgrading our store to serve you better. We'll be back shortly!">{{ old('maintenance_message', $tenant->maintenance_message) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-semibold">Save Maintenance Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Delete Site -->
<div class="modal fade" id="deleteSiteModal" tabindex="-1" aria-labelledby="deleteSiteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <h5 class="modal-title fw-bold fs-6 mb-0" id="deleteSiteModalLabel">Permanently Delete Site</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.settings.delete-site', ['tenant' => $tenant->id]) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-danger py-2 px-3 small mb-3">
                        <strong>Warning:</strong> This action is irreversible. All products, categories, orders, customer records, and settings for <strong>{{ $tenant->name }}</strong> will be deactivated and removed.
                    </div>
                    <p class="small text-secondary mb-3">To proceed, please type your exact store name <code>{{ $tenant->name }}</code> below to confirm:</p>
                    
                    <div class="mb-3">
                        <label for="confirm_store_name" class="form-label small fw-semibold">Confirm store name</label>
                        <input type="text" class="form-control" id="confirm_store_name" name="confirm_store_name" required placeholder="{{ $tenant->name }}" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold">Delete Site Permanently</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Account Deletion Request -->
<div class="modal fade" id="accountDeletionModal" tabindex="-1" aria-labelledby="accountDeletionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill bg-danger-subtle text-danger p-2"><i class="fa-solid fa-user-xmark fa-fw"></i></span>
                    <h5 class="modal-title fw-bold fs-6 mb-0" id="accountDeletionModalLabel">Request Account Deletion</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.settings.request-deletion', ['tenant' => $tenant->id]) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    @if($tenant->deletion_requested_at)
                        <div class="alert alert-warning py-2 px-3 small mb-3">
                            <i class="fa-solid fa-clock-rotate-left me-1"></i> You have already submitted a deletion request on {{ $tenant->deletion_requested_at->format('d M Y, h:i A') }}. Our support team is currently reviewing it.
                        </div>
                    @else
                        <p class="text-secondary small mb-3">Submitting this request will notify the MetoHub administrator team to permanently close your merchant account and cancel any active recurring subscriptions.</p>
                    @endif

                    <div class="mb-3">
                        <label for="deletion_reason" class="form-label small fw-semibold text-secondary">Why are you leaving? (required)</label>
                        <textarea class="form-control form-control-sm" id="deletion_reason" name="reason" rows="3" required placeholder="Please let us know how we could have improved or why you are deleting the account...">{{ old('reason', $tenant->deletion_reason) }}</textarea>
                    </div>

                    <div class="mb-2">
                        <label for="confirm_email" class="form-label small fw-semibold text-secondary">Confirm your account email (<code>{{ auth()->user()->email }}</code>)</label>
                        <input type="email" class="form-control form-control-sm" id="confirm_email" name="confirm_email" required placeholder="{{ auth()->user()->email }}">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top px-4 py-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-semibold">Submit Deletion Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
