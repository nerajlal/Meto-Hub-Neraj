<?php

namespace App\Services;

use App\Models\{CustomerPayment, TenantPaymentSetting};
use Illuminate\Support\Facades\{Cache, Crypt, Http};
use Illuminate\Validation\ValidationException;

class AlternativeCustomerGateways
{
    private function invalid(string $message = 'Payment provider response does not match this checkout.'): never
    {
        throw ValidationException::withMessages(['payment' => $message]);
    }

    public function credentials(?TenantPaymentSetting $settings, string $gateway): array
    {
        if (!$settings) $this->invalid('Configure the payment gateway first.');
        return match ($gateway) {
            'paytm' => ['mid' => $settings->paytm_mid, 'merchant_key' => $settings->paytm_merchant_key,
                'environment' => $settings->paytm_environment, 'website' => $settings->paytm_website_name],
            'phonepe' => ['client_id' => $settings->phonepe_client_id, 'client_secret' => $settings->phonepe_client_secret,
                'client_version' => $settings->phonepe_client_version, 'environment' => $settings->phonepe_environment,
                'webhook_username' => $settings->phonepe_webhook_username, 'webhook_password' => $settings->phonepe_webhook_password],
            default => throw new \InvalidArgumentException('Unsupported payment gateway'),
        };
    }

    public function paytmHost(array $credentials): string
    {
        return ($credentials['environment'] ?? '') === 'live' ? 'https://secure.paytmpayments.com' : 'https://securestage.paytmpayments.com';
    }

    public function paytmCall(array $credentials, string $path, array $body): array
    {
        $json = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        // The exact bytes being signed are the body sent to the provider.
        $payload = '{"body":'.$json.',"head":'.json_encode(['signature' => PaytmChecksum::sign($json, $credentials['merchant_key'])], JSON_THROW_ON_ERROR).'}';
        $response = Http::connectTimeout(5)->timeout(20)->withoutRedirecting()->acceptJson()
            ->withBody($payload, 'application/json')->post($this->paytmHost($credentials).$path)->throw()->json();
        if (!is_array($response['body'] ?? null) || !PaytmChecksum::valid(
            json_encode($response['body'], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), $credentials['merchant_key'], $response['head']['signature'] ?? null)) {
            $this->invalid('Paytm response signature verification failed.');
        }
        return $response['body'];
    }

    private function phonepeCall(array $credentials, string $method, string $path, array $body = []): array
    {
        $live = $credentials['environment'] === 'live';
        $base = $live ? 'https://api.phonepe.com/apis/pg' : 'https://api-preprod.phonepe.com/apis/pg-sandbox';
        $cacheKey = 'phonepe-oauth-'.hash('sha256', json_encode($credentials));
        for ($attempt = 0; $attempt < 2; $attempt++) {
            $token = null;
            if ($encrypted = Cache::get($cacheKey)) {
                try { $token = Crypt::decryptString($encrypted); } catch (\Throwable $e) { Cache::forget($cacheKey); }
            }
            if (!$token) {
                $authBase = $live ? 'https://api.phonepe.com/apis/identity-manager' : $base;
                $auth = Http::asForm()->acceptJson()->connectTimeout(5)->timeout(20)->withoutRedirecting()
                    ->post($authBase.'/v1/oauth/token', ['client_id' => $credentials['client_id'],
                        'client_secret' => $credentials['client_secret'], 'client_version' => $credentials['client_version'],
                        'grant_type' => 'client_credentials'])->throw()->json();
                $token = $auth['access_token'] ?? null;
                if (!is_string($token) || $token === '' || strlen($token) > 8192 || preg_match('/[\r\n]/', $token)
                    || ($auth['token_type'] ?? '') !== 'O-Bearer' || (int) ($auth['expires_at'] ?? 0) <= time()) throw new \App\Exceptions\PaymentSetupRejected('PhonePe authentication failed.');
                Cache::put($cacheKey, Crypt::encryptString($token), max(1, (int) $auth['expires_at'] - time() - 300));
            }
            $http = Http::acceptJson()->withHeaders(['Authorization' => 'O-Bearer '.$token])->connectTimeout(5)->timeout(20)->withoutRedirecting();
            $response = $method === 'POST' ? $http->post($base.$path, $body) : $http->get($base.$path, $body);
            // Retrying a status GET after token expiration is safe; never automatically retry payment creation.
            if ($response->status() === 401) {
                Cache::forget($cacheKey);
                if ($method === 'GET' && $attempt === 0) continue;
            }
            return $response->throw()->json();
        }
        $this->invalid('PhonePe authentication failed.');
    }

