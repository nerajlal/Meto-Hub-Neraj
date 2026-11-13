<div class="table-responsive">
<table class="table table-hover align-middle mb-0 sa-store-table">
    <thead class="table-light small"><tr><th>Store / owner</th><th>Plan / theme</th><th>Subscription</th><th>Trial</th><th>Registered</th><th>Details</th></tr></thead>
    <tbody>
    @forelse($tenants as $store)
        @php
            $state = $overview->describe($store);
        @endphp
        <tr>
            <td><a href="{{ route('super_admin.tenants.show', $store->id) }}" class="fw-semibold text-decoration-none">{{ $store->name }}</a><span class="small text-muted d-block">#{{ $store->id }} · {{ $store->slug }}</span>@if($store->is_frozen)<span class="badge text-bg-danger">Frozen</span>@endif
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
            <td><div class="d-flex flex-column gap-2 align-items-start"><a class="btn btn-sm btn-outline-primary" href="{{ route('super_admin.tenants.show', $store->id) }}">View store</a><a class="btn btn-sm btn-primary" href="{{ route('admin.dashboard', ['tenant' => $store->id]) }}" target="_blank" rel="noopener noreferrer" aria-label="Open {{ $store->name }} admin in a new tab">Open store admin ↗</a></div>
                <details class="mt-2"><summary class="small text-secondary" style="cursor:pointer">Manage store</summary>
                    <div class="border rounded p-2 mt-2" style="min-width:230px">
                        <form method="POST" action="{{ route('super_admin.tenants.freeze', $store->id) }}" class="mb-3">@csrf @method('PATCH')
                            <input type="hidden" name="frozen" value="{{ $store->is_frozen ? '0' : '1' }}">
                            @unless($store->is_frozen)<label class="small" for="freeze-reason-{{ $store->id }}">Freeze reason (internal, optional)</label><input class="form-control form-control-sm mb-2" id="freeze-reason-{{ $store->id }}" name="reason" maxlength="1000">@endunless
                            <p class="small text-muted">{{ $store->is_frozen ? 'Restore access under normal billing rules.' : 'Block storefront and merchant admin. Data stays saved. Gateway billing continues.' }}</p>
                            <button class="btn btn-sm btn-outline-warning">{{ $store->is_frozen ? 'Unfreeze store' : 'Freeze store' }}</button>
                        </form>
                        <form method="POST" action="{{ route('super_admin.tenants.delete', $store->id) }}">@csrf @method('DELETE')
                            <p class="small text-muted">Delete from active stores. Historical data is retained. Cancel gateway billing first.</p>
                            <label class="small" for="delete-store-{{ $store->id }}">Type {{ $store->name }} to confirm</label><input class="form-control form-control-sm mb-2" id="delete-store-{{ $store->id }}" name="confirm_name" required autocomplete="off">
                            <button class="btn btn-sm btn-outline-danger">Delete store</button>
                        </form>
                    </div>
                </details>
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted py-5">No stores match these filters.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
