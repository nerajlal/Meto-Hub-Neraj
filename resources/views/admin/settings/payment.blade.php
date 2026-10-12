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
            <p class="text-white-50 small mb-0" style="max-width: 600px; line-height: 1.6;">Choose whether customers can place cash on delivery orders.</p>
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
                                <p class="text-muted small mb-0">Allow customers to pay in cash upon package delivery.</p>
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="cod_enabled" id="cod_enabled" value="1" {{ $paymentSetting->cod_enabled ? 'checked' : '' }} style="width: 48px; height: 24px; cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="alert alert-info rounded-4" role="status">
                    Online checkout is unavailable until gateway processing and payment confirmation are integrated and verified.
                    Cash on delivery is the supported payment method. Disabling it pauses checkout for your store.
                </div>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top d-flex justify-content-end">
            <button type="submit" class="btn btn-primary text-white py-2.5 px-4 fw-semibold" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important; border: none !important; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 128, 96, 0.15);">
                Save Payment Settings
            </button>
        </div>
    </form>
</div>
@endsection
