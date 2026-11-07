@php
    $onboarding = config('payment_gateway_onboarding.'.$gateway);
@endphp
<div class="rounded-3 p-3 mb-4" style="background:#f5f3ff;border:1px solid #ede9fe;" data-gateway-onboarding="{{ $gateway }}">
    <h3 class="small fw-bold mb-2">Don’t have a {{ $onboarding['name'] }} gateway account?</h3>
    <p class="small text-muted mb-3">{{ $onboarding['guidance'] }}</p>
    <a href="{{ $onboarding['signup'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary" aria-label="Create {{ $onboarding['name'] }} account (opens in a new tab)">Create {{ $onboarding['name'] }} account <span aria-hidden="true">↗</span></a>
    <p class="small mt-2 mb-0">Already registered? <a href="{{ $onboarding['dashboard'] }}" target="_blank" rel="noopener noreferrer">Open {{ $onboarding['name'] }} dashboard <span aria-hidden="true">↗</span></a></p>
</div>
