@extends('layouts.admin')

@section('title', 'Payment Gateway Settings')

@section('content')
<div class="container-fluid py-4" style="background: #FAF9F6; min-height: 100vh;">

    <!-- Premium Header -->
    <div class="mb-4 mb-md-5 position-relative overflow-hidden p-3 p-md-4 rounded-4" style="background: linear-gradient(135deg, #111 0%, #222 100%); border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: rgba(197, 168, 128, 0.15); filter: blur(80px); top: -100px; right: -50px; pointer-events: none;"></div>
        
        <div class="position-relative z-1">
            <span class="badge mb-2 px-3 py-1.5 rounded-pill uppercase tracking-wider text-white-50" style="background: rgba(255,255,255,0.08); font-size: 10px; font-weight: 600; letter-spacing: 1.2px; border: 1px solid rgba(255,255,255,0.12);">TRANSACTIONS</span>
            <h1 class="h2 text-white mb-2 fw-bold" style="font-family: 'Playfair Display', Georgia, serif; letter-spacing: -0.5px;">Payment Gateways</h1>
            <p class="text-white-50 small mb-0" style="max-width: 600px; line-height: 1.6;">Configure the payment methods and gateway configurations accepted by your storefront.</p>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4 p-3 shadow-sm" role="alert" style="background: #EAFDF5; border-left: 5px solid #008060 !important; color: #004d3a;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle" style="color: #008060; font-size: 18px;"></i>
                <div>
                    <strong class="fw-semibold">Success!</strong> {{ session('success') }}
                </div>
            </div>
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
                                <h4 class="h6 fw-bold mb-1 text-dark" style="font-family: 'Playfair Display', serif;">Cash on Delivery (COD)</h4>
                                <p class="text-muted small mb-0">Allow customers to pay in cash upon package delivery.</p>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="cod_enabled" id="cod_enabled" value="1" {{ $paymentSetting->cod_enabled ? 'checked' : '' }} style="width: 48px; height: 24px; cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stripe Gateway -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 bg-primary-subtle text-primary rounded-3" style="font-size: 20px;">
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

                    <div class="form-group">
                        <label for="stripe_secret" class="form-label fw-semibold text-dark small">Stripe Secret Key</label>
                        <input type="password" name="stripe_secret" id="stripe_secret" class="form-control rounded-3 py-2 px-3 @error('stripe_secret') is-invalid @enderror" value="{{ old('stripe_secret', $paymentSetting->stripe_secret) }}" placeholder="sk_test_...">
                        @error('stripe_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Razorpay Gateway -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 bg-info-subtle text-info rounded-3" style="font-size: 20px;">
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

                    <div class="form-group">
                        <label for="razorpay_secret" class="form-label fw-semibold text-dark small">Razorpay Key Secret</label>
                        <input type="password" name="razorpay_secret" id="razorpay_secret" class="form-control rounded-3 py-2 px-3 @error('razorpay_secret') is-invalid @enderror" value="{{ old('razorpay_secret', $paymentSetting->razorpay_secret) }}" placeholder="Secret Key">
                        @error('razorpay_secret')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- PhonePe Gateway -->
            <div class="col-lg-4 col-12">
                <div class="card border-0 rounded-4 shadow-sm p-4 h-100" style="background: #ffffff;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 bg-success-subtle text-success rounded-3" style="font-size: 20px;">
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
                        @error('phonepe_salt_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phonepe_salt_index" class="form-label fw-semibold text-dark small">Salt Index</label>
                        <input type="text" name="phonepe_salt_index" id="phonepe_salt_index" class="form-control rounded-3 py-2 px-3 @error('phonepe_salt_index') is-invalid @enderror" value="{{ old('phonepe_salt_index', $paymentSetting->phonepe_salt_index) }}" placeholder="e.g. 1">
                        @error('phonepe_salt_index')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <button type="submit" class="btn btn-success text-white py-2.5 px-4 fw-semibold" style="background: linear-gradient(135deg, #008060 0%, #006e52 100%) !important; border: none !important; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 128, 96, 0.15);">
                Save Gateway Configurations
            </button>
        </div>
    </form>
</div>
@endsection
