@extends('layouts.admin')
@section('title', 'Billing & Subscription')
@section('content')
@if($billingOnly ?? false)
<div class="alert alert-warning m-3" role="status">
    <strong>Your subscription has ended.</strong> Renew your plan to unlock the dashboard.
    @php($graceEnds = app(\App\Services\PlanAccess::class)->storefrontGraceEndsAt($tenant))
    @if(now()->lt($graceEnds))
        Your storefront stays open until {{ $graceEnds->format('d M Y, g:i a') }} ({{ config('app.timezone') }}), including customer checkout.
    @else
        Your storefront is temporarily unavailable until a paid subscription is confirmed.
    @endif
</div>
@endif

<div class="container-fluid py-4">
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-4"><div><h1 class="h3 fw-bold">Billing & Subscription</h1><p class="text-muted mb-0">Manage your plan, renewal status and payment records.</p></div>@if($subscription)<form method="POST" action="{{ route('admin.billing.refresh') }}">@csrf<button class="btn btn-outline-primary">Refresh provider status</button></form>@endif</div>
    @if(!$billingConfigured)
    <div class="alert alert-warning" role="status">Online subscription checkout is not configured yet. The platform administrator must add Razorpay credentials and a webhook secret on the server.</div>
    @else
    <p class="small text-muted"><span class="badge {{ $billingMode === 'Test mode' ? 'bg-warning text-dark' : 'bg-primary' }}">Razorpay · {{ $billingMode }}</span>@if($billingMode === 'Test mode') Test transactions do not collect live money.@endif</p>
    @endif
    <div id="billing-feedback" class="alert alert-info" role="status" aria-live="polite" tabindex="-1" hidden></div>
    @if($canResumeCheckout)
    <div class="alert alert-info"><strong>Your checkout is not finished.</strong> Continue the existing {{ config('saas.plans.'.$subscription->plan.'.name') }} {{ $subscription->cycle }} subscription without creating another.
      <button type="button" class="btn btn-primary btn-sm ms-2 resume-checkout" data-tier="{{ $subscription->plan }}" data-cycle="{{ $subscription->cycle }}">Resume checkout</button>
    </div>
    @elseif($subscription && !$canChoosePlan)
    <div class="alert alert-light border">@if(!$subscription->gateway_id)A checkout attempt needs provider reconciliation. Use “Refresh provider status” before retrying. Contact support if it remains uncertain.@else Manage your existing subscription above. Refresh provider status if a payment is pending. A second subscription cannot be started while this subscription or its paid period remains active.@endif</div>
    @endif
    <p class="small text-muted">Razorpay subscriptions renew automatically according to the selected billing cycle and your authorization. Cancellation preserves any verified paid time. Customer store orders remain cash on delivery.</p>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="card mb-4"><div class="card-body"><div class="row g-3">
        <div class="col-md-3"><small class="text-muted">Current store plan</small><h2 class="h5 mt-1">{{ config('saas.plans.'.$tenant->plan.'.name', 'Legacy plan: '.ucfirst($tenant->plan ?: 'Unassigned')) }}</h2></div>
        <div class="col-md-3"><small class="text-muted">Subscription status</small><p class="mb-0 mt-1">{{ ucfirst($subscription?->status ?: $tenant->subscription_status ?: 'Trial / not subscribed') }}</p></div>
        <div class="col-md-3"><small class="text-muted">Billing cycle</small><p class="mb-0 mt-1">{{ $subscription?->cycle ? ucfirst($subscription->cycle) : 'Not recorded' }}</p></div>
        <div class="col-md-3"><small class="text-muted">Next payment date</small><p class="mb-0 mt-1">{{ $subscription?->cancel_at_cycle_end ? 'Cancellation scheduled' : ($subscription?->next_charge_at?->format('d M Y, H:i') ?: 'Not recorded') }}</p></div>
        <div class="col-md-6"><small class="text-muted">Agreed subscription amount</small><p>{{ $subscription?->amount !== null ? $subscription->currency.' '.number_format($subscription->amount / 100, 2).' / '.$subscription->cycle : 'Not recorded' }}</p></div><div class="col-md-6"><small class="text-muted">Verified paid period ends</small><p>{{ $subscription?->paid_count > 0 ? ($subscription->paid_through?->format('d M Y, H:i') ?: 'Not recorded') : 'No paid period recorded' }}</p></div>
        <div class="col-md-6"><small class="text-muted">Last provider synchronization</small><p>{{ $subscription?->synced_at?->format('d M Y, H:i') ?: 'Not synchronized yet' }}</p></div>
    </div>
    @if(!$subscription?->synced_at && $tenant->razorpay_subscription_id)<p class="text-muted small">Historical price not recorded. Refresh provider status to import the subscription’s original terms.</p>@endif
    @if($subscription?->gateway_id && !$subscription->cancel_at_cycle_end && !in_array($subscription->status, ['cancelled', 'completed', 'expired']))<form method="POST" action="{{ route('admin.billing.cancel') }}" onsubmit="return confirm('Cancel this subscription? Any remaining verified paid period will remain available.');">@csrf<button class="btn btn-outline-danger btn-sm">Cancel subscription</button></form>@endif
    </div></div>
    @if($subscription?->invoices_synced_at)
    <div class="card mb-4"><div class="card-body"><h2 class="h5">Outstanding invoiced amount</h2>@forelse($subscription->invoices()->whereIn('status', ['issued', 'partially_paid'])->get()->groupBy('currency') as $currency => $invoices)<p>{{ $currency }} {{ number_format($invoices->sum('amount_due') / 100, 2) }}</p>@empty<p class="mb-0">0.00</p>@endforelse<small class="text-muted">Based on provider invoices last reconciled {{ $subscription->invoices_synced_at->format('d M Y, H:i') }}.</small></div></div>
    @endif
    @include('super_admin.partials.payment_history', ['receiptRoutes' => true])
    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3"><h2 class="h4 mb-0">Available plans</h2><label class="d-flex align-items-center gap-2">Billing cycle<select id="billing-cycle" class="form-select"><option value="monthly">Monthly</option><option value="yearly">Yearly · 1 month free</option></select></label></div>
    <div class="row g-3">@foreach(config('saas.plans') as $id => $plan)<div class="col-lg-4"><div class="card h-100"><div class="card-body d-flex flex-column"><h3 class="h5">{{ $plan['name'] }}</h3><p class="fs-4">₹{{ number_format($plan['monthly_price']) }}<small class="fs-6 text-muted"> / month</small></p><p>₹{{ number_format($plan['monthly_price'] * config('saas.annual_months')) }} / year</p><p class="small text-muted">{{ $plan['description'] }}</p><ul class="small ps-3">@foreach($plan['features'] as $feature)<li class="mb-2">{{ $feature }}</li>@endforeach</ul><button type="button" class="btn btn-primary mt-auto subscribe-btn" data-tier="{{ $id }}" @disabled(!$canChoosePlan)>Choose {{ $plan['name'] }}</button></div></div></div>@endforeach</div>
    <p class="small text-muted mt-3">Existing subscriptions must be cancelled before a new one can be created. A new plan activates only after the provider confirms a paid period. Existing paid time is preserved.</p>
</div>
@endsection
@push('scripts')
<script id="billing-config" type="application/json">{!! json_encode([
    'subscribe' => route('admin.billing.subscribe'), 'verify' => route('admin.billing.verify'), 'status' => route('admin.billing.status'),
    'csrf' => csrf_token(), 'configured' => $billingConfigured, 'prefill' => ['name' => auth()->user()->name, 'email' => auth()->user()->email],
], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) !!}</script>
@if($billingConfigured)<script src="https://checkout.razorpay.com/v1/checkout.js"></script>@endif
<script src="{{ asset('js/metohub-billing.js') }}?v={{ filemtime(public_path('js/metohub-billing.js')) }}" defer></script>
@endpush
