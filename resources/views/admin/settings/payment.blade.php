@extends('layouts.admin')

@section('title', 'Payment Settings')

@section('content')
<style>
.gateway-summary{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:20px 24px;cursor:pointer;list-style:none;color:#4c1d95}
.gateway-summary::-webkit-details-marker{display:none}
.gateway-summary-icon{display:grid;place-items:center;width:36px;height:36px;border-radius:10px;background:#f5f3ff;color:#7c3aed;flex-shrink:0}
.gateway-chevron{font-size:12px;transition:transform .2s}
.gateway-settings[open]>.gateway-summary .gateway-chevron{transform:rotate(180deg)}
.gateway-summary:focus-visible{outline:2px solid #7c3aed;outline-offset:-3px;border-radius:16px}
@media(max-width:575px){.gateway-summary{padding:16px;gap:8px;font-size:14px}.gateway-summary .badge{font-size:10px}}
</style>
<div class="container-fluid py-4" style="background: var(--sf-bg); min-height: 100vh;">

    <!-- Premium Header -->
    <div class="mb-4 mb-md-5 position-relative overflow-hidden p-3 p-md-4 rounded-4" style="background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%); border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: rgba(255, 255, 255, 0.15); filter: blur(80px); top: -100px; right: -50px; pointer-events: none;"></div>
        
        <div class="position-relative z-1">
            <span class="badge mb-2 px-3 py-1.5 rounded-pill uppercase tracking-wider text-white-50" style="background: rgba(255,255,255,0.08); font-size: 10px; font-weight: 600; letter-spacing: 1.2px; border: 1px solid rgba(255,255,255,0.12);">TRANSACTIONS</span>
            <h1 class="h2 text-white mb-2 fw-bold" style="font-family: 'Playfair Display', Georgia, serif; letter-spacing: -0.5px;">Payment Methods</h1>
            <p class="text-white-50 small mb-0" style="max-width: 600px; line-height: 1.6;">Configure the payment methods and online gateways available to your store customers at checkout.</p>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4 p-3 shadow-sm" role="alert" style="background: #ede9fe; border-left: 5px solid #8b5cf6 !important; color: #6d28d9;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle" style="color: #8b5cf6; font-size: 18px;"></i>
                <div>
                    <strong class="fw-semibold">Success!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-4 p-3 shadow-sm" role="alert">
            <strong>Please fix the errors below:</strong>
            <ul class="mb-0 mt-2 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($paymentIssues->isNotEmpty())
    <div class="alert alert-warning">
        <h2 class="h6">Customer payments needing attention</h2>
        <p class="small">Payments below must be checked and resolved before asking the customer to pay again. If stock changed, restore stock and let reconciliation retry; otherwise issue a refund from the payment provider. PhonePe payments under review require an explicit retry after checking that no refund was issued. Refunds and courier booking are not performed here.</p>
        <div class="table-responsive"><table class="table table-sm"><thead><tr><th>Checkout reference</th><th>Gateway</th><th>Amount</th><th>State</th><th>Provider payment</th><th>Review action</th></tr></thead><tbody>
        @foreach($paymentIssues as $issue)<tr><td>{{ $issue->id }}<br><small>{{ $issue->provider_order_id }}</small></td><td>{{ ucfirst($issue->gateway) }}</td><td>₹{{ number_format($issue->amount / 100, 2) }}</td><td>{{ $issue->status }}<br><small>{{ $issue->review_reason }}</small></td><td>{{ $issue->provider_payment_id ?: 'Check provider order history' }}</td><td>
            @if($issue->gateway === 'phonepe' && $issue->status === 'review')
                <form method="POST" action="{{ route('admin.settings.payment.retry', ['checkout' => $issue->id]) }}">@csrf
                    <label class="small"><input type="checkbox" name="confirm_not_refunded" value="1" required> I checked PhonePe: this payment has not been refunded.</label>
                    <button class="btn btn-sm btn-outline-primary mt-1" type="submit">Verify &amp; retry order</button>
                </form>
            @else — @endif
            </td></tr>@endforeach
        </tbody></table></div>
    </div>
    @endif
    <form action="{{ route('admin.settings.payment.update') }}" method="POST">
        @csrf

        <div class="row g-4">
            <!-- COD Gateway -->
            <div class="col-md-6 col-12">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-light rounded-3 text-secondary" style="font-size: 24px;">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold mb-1 text-primary" style="font-family: 'Playfair Display', serif;">Cash on Delivery (COD)</h4>
                                <p class="text-muted small mb-0">Allow customers to place orders with payment upon delivery.</p>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="cod_enabled" id="cod_enabled" value="1" {{ $paymentSetting->cod_enabled ? 'checked' : '' }} style="width: 48px; height: 24px; cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Checkout Gateway -->
            <div class="col-md-6 col-12">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3 text-success" style="background: #e8f8f0; font-size: 24px;">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold mb-1 text-primary" style="font-family: 'Playfair Display', serif;">WhatsApp Checkout</h4>
                                <p class="text-muted small mb-0">Saves an unpaid order and opens a prepared WhatsApp message. The customer must send it; payment and confirmation are arranged with the store.</p>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="whatsapp_checkout_enabled" id="whatsapp_checkout_enabled" value="1" {{ $paymentSetting->whatsapp_checkout_enabled ? 'checked' : '' }} style="width: 48px; height: 24px; cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12"><div class="alert alert-info">Online payments go to each store’s own activated merchant account. An in-store QR code or Soundbox alone does not enable website payments. Use the Create account link below if you do not have credentials. Registration and activation are handled by the provider. Return here to enter your credentials, configure the webhook and test an order before switching to Live.</div></div>
            @include('admin.settings.partials.paytm-settings')

            <div class="col-12">
                <details class="gateway-settings card border-0 rounded-4 shadow-sm bg-white" data-gateway-settings="razorpay" @if(collect(array_keys($errors->getMessages()))->contains(fn ($field) => str_starts_with($field, 'razorpay_'))) open @endif>
                    @include('admin.settings.partials.gateway-summary', ['gateway' => 'razorpay', 'gatewayName' => 'Razorpay'])
                    <div class="gateway-settings-body p-3 p-md-4 border-top">
                        <label class="form-check form-switch d-flex align-items-center gap-2 mb-4">
                            <input class="form-check-input" type="checkbox" name="razorpay_enabled" id="razorpay_enabled" value="1" @checked(old('razorpay_enabled', $paymentSetting->razorpay_enabled))>
                            <span class="small fw-semibold">Enable Razorpay payments</span>
                        </label>
                    @include('admin.settings.partials.gateway-onboarding', ['gateway' => 'razorpay'])

                    <div class="form-group mb-3">
                        <label for="razorpay_key" class="form-label fw-semibold text-dark small">Razorpay Key ID</label>
                        <input type="text" name="razorpay_key" id="razorpay_key" class="form-control rounded-3 py-2 px-3 @error('razorpay_key') is-invalid @enderror" value="{{ old('razorpay_key', $paymentSetting->razorpay_key) }}" placeholder="rzp_test_...">
                        @error('razorpay_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="razorpay_secret" class="form-label fw-semibold text-dark small">Razorpay Key Secret</label>
                        <input type="password" name="razorpay_secret" id="razorpay_secret" class="form-control rounded-3 py-2 px-3 @error('razorpay_secret') is-invalid @enderror" value="" autocomplete="new-password" placeholder="Secret Key">
                        <small class="text-muted" style="font-size: 11px;">Leave blank if not changing saved secret.</small>
                        @error('razorpay_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="razorpay_webhook_secret" class="form-label fw-semibold small">Customer payment webhook secret</label>
                        <input type="password" name="razorpay_webhook_secret" id="razorpay_webhook_secret" value="" autocomplete="new-password" class="form-control" placeholder="Leave blank to keep the saved secret">
                        <small class="text-muted">Use the same secret (at least 16 characters) in Razorpay Dashboard → Webhooks. Keep this separate from MetoHub subscription billing.</small>
                        <p class="small mt-2 mb-1">Webhook URL:</p>
                        <code class="d-block text-break">{{ route('payment.razorpay.webhook', ['tenant' => $paymentSetting->tenant_id]) }}</code>
                        <p class="small text-muted mt-2">Subscribe to <strong>payment.captured</strong> and <strong>order.paid</strong>. Enable automatic payment capture in Razorpay. Test the full payment flow before using live keys.</p>
                        <p class="small text-muted">{{ str_starts_with((string) $paymentSetting->razorpay_key, 'rzp_live_') ? 'Live key saved' : 'Test key or no key saved' }} · Saving credentials does not verify your gateway account. Store currency must be INR.</p>
                    </div>
                    <div class="p-2.5 rounded-3 bg-light mt-auto">
                        <small class="text-muted d-block" style="font-size: 11px;">Accepts UPI (GPay, PhonePe, Paytm), Cards, NetBanking, and Wallets.</small>
                    </div>
                    </div>
                </details>
            </div>

            @include('admin.settings.partials.phonepe-settings')
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <button type="submit" class="btn btn-primary text-white py-2.5 px-4 fw-semibold" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important; border: none !important; border-radius: 8px; box-shadow: 0 4px 10px rgba(139, 92, 246, 0.25);">
                Save Payment Settings
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.gateway-settings').forEach(section => {
        section.addEventListener('invalid', function () { section.open = true; }, true);
    });
    // Check if gateway has saved credentials from database
    const hasSavedWebhookSecret = {{ !empty($paymentSetting->razorpay_webhook_secret) ? 'true' : 'false' }};
    const hasSavedRazorpaySecret = {{ !empty($paymentSetting->razorpay_secret) ? 'true' : 'false' }};

    function updateGatewayState(config) {
        const canEnable = config.isFilled();
        const toggle = document.getElementById(config.toggleId);
        if (!toggle) return;

        if (!canEnable) {
            toggle.checked = false;
            toggle.disabled = true;
            toggle.style.opacity = '0.5';
            toggle.style.cursor = 'not-allowed';
            toggle.title = config.disabledMessage;
        } else {
            toggle.disabled = false;
            toggle.style.opacity = '1';
            toggle.style.cursor = 'pointer';
            toggle.title = 'Click to enable or disable';
        }
    }

    // Razorpay
    const razorpayKeyInput = document.getElementById('razorpay_key');
    const razorpaySecretInput = document.getElementById('razorpay_secret');
    const webhookSecretInput = document.getElementById('razorpay_webhook_secret');
    function checkRazorpay() {
        updateGatewayState({
            toggleId: 'razorpay_enabled',
            disabledMessage: 'Set the Razorpay Key ID, Key Secret and webhook secret to enable.',
            isFilled: function () {
                const key = razorpayKeyInput ? razorpayKeyInput.value.trim() : '';
                const secret = razorpaySecretInput ? razorpaySecretInput.value.trim() : '';
                return key.length > 0 && (secret.length > 0 || hasSavedRazorpaySecret) && (webhookSecretInput.value.length >= 16 || hasSavedWebhookSecret);
            }
        });
    }

    // Attach real-time input listeners
    [razorpayKeyInput, razorpaySecretInput, webhookSecretInput].forEach(el => el && el.addEventListener('input', checkRazorpay));

    // Run on initial load
    checkRazorpay();
});
</script>
@endpush
