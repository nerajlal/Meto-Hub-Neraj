<div class="card border p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3"><div><h2 class="h5 fw-bold mb-1">Subscription revenue & growth</h2><p class="small text-muted mb-0">Confirmed payment records only. Merchant sales are excluded.</p></div><a href="{{ route('super_admin.payments') }}" class="btn btn-outline-primary btn-sm">Payment history</a></div>
    <form method="GET" action="{{ route('super_admin.dashboard') }}" class="row g-2 align-items-end">
        <div class="col-sm-4"><label for="revenue-from" class="form-label small">From</label><input id="revenue-from" class="form-control" type="date" name="from" value="{{ $range['from'] }}" required></div>
        <div class="col-sm-4"><label for="revenue-to" class="form-label small">Through</label><input id="revenue-to" class="form-control" type="date" name="to" value="{{ $range['to'] }}" required></div>
        <div class="col-sm-4 d-flex gap-2"><button class="btn btn-primary">Apply dates</button><a href="{{ route('super_admin.dashboard') }}" class="btn btn-light border">Reset</a></div>
    </form>
    @if($errors->any())<p class="text-danger small mt-2 mb-0">{{ $errors->first() }}</p>@endif
    <div class="row g-3 mt-1">
        @foreach(['selected' => 'Selected payment dates', 'this_month' => 'This calendar month', 'last_month' => 'Last calendar month'] as $period => $label)
        <div class="col-lg-4"><div class="border rounded p-3 h-100"><h3 class="h6">{{ $label }}</h3>
            @forelse($analyticsReport[$period] as $money)
            <div class="mb-2"><strong class="fs-5">{{ $money['currency'] }} {{ number_format($money['net']/100, 2) }}</strong><div class="small text-muted">Gross {{ number_format($money['gross']/100, 2) }} · refunds {{ number_format($money['refunds']/100, 2) }}</div></div>
            @empty<p class="text-muted small mb-0">No confirmed payments in this period.</p>@endforelse
        </div></div>
        @endforeach
    </div>
    <p class="small text-muted mt-3 mb-0">Dates use {{ config('app.timezone') }}. Net collections deduct refunds recorded to date on payments captured in that period, before gateway fees and tax adjustments. This is not profit or a refund-date cash-flow report.</p>
    @if($analyticsReport['undated'])<p class="small text-warning mb-0">{{ $analyticsReport['undated'] }} confirmed payment records lack a paid date and are excluded from dated totals. Reconcile their billing records.</p>@endif
</div>
<div class="row g-3 mb-4">
    @foreach(['paid' => 'Stores with current paid access', 'trial' => 'Trial stores without paid history', 'overdue' => 'Paid coverage expired', 'renewing' => 'Renewals due within 7 days'] as $key => $label)
    <div class="col-6 col-xl-3"><div class="card border p-3 h-100"><span class="small text-muted">{{ $label }}</span><strong class="fs-2">{{ number_format($analyticsReport['totals'][$key]) }}</strong></div></div>
    @endforeach
</div>
<div class="row g-4 mb-4">
    <div class="col-lg-7"><div class="card border p-3 h-100"><h2 class="h5 fw-bold">Stores by plan and paid coverage</h2><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Plan</th><th>Total</th><th>Paid now</th><th>Trial</th><th>Other</th></tr></thead><tbody>
    @foreach($analyticsReport['plans'] as $key => $plan)<tr><td>{{ $plan['name'] }}</td><td>{{ $plan['total'] }}</td><td>{{ $plan['paid'] }}</td><td>{{ $plan['trial'] }}</td><td>{{ $plan['other'] }}</td></tr>@endforeach
    </tbody></table></div><p class="small text-muted mt-2 mb-0">Paid stores use the current subscription plan; other stores use their selected plan. “Other” includes expired and unconfirmed billing records.</p></div></div>
    <div class="col-lg-5"><div class="card border p-3 h-100"><h2 class="h5 fw-bold">Billing cycles & growth</h2>
        @foreach(['monthly' => 'Monthly paid stores', 'yearly' => 'Yearly paid stores', 'unknown' => 'Paid cycle needs review'] as $key => $label)<div class="d-flex justify-content-between border-bottom py-2 gap-2"><span>{{ $label }}</span><strong>{{ $analyticsReport['cycles'][$key] }}</strong></div>@endforeach
        <div class="d-flex justify-content-between border-bottom py-2 gap-2"><span>New stores in selected dates</span><strong>{{ $analyticsReport['new_stores'] }}</strong></div>
        <div class="d-flex justify-content-between py-2 gap-2"><span>Those stores paid at least once</span><strong>{{ $analyticsReport['converted'] }} ({{ $analyticsReport['conversion'] }}%)</strong></div>
        <p class="small text-muted mb-0">Signup cohort conversion as of now; stores still in trial may convert later. Coverage and cycle counts reflect now, independent of the revenue date filter.</p>
    </div></div>
</div>
<div class="card border p-3 mb-4"><div class="d-flex flex-wrap gap-2 justify-content-between"><h2 class="h5 fw-bold">Renewal follow-ups</h2><a href="{{ route('super_admin.subscriptions') }}">All subscriptions →</a></div>
    <p class="small text-muted">{{ $analyticsReport['totals']['admin_grace'] }} stores in admin grace · {{ $analyticsReport['totals']['storefront_grace'] }} in storefront grace (includes admin grace). {{ $analyticsReport['totals']['review'] }} active-status records lack confirmed current paid coverage.</p>
    @forelse($analyticsReport['overdue_invoices'] as $due)<p class="small mb-2"><strong>{{ $due->invoice_count }} overdue invoices · {{ $due->currency }} {{ number_format($due->due/100, 2) }} outstanding</strong></p>@empty<p class="small text-muted">No overdue invoices with a recorded due date.</p>@endforelse
    <p class="small text-muted">Expired coverage means renewal is needed for paid access; it does not by itself establish an unpaid invoice.</p>
    <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Store</th><th>Follow-up</th><th>Coverage end / next charge</th></tr></thead><tbody>
    @forelse($analyticsReport['followups'] as $item)<tr><td><a href="{{ route('super_admin.tenants.show', $item['tenant']->id) }}">{{ $item['tenant']->name }}</a></td><td>{{ $item['reason'] }}</td><td>{{ $item['date']->format('d M Y, H:i') }}</td></tr>@empty<tr><td colspan="3" class="text-muted">No expired paid coverage or upcoming automatic renewals recorded.</td></tr>@endforelse
    </tbody></table></div><p class="small text-muted mt-2 mb-0">Shows up to 12 earliest follow-ups. Cancelled subscriptions keep paid access until coverage ends; no automatic renewal is assumed.</p>
</div>
