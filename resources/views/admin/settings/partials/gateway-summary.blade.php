<summary class="gateway-summary">
    <span class="d-flex align-items-center gap-3"><span class="gateway-summary-icon"><i class="fa-solid fa-wallet" aria-hidden="true"></i></span><span class="fw-semibold">{{ $gatewayName }} Payments</span></span>
    <span class="d-flex align-items-center gap-3"><span class="badge {{ $paymentSetting->{$gateway.'_enabled'} ? 'bg-success-subtle text-success' : 'bg-light text-secondary' }}">{{ $paymentSetting->{$gateway.'_enabled'} ? 'Enabled' : 'Not enabled' }}</span><i class="fa-solid fa-chevron-down gateway-chevron" aria-hidden="true"></i></span>
</summary>
