@if($order->tracking_number && in_array($order->status, ['shipped', 'delivered'], true))
<div style="margin: 1rem 0; padding: 1rem; border: 1px solid var(--border-color, #ddd); border-radius: 1rem; overflow-wrap: anywhere;">
    <p style="font-weight: 700; margin: 0 0 .5rem;">Shipment details</p>
    @if($order->deliveryPartner && (int) $order->deliveryPartner->tenant_id === (int) $order->tenant_id)
        <p style="margin: 0 0 .5rem;">Courier: {{ $order->deliveryPartner->name }}</p>
    @endif
    <p style="margin: 0 0 .5rem;">Tracking number: <strong>{{ $order->tracking_number }}</strong></p>
    @if($trackingUrl = $order->trackingUrl())
        <a href="{{ $trackingUrl }}" target="_blank" rel="noopener noreferrer" style="color: var(--primary-color, #312e81); font-weight: 700;">Track shipment ↗</a>
        @if($order->deliveryPartner?->name === 'India Post')<p style="margin: .5rem 0 0; font-size: .85rem;">Enter the tracking number on the India Post website.</p>@endif
    @endif
</div>
@endif
