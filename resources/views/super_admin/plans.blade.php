@extends('super_admin.layouts.app')

@section('title', 'SaaS Pricing Plans')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">SaaS Plan Pricing & Tiers</h1>
        <p class="text-muted small">Manage subscription pricing tiers, feature lists, and store limits for all tenants.</p>
    </div>
</div>

<div class="row g-4">
    @foreach($plans as $plan)
    <div class="col-12 col-lg-4">
        <div class="card h-100 border shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark fw-bold">{{ $plan['name'] }}</h5>
                <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase small" style="font-size: 10px;">active</span>
            </div>
            <div class="card-body vstack gap-4">
                <div class="d-flex align-items-baseline">
                    <h2 class="fw-bold text-dark mb-0">{{ $currentTenant->currency ?? '₹' }}{{ number_format($plan['price'] * 85, 0) }}</h2>
                    <span class="text-muted small ms-2">/ {{ $plan['billing'] }}</span>
                </div>

                <div>
                    <h6 class="text-dark fw-bold mb-2">Store Allocation:</h6>
                    <span class="badge bg-light text-secondary border px-2 py-1 rounded fw-semibold">{{ $plan['limit'] }}</span>
                </div>

                <div>
                    <h6 class="text-dark fw-bold mb-2">Features Included:</h6>
                    <ul class="list-unstyled mb-0 vstack gap-2 small text-muted">
                        @foreach($plan['features'] as $feature)
                        <li>
                            <i class="fas fa-check-circle text-success me-2"></i> {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="card-footer bg-light border-top py-3 text-center">
                <button class="btn btn-sm btn-outline-primary rounded-3 w-100" disabled>
                    <i class="fas fa-cog me-1"></i> Configure Plan (SaaS Stripe API Integrated)
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