    public function initiate(CustomerPayment $payment): CustomerPayment
    {
        // The merchant reference is persisted BEFORE the HTTP request, allowing recovery of an uncertain response.
        $reference = strtoupper(substr($payment->gateway, 0, 2)).str_replace('-', '', $payment->id);
        $payment->update(['provider_order_id' => $reference]);
        try {
            $this->start($payment);
        } catch (\Throwable $e) {
            $rejected = $e instanceof \App\Exceptions\PaymentSetupRejected || ($e instanceof \Illuminate\Http\Client\RequestException && in_array($e->response->status(), [401, 403], true));
            $payment->update(['status' => $rejected ? 'creation_failed' : 'creation_unknown',
                'review_reason' => $rejected ? 'Provider rejected checkout setup; no checkout was opened.' : 'Provider checkout creation needs reconciliation.']);
        }
        return $payment->fresh();
    }

    private function start(CustomerPayment $payment): void
    {
        $credentials = $payment->credentials;
        $returnUrl = rtrim(config('app.url'), '/').route('payment.return', ['checkout' => $payment->id], false);
        if ($payment->gateway === 'paytm') {
            $body = $this->paytmCall($credentials, '/theia/api/v1/initiateTransaction?'.http_build_query([
                'mid' => $credentials['mid'], 'orderId' => $payment->provider_order_id]), [
                'requestType' => 'Payment', 'mid' => $credentials['mid'], 'websiteName' => $credentials['website'],
                'orderId' => $payment->provider_order_id, 'txnAmount' => ['value' => number_format($payment->amount / 100, 2, '.', ''), 'currency' => 'INR'],
                'userInfo' => ['custId' => $payment->id], 'callbackUrl' => rtrim(config('app.url'), '/').route('payment.paytm.callback', ['checkout' => $payment->id], false)]);
            if (($body['resultInfo']['resultStatus'] ?? '') === 'F') throw new \App\Exceptions\PaymentSetupRejected('Paytm rejected checkout setup.');
            if (($body['resultInfo']['resultStatus'] ?? '') !== 'S' || !is_string($body['txnToken'] ?? null) || $body['txnToken'] === '') $this->invalid('Paytm checkout could not be created.');
            $data = ['token' => $body['txnToken'], 'expires_at' => time() + 14 * 60];
        } else {
            $body = $this->phonepeCall($credentials, 'POST', '/checkout/v2/pay', [
                'merchantOrderId' => $payment->provider_order_id, 'amount' => $payment->amount, 'expireAfter' => 1200,
                'metaInfo' => ['udf1' => $payment->id, 'udf2' => (string) $payment->tenant_id],
                'paymentFlow' => ['type' => 'PG_CHECKOUT', 'message' => 'Store order', 'merchantUrls' => ['redirectUrl' => $returnUrl]]]);
            if (($body['state'] ?? '') !== 'PENDING' || !is_string($body['orderId'] ?? null) || $body['orderId'] === ''
                || !$this->validPhonepeUrl($body['redirectUrl'] ?? null)) $this->invalid('PhonePe checkout could not be created.');
            $data = ['redirect_url' => $body['redirectUrl'], 'phonepe_order_id' => $body['orderId'], 'expires_at' => time() + 19 * 60];
        }
        $payment->update(['status' => 'ready', 'checkout_data' => $data, 'review_reason' => null]);
    }

    public function validPhonepeUrl(mixed $url): bool
    {
        if (!is_string($url) || !filter_var($url, FILTER_VALIDATE_URL)) return false;
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        return parse_url($url, PHP_URL_SCHEME) === 'https' && ($host === 'phonepe.com' || str_ends_with($host, '.phonepe.com'))
            && !parse_url($url, PHP_URL_USER) && !parse_url($url, PHP_URL_PASS)
            && in_array(parse_url($url, PHP_URL_PORT), [null, 443], true);
    }

    public function resumePaytm(CustomerPayment $payment, CustomerPayments $payments): CustomerPayment
    {
        $payment = $this->reconcile($payment, $payments);
        if (in_array($payment->status, ['ready', 'creating', 'creation_unknown'], true)) $this->start($payment); // Same Paytm order reference; provider supports idempotent initiation.
        return $payment->fresh();
    }

