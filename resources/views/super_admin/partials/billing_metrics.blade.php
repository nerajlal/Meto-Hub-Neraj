<div class="row g-3 mb-4">
    @foreach(['active' => ['Active subscriptions', 'success'], 'trial' => ['Stores in trial', 'info'], 'trial_expired' => ['Trial expired', 'secondary'], 'pending' => ['Payment pending', 'warning']] as $status => [$label, $color])
        <div class="col-6 col-xl-3"><a href="{{ route('super_admin.subscriptions', ['status' => $status]) }}" class="card h-100 p-3 text-decoration-none border">
            <span class="small text-muted">{{ $label }}</span><strong class="fs-2 text-{{ $color }}">{{ number_format($counts[$status]) }}</strong>
            <span class="small text-muted">View stores →</span>
        </a></div>
    @endforeach
</div>

<p class="small text-muted">Subscription statuses reflect local records. Trial dates use {{ config('app.timezone') }} time.</p>
