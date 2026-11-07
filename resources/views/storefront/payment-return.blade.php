<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Payment status | MetoHub</title>
<style>body{margin:0;background:#f7f7fb;color:#252238;font:16px/1.6 system-ui,sans-serif;display:grid;place-items:center;min-height:100vh}main{max-width:520px;margin:24px;padding:32px;background:white;border:1px solid #e5e2ed;border-radius:20px}h1{font-size:26px}a,button{display:inline-block;background:#6d28d9;color:white;border:0;border-radius:10px;padding:12px 18px;text-decoration:none;font:inherit;cursor:pointer}code{overflow-wrap:anywhere;font-size:13px}</style></head>
<body><main><p>MetoHub · Secure checkout</p><h1>Payment status</h1>
@if(!$owned)
<p>Return to the browser where you started checkout to check the result, or contact the store with this reference. Do not pay again until the store checks the payment.</p>
@elseif($payment->status === 'completed')
<p>Your payment is confirmed and your order has been saved.</p>
<p><a href="{{ $orderUrl }}">View your order</a></p>
<script>sessionStorage.removeItem('metohub-checkout-{{ $payment->tenant_id }}');localStorage.removeItem('checkout_form_data');</script>
@elseif($payment->status === 'review')
<p>Your payment needs store review. Do not pay again.</p>
@elseif($payment->status === 'failed')
<p>The provider reports that this checkout failed or expired. Return to your store checkout to check status before starting again.</p>
@else
<p>Payment is not confirmed yet. Check again shortly. If money was deducted, do not pay again.</p>
@endif
<p>Checkout reference:<br><code>{{ $payment->id }}</code></p>
@if($owned)<form method="GET"><button type="submit">Check again</button></form>@endif
<p><a href="{{ $storeCheckoutUrl }}">Return to store checkout</a></p>
<p class="small">You can close this page and return to your store checkout to use “Check payment status”.</p>
</main></body></html>
