<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Shipment update</title></head>
<body style="font-family: Arial, sans-serif; color: #202020; padding: 24px;">
<h1 style="font-size: 24px;">Your order is on its way</h1>
<p>Hello {{ $order->customer_name ?: 'there' }},</p>
<p>{{ $storeName }} has added shipment details for order <strong>#{{ $order->order_number }}</strong>.</p>
@if($order->deliveryPartner)<p>Courier: {{ $order->deliveryPartner->name }}</p>@endif
<p>Tracking number: <strong>{{ $order->tracking_number }}</strong></p>
@if($trackingUrl = $order->trackingUrl())<p><a href="{{ $trackingUrl }}" target="_blank" rel="noopener noreferrer">Track shipment</a></p>@endif
@if($order->deliveryPartner?->name === 'India Post')<p>Enter the tracking number on the India Post website.</p>@endif
<p>Tracking updates may take time to appear on the courier website.</p>
</body></html>
