@extends('layouts.admin')

@section('title', 'Payment Settings')

@section('content')
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

    <form action="{{ route('admin.settings.payment.update') }}" method="POST">
        @csrf

        <div class="row g-4">
            <!-- COD Gateway -->
            <div class="col-12">
                <div class="card border-0 rounded-4 shadow-sm p-4" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-light rounded-3 text-secondary" style="font-size: 24px;">
                                <i class="fa-solid fa-hand-holding-dollar"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold mb-1 text-primary" style="font-family: 'Playfair Display', serif;">Cash on Delivery (COD)</h4>
                                <p class="text-muted small mb-0">Allow customers to pay in cash or via doorstep UPI upon order arrival.</p>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="cod_enabled" id="cod_enabled" value="1" {{ $paymentSetting->cod_enabled ? 'checked' : '' }} style="width: 48px; height: 24px; cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stripe Checkout -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 bg-primary-subtle text-primary rounded-3" style="font-size: 22px;">
                                <i class="fa-brands fa-stripe"></i>
                            </div>
                            <h4 class="h6 fw-bold mb-0 text-dark" style="font-family: 'Playfair Display', serif;">Stripe Checkout</h4>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="stripe_enabled" id="stripe_enabled" value="1" {{ $paymentSetting->stripe_enabled ? 'checked' : '' }} style="width: 40px; height: 20px; cursor: pointer;">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="stripe_key" class="form-label fw-semibold text-dark small">Stripe Publishable Key</label>
                        <input type="text" name="stripe_key" id="stripe_key" class="form-control rounded-3 py-2 px-3 @error('stripe_key') is-invalid @enderror" value="{{ old('stripe_key', $paymentSetting->stripe_key) }}" placeholder="pk_test_...">
                        @error('stripe_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="stripe_secret" class="form-label fw-semibold text-dark small">Stripe Secret Key</label>
                        <input type="password" name="stripe_secret" id="stripe_secret" class="form-control rounded-3 py-2 px-3 @error('stripe_secret') is-invalid @enderror" value="{{ old('stripe_secret', $paymentSetting->stripe_secret) }}" placeholder="sk_test_...">
                        <small class="text-muted" style="font-size: 11px;">Leave blank if not changing saved secret.</small>
                        @error('stripe_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="p-2.5 rounded-3 bg-light mt-auto">
                        <small class="text-muted d-block" style="font-size: 11px;">Supports International Credit/Debit cards & Apple Pay.</small>
                    </div>
                </div>
            </div>

            <!-- Razorpay Gateway -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 bg-info-subtle text-info rounded-3" style="font-size: 22px;">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <h4 class="h6 fw-bold mb-0 text-dark" style="font-family: 'Playfair Display', serif;">Razorpay Payments</h4>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="razorpay_enabled" id="razorpay_enabled" value="1" {{ $paymentSetting->razorpay_enabled ? 'checked' : '' }} style="width: 40px; height: 20px; cursor: pointer;">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="razorpay_key" class="form-label fw-semibold text-dark small">Razorpay Key ID</label>
                        <input type="text" name="razorpay_key" id="razorpay_key" class="form-control rounded-3 py-2 px-3 @error('razorpay_key') is-invalid @enderror" value="{{ old('razorpay_key', $paymentSetting->razorpay_key) }}" placeholder="rzp_test_...">
                        @error('razorpay_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="razorpay_secret" class="form-label fw-semibold text-dark small">Razorpay Key Secret</label>
                        <input type="password" name="razorpay_secret" id="razorpay_secret" class="form-control rounded-3 py-2 px-3 @error('razorpay_secret') is-invalid @enderror" value="{{ old('razorpay_secret', $paymentSetting->razorpay_secret) }}" placeholder="Secret Key">
                        <small class="text-muted" style="font-size: 11px;">Leave blank if not changing saved secret.</small>
                        @error('razorpay_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="p-2.5 rounded-3 bg-light mt-auto">
                        <small class="text-muted d-block" style="font-size: 11px;">Accepts UPI (GPay, PhonePe, Paytm), Cards, NetBanking, and Wallets.</small>
                    </div>
                </div>
            </div>

            <!-- PhonePe Gateway -->
            <div class="col-lg-4 col-12">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 bg-success-subtle text-success rounded-3" style="font-size: 22px;">
                                <i class="fa-solid fa-mobile-screen-button"></i>
                            </div>
                            <h4 class="h6 fw-bold mb-0 text-dark" style="font-family: 'Playfair Display', serif;">PhonePe Payment</h4>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="phonepe_enabled" id="phonepe_enabled" value="1" {{ $paymentSetting->phonepe_enabled ? 'checked' : '' }} style="width: 40px; height: 20px; cursor: pointer;">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="phonepe_merchant_id" class="form-label fw-semibold text-dark small">Merchant ID</label>
                        <input type="text" name="phonepe_merchant_id" id="phonepe_merchant_id" class="form-control rounded-3 py-2 px-3 @error('phonepe_merchant_id') is-invalid @enderror" value="{{ old('phonepe_merchant_id', $paymentSetting->phonepe_merchant_id) }}" placeholder="MID...">
                        @error('phonepe_merchant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="phonepe_salt_key" class="form-label fw-semibold text-dark small">Salt Key</label>
                        <input type="password" name="phonepe_salt_key" id="phonepe_salt_key" class="form-control rounded-3 py-2 px-3 @error('phonepe_salt_key') is-invalid @enderror" value="{{ old('phonepe_salt_key', $paymentSetting->phonepe_salt_key) }}" placeholder="Salt Key">
                        <small class="text-muted" style="font-size: 11px;">Leave blank if not changing saved salt key.</small>
                        @error('phonepe_salt_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="phonepe_salt_index" class="form-label fw-semibold text-dark small">Salt Index</label>
                        <input type="text" name="phonepe_salt_index" id="phonepe_salt_index" class="form-control rounded-3 py-2 px-3 @error('phonepe_salt_index') is-invalid @enderror" value="{{ old('phonepe_salt_index', $paymentSetting->phonepe_salt_index) }}" placeholder="e.g. 1">
                        @error('phonepe_salt_index')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="p-2.5 rounded-3 bg-light mt-auto">
                        <small class="text-muted d-block" style="font-size: 11px;">Seamless UPI direct payments for Indian customers.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <button type="submit" class="btn btn-primary text-white py-2.5 px-4 fw-semibold" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important; border: none !important; border-radius: 8px; box-shadow: 0 4px 10px rgba(139, 92, 246, 0.25);">
                Save Payment Settings
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Check if gateway has saved credentials from database
    const hasSavedStripeSecret = {{ !empty($paymentSetting->stripe_secret) ? 'true' : 'false' }};
    const hasSavedRazorpaySecret = {{ !empty($paymentSetting->razorpay_secret) ? 'true' : 'false' }};
    const hasSavedPhonepeSalt = {{ !empty($paymentSetting->phonepe_salt_key) ? 'true' : 'false' }};

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

    // Stripe
    const stripeKeyInput = document.getElementById('stripe_key');
    const stripeSecretInput = document.getElementById('stripe_secret');
    function checkStripe() {
        updateGatewayState({
            toggleId: 'stripe_enabled',
            disabledMessage: 'Fill in Stripe Publishable Key and Secret Key to enable.',
            isFilled: function () {
                const key = stripeKeyInput ? stripeKeyInput.value.trim() : '';
                const secret = stripeSecretInput ? stripeSecretInput.value.trim() : '';
                return key.length > 0 && (secret.length > 0 || hasSavedStripeSecret);
            }
        });
    }

    // Razorpay
    const razorpayKeyInput = document.getElementById('razorpay_key');
    const razorpaySecretInput = document.getElementById('razorpay_secret');
    function checkRazorpay() {
        updateGatewayState({
            toggleId: 'razorpay_enabled',
            disabledMessage: 'Fill in Razorpay Key ID and Key Secret to enable.',
            isFilled: function () {
                const key = razorpayKeyInput ? razorpayKeyInput.value.trim() : '';
                const secret = razorpaySecretInput ? razorpaySecretInput.value.trim() : '';
                return key.length > 0 && (secret.length > 0 || hasSavedRazorpaySecret);
            }
        });
    }

    // PhonePe
    const phonepeMidInput = document.getElementById('phonepe_merchant_id');
    const phonepeSaltInput = document.getElementById('phonepe_salt_key');
    function checkPhonepe() {
        updateGatewayState({
            toggleId: 'phonepe_enabled',
            disabledMessage: 'Fill in Merchant ID and Salt Key to enable.',
            isFilled: function () {
                const mid = phonepeMidInput ? phonepeMidInput.value.trim() : '';
                const salt = phonepeSaltInput ? phonepeSaltInput.value.trim() : '';
                return mid.length > 0 && (salt.length > 0 || hasSavedPhonepeSalt);
            }
        });
    }

    // Attach real-time input listeners
    [stripeKeyInput, stripeSecretInput].forEach(el => el && el.addEventListener('input', checkStripe));
    [razorpayKeyInput, razorpaySecretInput].forEach(el => el && el.addEventListener('input', checkRazorpay));
    [phonepeMidInput, phonepeSaltInput].forEach(el => el && el.addEventListener('input', checkPhonepe));

    // Run on initial load
    checkStripe();
    checkRazorpay();
    checkPhonepe();
});
</script>
@endsection
