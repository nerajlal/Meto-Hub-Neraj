<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\TenantPaymentSetting;
use Illuminate\Support\Facades\Log;

class OnlinePaymentController extends Controller
{
    /**
     * Create an online payment order/session for the customer.
     */
    public function initiate(Request $request)
    {
        $tenantId = $request->attributes->get('resolved_tenant_id') ?? session('active_tenant_id');
        $tenant = Tenant::findOrFail($tenantId);
        $settings = TenantPaymentSetting::withoutGlobalScopes()->where('tenant_id', $tenant->id)->first();

        $request->validate([
            'gateway' => 'required|string|in:razorpay,stripe,phonepe',
            'amount' => 'required|numeric|min:1',
            'currency' => 'nullable|string',
            'customer_name' => 'nullable|string',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
        ]);

        $gateway = $request->gateway;
        $amount = (float) $request->amount;
        $currency = strtoupper($tenant->currency ?? 'INR');

        if ($gateway === 'razorpay') {
            if (!$settings?->razorpay_enabled || empty($settings->razorpay_key) || empty($settings->razorpay_secret)) {
                return response()->json(['success' => false, 'message' => 'Razorpay is not configured for this store.'], 422);
            }

            try {
                $api = new \Razorpay\Api\Api($settings->razorpay_key, $settings->razorpay_secret);
                $razorpayOrder = $api->order->create([
                    'receipt' => 'rcpt_' . uniqid(),
                    'amount' => (int) round($amount * 100), // in paise
                    'currency' => $currency === '₹' ? 'INR' : $currency,
                    'notes' => [
                        'tenant_id' => (string) $tenant->id,
                        'store_name' => $tenant->name,
                    ],
                ]);

                return response()->json([
                    'success' => true,
                    'gateway' => 'razorpay',
                    'key' => $settings->razorpay_key,
                    'order_id' => $razorpayOrder['id'],
                    'amount' => $razorpayOrder['amount'],
                    'currency' => $razorpayOrder['currency'],
                    'store_name' => $tenant->name,
                ]);
            } catch (\Throwable $e) {
                Log::error('Razorpay Order Creation Failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to initialize Razorpay payment. ' . $e->getMessage()], 500);
            }
        }

        if ($gateway === 'stripe') {
            if (!$settings?->stripe_enabled || empty($settings->stripe_secret)) {
                return response()->json(['success' => false, 'message' => 'Stripe is not configured for this store.'], 422);
            }

            try {
                \Stripe\Stripe::setApiKey($settings->stripe_secret);
                $intent = \Stripe\PaymentIntent::create([
                    'amount' => (int) round($amount * 100),
                    'currency' => strtolower($currency === '₹' ? 'inr' : $currency),
                    'metadata' => [
                        'tenant_id' => (string) $tenant->id,
                        'store_name' => $tenant->name,
                    ],
                ]);

                return response()->json([
                    'success' => true,
                    'gateway' => 'stripe',
                    'client_secret' => $intent->client_secret,
                    'publishable_key' => $settings->stripe_key,
                ]);
            } catch (\Throwable $e) {
                Log::error('Stripe Intent Creation Failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to initialize Stripe payment. ' . $e->getMessage()], 500);
            }
        }

        if ($gateway === 'phonepe') {
            if (!$settings?->phonepe_enabled || empty($settings->phonepe_merchant_id) || empty($settings->phonepe_salt_key)) {
                return response()->json(['success' => false, 'message' => 'PhonePe is not configured for this store.'], 422);
            }

            try {
                $merchantId = $settings->phonepe_merchant_id;
                $saltKey = $settings->phonepe_salt_key;
                $saltIndex = $settings->phonepe_salt_index ?: '1';
                $transactionId = 'MT_' . time() . '_' . rand(1000, 9999);

                $payload = [
                    'merchantId' => $merchantId,
                    'merchantTransactionId' => $transactionId,
                    'merchantUserId' => 'CUST_' . ($request->customer_phone ?: 'GUEST'),
                    'amount' => (int) round($amount * 100),
                    'redirectUrl' => url('/'),
                    'redirectMode' => 'POST',
                    'callbackUrl' => route('payment.phonepe.callback'),
                    'paymentInstrument' => [
                        'type' => 'PAY_PAGE',
                    ],
                ];

                $base64Payload = base64_encode(json_encode($payload));
                $checksum = hash('sha256', $base64Payload . '/pg/v1/pay' . $saltKey) . '###' . $saltIndex;

                return response()->json([
                    'success' => true,
                    'gateway' => 'phonepe',
                    'transaction_id' => $transactionId,
                    'base64_payload' => $base64Payload,
                    'checksum' => $checksum,
                    'message' => 'PhonePe payload prepared successfully.',
                ]);
            } catch (\Throwable $e) {
                Log::error('PhonePe Payload Creation Failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Failed to initialize PhonePe payment. ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['success' => false, 'message' => 'Unknown gateway selected.'], 422);
    }

    /**
     * Verify payment signature (e.g. Razorpay).
     */
    public function verify(Request $request)
    {
        $tenantId = $request->attributes->get('resolved_tenant_id') ?? session('active_tenant_id');
        $tenant = Tenant::findOrFail($tenantId);
        $settings = TenantPaymentSetting::withoutGlobalScopes()->where('tenant_id', $tenant->id)->first();

        $gateway = $request->gateway;

        if ($gateway === 'razorpay') {
            $request->validate([
                'razorpay_payment_id' => 'required|string',
                'razorpay_order_id' => 'required|string',
                'razorpay_signature' => 'required|string',
            ]);

            try {
                $api = new \Razorpay\Api\Api($settings->razorpay_key, $settings->razorpay_secret);
                $api->utility->verifyPaymentSignature([
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_signature' => $request->razorpay_signature,
                ]);

                return response()->json(['success' => true, 'verified' => true]);
            } catch (\Throwable $e) {
                return response()->json(['success' => false, 'message' => 'Signature verification failed: ' . $e->getMessage()], 422);
            }
        }

        return response()->json(['success' => true]);
    }
}