    public function checkoutData(CustomerPayment $payment): array
    {
        $data = $payment->checkout_data ?? [];
        if (($data['expires_at'] ?? 0) <= time()) $this->invalid('This payment window has expired. Check payment status before retrying.');
        if ($payment->gateway === 'phonepe') {
            if (!$this->validPhonepeUrl($data['redirect_url'] ?? null)) $this->invalid();
            return ['payment_url' => $data['redirect_url']];
        }
        return ['mid' => $payment->credentials['mid'], 'txn_token' => $data['token'],
            'script_url' => $this->paytmHost($payment->credentials).'/merchantpgpui/checkoutjs/merchants/'.rawurlencode($payment->credentials['mid']).'.js'];
    }

    public function reconcile(CustomerPayment $payment, CustomerPayments $payments, bool $retryReview = false): CustomerPayment
    {
        if (in_array($payment->status, ['completed', 'refunded'], true)) return $payment;
        // V2 order status does not expose a cumulative refund amount. Never automatically fulfil a reviewed PhonePe payment after a merchant may have refunded it.
        if ($payment->gateway === 'phonepe' && $payment->status === 'review' && !$retryReview) return $payment;
        if (!$payment->provider_order_id) return $payment;
        $payment->update(['last_checked_at' => now()]);
        if ($payment->gateway === 'paytm') {
            $body = $this->paytmCall($payment->credentials, '/v3/order/status', [
                'mid' => $payment->credentials['mid'], 'orderId' => $payment->provider_order_id]);
            $state = $body['resultInfo']['resultStatus'] ?? '';
            if ($state !== 'TXN_SUCCESS') {
                // NO_RECORD_FOUND can be transient. Do not declare an uncertain POST safe to duplicate.
                if ($state === 'TXN_FAILURE') $payment->update(['status' => 'failed']);
                return $payment->fresh();
            }
            if (($body['mid'] ?? '') !== $payment->credentials['mid'] || ($body['orderId'] ?? '') !== $payment->provider_order_id
                || $this->paise($body['txnAmount'] ?? null) !== $payment->amount
                || (isset($body['currency']) && $body['currency'] !== 'INR')
                || in_array($body['txnType'] ?? '', ['PREAUTH', 'RELEASE'], true)
                || !is_string($body['txnId'] ?? null) || $body['txnId'] === '') $this->invalid();
            $refund = $this->paise($body['refundAmt'] ?? '0.00');
            if ($refund > 0) {
                $payment->update(['status' => $refund === $payment->amount ? 'refunded' : 'review', 'review_reason' => 'Refund recorded at provider.']);
                return $payment->fresh();
            }
            return $payments->completeVerified($payment, $body['txnId']);
        }
        $body = $this->phonepeCall($payment->credentials, 'GET', '/checkout/v2/order/'.rawurlencode($payment->provider_order_id).'/status', ['details' => 'true']);
        if (($body['state'] ?? '') !== 'COMPLETED') {
            if (in_array($body['state'] ?? '', ['FAILED', 'EXPIRED'], true)) $payment->update(['status' => 'failed']);
            return $payment->fresh();
        }
        $savedOrder = $payment->checkout_data['phonepe_order_id'] ?? null;
        if ((int) ($body['amount'] ?? -1) !== $payment->amount || (isset($body['currency']) && $body['currency'] !== 'INR') || !is_string($body['orderId'] ?? null) || $body['orderId'] === ''
            || ($savedOrder && $savedOrder !== $body['orderId'])
            || (isset($body['merchantOrderId']) && $body['merchantOrderId'] !== $payment->provider_order_id)
            || (isset($body['metaInfo']['udf1']) && $body['metaInfo']['udf1'] !== $payment->id)
            || (isset($body['metaInfo']['udf2']) && (string) $body['metaInfo']['udf2'] !== (string) $payment->tenant_id)) $this->invalid();
        $matches = array_values(array_filter($body['paymentDetails'] ?? [], fn ($detail) =>
            ($detail['state'] ?? '') === 'COMPLETED' && (int) ($detail['amount'] ?? -1) === $payment->amount
            && is_string($detail['transactionId'] ?? null) && $detail['transactionId'] !== ''));
        if (count($matches) !== 1) $this->invalid();
        // For lost creation responses, the authenticated status endpoint is scoped by our stored merchant reference.
        return $payments->completeVerified($payment, $matches[0]['transactionId'], $retryReview);
    }

    private function paise(mixed $value): int
    {
        if (!is_string($value) || !preg_match('/^\d{1,9}(?:\.\d{1,2})?$/', $value)) $this->invalid();
        [$rupees, $fraction] = array_pad(explode('.', $value), 2, '');
        return ((int) $rupees * 100) + (int) str_pad($fraction, 2, '0');
    }
}
