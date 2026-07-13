@extends('template_3.layouts.app')

@section('title', 'Shipping Policy | ' . ($currentTenant->name ?? 'Fresh Grocery'))

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 3rem 1.5rem; background: #fff; border-radius: 1.5rem; border: 1px solid var(--border-color);">
    <h1 style="font-weight: 800; font-size: 2.2rem; color: var(--primary-color); margin-bottom: 1.5rem;">Shipping & Delivery Policy</h1>
    @if($currentTenant->shipping_policy)
        <div style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6;">
            {!! nl2br(e($currentTenant->shipping_policy)) !!}
        </div>
    @else
        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;">At {{ $currentTenant->name ?? 'Fresh Grocery' }}, we strive to deliver your farm fresh produce and daily essentials directly to your doorstep in the shortest time possible.</p>
        
        <h2 style="font-weight: 700; font-size: 1.4rem; margin-top: 2rem; margin-bottom: 1rem;">Delivery Timeframes</h2>
        <p style="color: var(--primary-color); line-height: 1.6;">Our standard delivery time is within 2 hours of order confirmation. Delivery slots can also be scheduled at your convenience during checkout.</p>

        <h2 style="font-weight: 700; font-size: 1.4rem; margin-top: 2rem; margin-bottom: 1rem;">Shipping Rates</h2>
        <p style="color: var(--primary-color); line-height: 1.6;">Free delivery is applicable on all orders above ₹499. For orders below this threshold, a flat delivery fee of ₹40 will be charged.</p>
    @endif
</div>
@endsection
