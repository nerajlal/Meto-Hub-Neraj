@extends('template_1.layouts.app')

@section('title', 'Return Policy | ' . ($currentTenant->name ?? 'SaaS Store'))

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 3rem 1.5rem; background: #fff; border-radius: 1.5rem; border: 1px solid var(--border-color);">
    <h1 style="font-weight: 800; font-size: 2.2rem; color: var(--primary-color); margin-bottom: 1.5rem;">Return & Refund Policy</h1>
    @if($currentTenant->return_policy)
        <div style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6;">
            {!! nl2br(e($currentTenant->return_policy)) !!}
        </div>
    @else
        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;">We back our fresh products with a 100% satisfaction guarantee. If you are not satisfied with the quality of any product, you can return it easily.</p>
        
        <h2 style="font-weight: 700; font-size: 1.4rem; margin-top: 2rem; margin-bottom: 1rem;">No Questions Asked Return</h2>
        <p style="color: var(--primary-color); line-height: 1.6;">You can return fresh items (vegetables, fruits, dairy, bakery) directly at the time of delivery if the quality does not meet your expectations. We will issue an instant refund to your wallet or original payment method.</p>

        <h2 style="font-weight: 700; font-size: 1.4rem; margin-top: 2rem; margin-bottom: 1rem;">Refund Timelines</h2>
        <p style="color: var(--primary-color); line-height: 1.6;">Refunds for cancelled or returned orders are processed within 24-48 business hours.</p>
    @endif
</div>
@endsection
