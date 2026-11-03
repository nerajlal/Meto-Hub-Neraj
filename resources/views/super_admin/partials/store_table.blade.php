<div class="table-responsive">
<table class="table table-hover align-middle mb-0 sa-store-table">
    <thead class="table-light small"><tr><th>Store / owner</th><th>Plan / theme</th><th>Subscription</th><th>Trial</th><th>Registered</th><th>Details</th></tr></thead>
    <tbody>
    @forelse($tenants as $store)
        @php
            $state = $overview->describe($store);
        @endphp
        <tr>
            <td><a href="{{ route('super_admin.tenants.show', $store->id) }}" class="fw-semibold text-decoration-none">{{ $store->name }}</a><span class="small text-muted d-block">#{{ $store->id }} · {{ $store->slug }}</span>
                @if($store->admin)<span class="small d-block">{{ $store->admin->name }}</span><span class="small text-muted d-block">{{ $store->admin->email }}</span><span class="badge {{ $store->admin->email_verified_at ? 'text-bg-light' : 'text-bg-warning' }}">{{ $store->admin->email_verified_at ? 'Email verified' : 'Email unverified' }}</span>@else<span class="small text-danger d-block">No store owner assigned</span>@endif
            </td>
            <td><span class="fw-semibold">{{ $state['plan_name'] }}</span><span class="small text-muted d-block">{{ $state['theme_name'] }}</span></td>
            <td><span class="badge text-bg-{{ $state['badge'] }}">{{ $state['label'] }}</span><span class="small text-muted d-block mt-1">Provider: {{ $state['raw_status'] }}</span>@if($store->razorpay_subscription_id)<code class="small sa-reference">{{ $store->razorpay_subscription_id }}</code>@endif</td>
            <td><span class="small">{{ $state['trial_label'] }}</span><span class="small text-muted d-block">Ends {{ $state['trial_ends_at']->format('d M Y, H:i') }} ({{ config('app.timezone') }})</span>
                <details class="mt-2"><summary class="small text-primary">Edit trial</summary>
                    <form method="POST" action="{{ route('super_admin.tenants.trial.update', $store->id) }}" class="mt-2" style="min-width:220px">@csrf @method('PATCH')
                        <label class="small" for="trial-{{ $store->id }}">Trial ends ({{ config('app.timezone') }})</label>
                        <input id="trial-{{ $store->id }}" type="datetime-local" name="trial_ends_at" value="{{ $state['trial_ends_at']->format('Y-m-d\TH:i') }}" class="form-control form-control-sm mb-2" required>
                        <label class="small" for="trial-reason-{{ $store->id }}">Reason</label><input id="trial-reason-{{ $store->id }}" name="reason" maxlength="500" class="form-control form-control-sm mb-2" required>
                        <button class="btn btn-sm btn-primary">Save trial</button>
                    </form>
                </details></td>
            <td class="small">{{ $store->created_at->format('d M Y') }}</td>
            <td><a class="btn btn-sm btn-outline-primary" href="{{ route('super_admin.tenants.show', $store->id) }}">View store</a></td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted py-5">No stores match these filters.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
