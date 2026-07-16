<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->order_number }} - Print</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            font-size: 14px;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .invoice-details {
            text-align: right;
        }
        .shipping-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .address-box {
            width: 48%;
        }
        .address-title {
            font-size: 12px;
            text-transform: uppercase;
            color: #999;
            font-weight: 600;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .address-content {
            font-size: 16px;
        }
        .address-content p {
            margin: 0 0 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #333;
            font-size: 12px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 15px 10px;
            border-bottom: 1px solid #eee;
        }
        .total-section {
            display: flex;
            justify-content: flex-end;
        }
        .total-box {
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 14px;
        }
        .total-row.final {
            font-weight: bold;
            font-size: 18px;
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .print-btn {
            display: block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #000;
            color: #fff;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            border-radius: 4px;
        }
        @media print {
            .print-btn {
                display: none;
            }
            body {
                padding: 0;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn">Print Label</button>

    <div class="header">
        <div class="logo">{{ $order->tenant->name ?? 'Grocery Store' }}</div>
        <div class="invoice-details">
            <h1 style="margin: 0; font-size: 24px;">#{{ $order->order_number }}</h1>
            <p style="margin: 5px 0 0; color: #666;">{{ $order->created_at->format('F d, Y') }}</p>
            <p style="margin: 5px 0 0; color: #666;">Payment: {{ ucfirst($order->payment_method) }}</p>
        </div>
    </div>

    <div class="shipping-info">
        <div class="address-box">
            <div class="address-title">Ship To</div>
            <div class="address-content">
                @php
                    $shippingAddress = $order->shipping_address;
                    // Ensure it's an array if for some reason it's not cast correctly
                    if (is_string($shippingAddress)) {
                        $shippingAddress = json_decode($shippingAddress, true);
                    }
                @endphp
                <p><strong>{{ $order->customer_name }}</strong></p>
                <p>{{ $shippingAddress['address'] ?? '' }}</p>
                @if(!empty($shippingAddress['apartment']))
                    <p>{{ $shippingAddress['apartment'] }}</p>
                @endif
                <p>{{ $shippingAddress['city'] ?? '' }}, {{ $shippingAddress['state'] ?? '' }} {{ $shippingAddress['zip'] ?? '' }}</p>
                <p>Phone: {{ $order->customer_phone ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="address-box">
            <div class="address-title">From</div>
            <div class="address-content">
                <p><strong>{{ $order->tenant->name ?? 'Grocery Store' }}</strong></p>
                <p>Online Fulfillment Centre</p>
                <p>Standard Delivery Service Area</p>
                <p>{{ 'support@' . strtolower(str_replace(' ', '', $order->tenant->name ?? 'grocery')) . '.com' }}</p>
            </div>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 10%;">Qty</th>
                <th style="width: 50%;">Item</th>
                <th style="width: 20%;">SKU/Details</th>
                <th style="width: 20%; text-align: right;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->quantity }}</td>
                <td>
                    <strong>{{ $item->name }}</strong>
                    @if($item->type == 'bundle' && $item->bundle && $item->bundle->products->count() > 0)
                        <div style="font-size: 12px; color: #666; margin-top: 5px;">
                            @foreach($item->bundle->products as $bProduct)
                                <div>• {{ $bProduct->title }} @if($bProduct->variants->isNotEmpty()) ({{ $bProduct->variants->first()->size }}) @endif</div>
                            @endforeach
                        </div>
                    @endif
                </td>
                <td>
                    @if($item->size) Size: {{ $item->size }}<br> @endif
                    @if($item->sku) SKU: {{ $item->sku }} @endif
                </td>
                <td style="text-align: right;">₹{{ number_format($item->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-box">
            <div class="total-row">
                <span>Subtotal</span>
                <span>₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Shipping</span>
                <span>₹{{ number_format($order->shipping_cost, 2) }}</span>
            </div>
            <div class="total-row final">
                <span>Total</span>
                <span>₹{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for shopping with {{ $order->tenant->name ?? 'Grocery Store' }}!</p>
        <!--  -->
    </div>

    <script>
        // Automatically open print dialog
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
