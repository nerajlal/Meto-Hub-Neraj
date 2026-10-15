<div class="card border mb-4">
    <div class="card-header bg-white p-3 d-flex flex-wrap gap-2 justify-content-between"><h2 class="h5 mb-0 fw-bold text-dark">Subscription & Trial Details</h2>@if($subscription)<form method="POST" action="{{ route('super_admin.tenants.billing.refresh', $tenant->id) }}">@csrf<button class="btn btn-sm btn-outline-primary">Refresh provider records</button></form>@endif<span class="badge text-bg-{{ $billing['badge'] }}">{{ $billing['label'] }}</span></div>
    <div class="card-body"><dl class="row gy-3 mb-0">
        <div class="col-md-4"><dt class="small text-muted">Plan</dt><dd>{{ $billing['plan_name'] }}@if($billing['monthly_price'] !== null)<span class="small text-muted d-block">Catalog price: INR {{ number_format($billing['monthly_price']) }}/month</span>@else<span class="small text-muted d-block">Legacy or unrecognized plan</span>@endif</dd></div>
        <div class="col-md-4"><dt class="small text-muted">Provider status recorded</dt><dd>{{ $billing['raw_status'] }}</dd></div>
        <div class="col-md-4"><dt class="small text-muted">Trial</dt><dd>{{ $billing['trial_label'] }}</dd></div>
        <div class="col-md-4"><dt class="small text-muted">Trial starts</dt><dd>{{ $tenant->created_at->format('d M Y, H:i') }}</dd></div>
        <div class="col-md-4"><dt class="small text-muted">Trial ends</dt><dd>{{ $billing['trial_ends_at']->format('d M Y, H:i') }}</dd></div>
        <div class="col-md-4"><dt class="small text-muted">Owner email verification</dt><dd>{{ !$tenant->admin ? 'No store owner assigned' : ($tenant->admin->email_verified_at ? 'Verified '.$tenant->admin->email_verified_at->format('d M Y') : 'Not verified') }}</dd></div>
        <div class="col-md-6"><dt class="small text-muted">Razorpay subscription reference</dt><dd class="sa-reference">{{ $tenant->razorpay_subscription_id ?: 'Not recorded' }}</dd></div>
        <div class="col-md-6"><dt class="small text-muted">Razorpay customer reference</dt><dd class="sa-reference">{{ $tenant->razorpay_customer_id ?: 'Not recorded' }}</dd></div>
        <div class="col-md-3"><dt class="small text-muted">Billing cycle</dt><dd>{{ $subscription?->cycle ? ucfirst($subscription->cycle) : 'Not recorded' }}</dd></div>
        <div class="col-md-3"><dt class="small text-muted">Next payment date</dt><dd>{{ $subscription?->next_charge_at?->format('d M Y, H:i') ?: 'Not recorded' }}</dd></div>
        <div class="col-md-3"><dt class="small text-muted">Paid period ends</dt><dd>{{ $subscription?->paid_count > 0 ? ($subscription->paid_through?->format('d M Y, H:i') ?: 'Not recorded') : 'Not recorded' }}</dd></div>
        <div class="col-md-3"><dt class="small text-muted">Outstanding invoiced amount</dt><dd>@if($subscription?->invoices_synced_at)@forelse($subscription->invoices()->whereIn('status', ['issued', 'partially_paid'])->get()->groupBy('currency') as $currency => $invoices){{ $currency }} {{ number_format($invoices->sum('amount_due') / 100, 2) }}@empty 0.00 @endforelse @else Not recorded @endif</dd></div>
        <div class="col-md-6"><dt class="small text-muted">Last provider synchronization</dt><dd>{{ $subscription?->synced_at?->format('d M Y, H:i') ?: 'Not recorded' }}</dd></div>
        <div class="col-12"><dt class="small text-muted">Storefront</dt><dd><a href="{{ $tenant->storefrontUrl() }}" target="_blank" rel="noopener">{{ $tenant->storefrontUrl() }}</a></dd></div>
    </dl><p class="small text-muted border-top pt-3 mt-3 mb-0">Trials use the platform’s {{ config('saas.trial_days') }}-day policy from registration. Plan prices are reference prices, not amounts paid or due. Provider synchronization supplies dates, invoices and payments. Historical records remain unknown until imported.</p></div>
</div>

@include('super_admin.partials.payment_history')
