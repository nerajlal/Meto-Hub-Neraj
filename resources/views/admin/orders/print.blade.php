<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>{{ $document === 'invoice' ? 'Invoice' : 'Shipping Label' }} — {{ $order->order_number }}</title>
<style>
*{box-sizing:border-box}body{margin:0;padding:24px;background:#f3f4f6;color:#17231f;font:14px/1.6 Arial,sans-serif}.paper{max-width:800px;margin:auto;background:white;border:1px solid #dce1df;padding:32px;overflow-wrap:anywhere}h1{font-size:24px;margin:0}h2{font-size:16px;margin:0 0 8px}.muted{color:#59665f}.top{border-bottom:2px solid #17231f;padding-bottom:20px;margin-bottom:24px}.addresses{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin:24px 0}p{margin:4px 0}table{width:100%;border-collapse:collapse;table-layout:fixed}th,td{padding:10px 6px;border-bottom:1px solid #ddd;text-align:left;vertical-align:top}.number{text-align:right}.totals{margin:20px 0 20px auto;max-width:320px}.totals p{display:flex;justify-content:space-between;gap:16px}.toolbar{max-width:800px;margin:0 auto 16px;display:flex;flex-wrap:wrap;gap:12px;align-items:center}button{background:#193d30;color:white;border:0;border-radius:6px;padding:12px 20px;cursor:pointer}.notice{border-top:1px solid #ddd;padding-top:16px;font-size:12px}@media(max-width:480px){body{padding:12px}.paper{padding:18px}.addresses{grid-template-columns:1fr;gap:16px}th,td{padding:8px 3px;font-size:12px}}@page{size:A4;margin:12mm}@media print{body{padding:0;background:white}.paper{border:0;padding:0;max-width:none}.toolbar{display:none}thead{display:table-header-group}tr,.addresses,.totals{break-inside:avoid}}
</style>
</head>
<body>
@php
    $store = $order->tenant;
    $shipping = $order->shipping_address ?? [];
    $billing = $order->billing_address ?: $shipping;
    $address = $document === 'invoice' ? $billing : $shipping;
    $currency = $store->currency ?? '₹';
@endphp
<div class="toolbar"><button onclick="window.print()">Print {{ $document === 'invoice' ? 'Invoice' : 'Shipping Label' }} / Save PDF</button><span class="muted">Choose “Save as PDF” in your print dialog to download.</span></div>
<main class="paper">
<header class="top"><p class="muted">{{ $document === 'invoice' ? 'ORDER INVOICE' : 'SHIPPING LABEL' }}</p><h1>{{ $store->name }}</h1><p><strong>{{ $order->order_number }}</strong> · {{ $order->created_at->format('d M Y') }}</p></header>
<div class="addresses">
<section><h2>From</h2><p><strong>{{ $store->name }}</strong></p>
@if($store->contact_address)<p style="white-space:pre-line">{{ $store->contact_address }}</p>@else<p class="muted">Sender address not configured. Update Storefront → Contact Page Details before dispatch.</p>@endif
@if($store->contact_phone)<p>Phone: {{ $store->contact_phone }}</p>@endif
@if($store->contact_email)<p>{{ $store->contact_email }}</p>@endif
</section>
<section><h2>{{ $document === 'invoice' ? 'Bill to' : 'Deliver to' }}</h2><p><strong>{{ $order->customer_name }}</strong></p>
@foreach(['address','apartment','city','state','zip','country'] as $field)@if(!empty($address[$field]))<p>{{ $address[$field] }}</p>@endif @endforeach
@if($order->customer_phone)<p>Phone: {{ $order->customer_phone }}</p>@endif
</section></div>
<p>Payment: <strong>{{ strtoupper($order->payment_method) }}</strong> · {{ $order->payment_status === 'paid' ? 'Paid' : 'Unpaid' }}</p>
@if($document === 'label')
@if($order->payment_method === 'cod')<p><strong>Collect on delivery: {{ $currency }}{{ number_format($order->payment_status === 'paid' ? 0 : $order->total_amount, 2) }}</strong></p>@endif
@if($order->tracking_number)<p>Tracking: {{ $order->tracking_number }}</p>@endif
@endif
<table><thead><tr><th style="width:{{ $document === 'invoice' ? '48' : '80' }}%">Item</th><th class="number">Qty</th>@if($document === 'invoice')<th class="number">Price</th><th class="number">Total</th>@endif</tr></thead><tbody>
@foreach($order->items as $item)<tr><td>{{ $item->name }}@if($item->size)<br><span class="muted">Size: {{ $item->size }}</span>@endif</td><td class="number">{{ $item->quantity }}</td>@if($document === 'invoice')<td class="number">{{ $currency }}{{ number_format($item->price, 2) }}</td><td class="number">{{ $currency }}{{ number_format($item->total, 2) }}</td>@endif</tr>@endforeach
</tbody></table>
@if($document === 'invoice')
<div class="totals"><p><span>Subtotal</span><span>{{ $currency }}{{ number_format($order->subtotal, 2) }}</span></p><p><span>Shipping</span><span>{{ $currency }}{{ number_format($order->shipping_cost, 2) }}</span></p>
@if($order->discount_amount > 0)<p><span>Discount</span><span>−{{ $currency }}{{ number_format($order->discount_amount, 2) }}</span></p>@endif
@if($order->tax_amount > 0)<p><span>{{ $order->tax_name ?: 'Tax' }}</span><span>{{ $currency }}{{ number_format($order->tax_amount, 2) }}</span></p>@endif
<p><strong>Total</strong><strong>{{ $currency }}{{ number_format($order->total_amount, 2) }}</strong></p></div>
<p class="notice">Order invoice generated from the recorded order details. This document does not confirm payment unless marked Paid. Tax registration details and a statutory tax-invoice series are not configured.</p>
@else<p class="notice">Store-generated packing label. Courier booking and courier-issued labels are managed separately.</p>@endif
</main></body></html>
