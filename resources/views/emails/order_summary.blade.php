<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Summary - #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #10B981;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            color: #333333;
        }
        .content p {
            line-height: 1.6;
        }
        .order-details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-details th, .order-details td {
            border: 1px solid #eeeeee;
            padding: 12px;
            text-align: left;
        }
        .order-details th {
            background-color: #f9fafb;
            color: #6b7280;
            font-weight: 600;
        }
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        .totals p {
            margin: 5px 0;
            font-size: 16px;
        }
        .totals .total-amount {
            font-weight: bold;
            font-size: 18px;
            color: #10B981;
        }
        .footer {
            background-color: #f9fafb;
            color: #6b7280;
            text-align: center;
            padding: 15px;
            font-size: 14px;
            border-top: 1px solid #eeeeee;
        }
        .shipping-address {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .shipping-address p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Your Order!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $order->customer_name }},</p>
            <p>We have successfully received your order <strong>#{{ $order->order_number }}</strong>. Below is the summary of your purchase:</p>
            
            <table class="order-details">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                {{ $item->name }}
                                @if($item->size)
                                    <br><small style="color: #6b7280;">Size: {{ $item->size }}</small>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $order->tenant->currency ?? '₹' }}{{ number_format($item->price, 2) }}</td>
                            <td>{{ $order->tenant->currency ?? '₹' }}{{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals">
                <p>Subtotal: {{ $order->tenant->currency ?? '₹' }}{{ number_format($order->subtotal, 2) }}</p>
                <p>Shipping: {{ $order->tenant->currency ?? '₹' }}{{ number_format($order->shipping_cost, 2) }}</p>
                @if($order->tax_amount > 0)
                    <p>{{ $order->tax_name ?? 'Tax' }} ({{ $order->tax_rate }}%): {{ $order->tenant->currency ?? '₹' }}{{ number_format($order->tax_amount, 2) }}</p>
                @endif
                <p class="total-amount">Total: {{ $order->tenant->currency ?? '₹' }}{{ number_format($order->total_amount, 2) }}</p>
            </div>

            <div class="shipping-address">
                <strong>Shipping Address:</strong>
                <p>{{ $order->customer_name }}</p>
                <p>{{ $order->shipping_address['address'] ?? '' }}</p>
                @if(!empty($order->shipping_address['apartment']))
                    <p>{{ $order->shipping_address['apartment'] }}</p>
                @endif
                <p>{{ $order->shipping_address['city'] ?? '' }}, {{ $order->shipping_address['state'] ?? '' }} - {{ $order->shipping_address['zip'] ?? '' }}</p>
                <p>Phone: {{ $order->customer_phone }}</p>
            </div>
            
            <p style="margin-top: 30px;">If you have any questions, feel free to reply to this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $order->tenant->name ?? 'Our Store' }}. All rights reserved.
        </div>
    </div>
</body>
</html>
