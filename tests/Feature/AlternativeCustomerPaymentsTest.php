<?php

namespace Tests\Feature;

use App\Models\{CustomerPayment, Order, Product, Tenant, TenantPaymentSetting, User};
use App\Services\PaytmChecksum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB, Http, Mail};
use Illuminate\Support\Str;
use Tests\TestCase;

class AlternativeCustomerPaymentsTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $store;
    private Product $product;
    private string $id;
    private string $gateway = 'paytm';
    private array $statusOverrides = [];
    private bool $badSignature = false;
    private bool $outage = false;
    private bool $creationOutage = false;
    private string $redirect = 'https://mercury-t2.phonepe.com/transact/pg?token=fixture';

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Http::preventStrayRequests();
        $this->id = (string) Str::uuid();
        $this->store = Tenant::create(['name' => 'Direct Gateways', 'currency' => 'INR', 'tax_name' => 'Tax', 'tax_rate' => 5]);
        $this->product = Product::create(['tenant_id' => $this->store->id, 'title' => 'Chips', 'status' => 'active']);
        $variant = $this->product->variants()->create(['size' => '100g', 'price' => 100, 'stock' => 10]);
        TenantPaymentSetting::create(['tenant_id' => $this->store->id, 'cod_enabled' => false,
            'paytm_enabled' => true, 'paytm_mid' => 'MIDfixture12345678901', 'paytm_merchant_key' => '1234567890123456',
            'paytm_environment' => 'test', 'paytm_website_name' => 'WEBSTAGING',
            'phonepe_enabled' => true, 'phonepe_client_id' => 'fixture-client', 'phonepe_client_version' => 1,
            'phonepe_client_secret' => 'fixture-client-secret', 'phonepe_environment' => 'test',
            'phonepe_webhook_username' => 'fixture-user', 'phonepe_webhook_password' => 'fixture-webhook-password']);
        $this->withSession(['active_tenant_id' => $this->store->id, 'cart' => [[
            'type' => 'product', 'product_id' => $this->product->id, 'variant_id' => $variant->id,
            'quantity' => 1, 'price' => 1, 'size' => '100g']]]);
        Http::fake(function ($request) {
            if ($this->outage) return Http::response([], 503);
            if (str_contains($request->url(), 'paytmpayments.com')) {
                $payload = json_decode($request->body(), true);
                $this->assertTrue(PaytmChecksum::valid(json_encode($payload['body'], JSON_UNESCAPED_SLASHES), '1234567890123456', $payload['head']['signature']));
                if (str_contains($request->url(), '/initiateTransaction')) {
                    if ($this->creationOutage) return Http::response([], 503);
                    $this->assertSame('105.00', $payload['body']['txnAmount']['value']);
                    return $this->paytmResponse(['resultInfo' => ['resultStatus' => 'S'], 'txnToken' => 'fixture-token']);
                }
                $this->assertSame($this->reference(), $payload['body']['orderId']);
                return $this->paytmResponse(array_replace(['resultInfo' => ['resultStatus' => 'TXN_SUCCESS'],
                    'mid' => 'MIDfixture12345678901', 'orderId' => $this->reference(), 'txnAmount' => '105.00',
                    'txnId' => 'PTfixture123', 'refundAmt' => '0.00'], $this->statusOverrides));
            }
            if (str_contains($request->url(), '/v1/oauth/token')) {
                $this->assertSame('fixture-client', $request['client_id']);
                $this->assertSame('fixture-client-secret', $request['client_secret']);
                $this->assertSame('client_credentials', $request['grant_type']);
                return Http::response(['access_token' => 'oauth-fixture-token', 'token_type' => 'O-Bearer', 'expires_at' => time() + 3600]);
            }
            $this->assertSame('O-Bearer oauth-fixture-token', $request->header('Authorization')[0] ?? '');
            if (str_ends_with($request->url(), '/checkout/v2/pay')) {
                if ($this->creationOutage) return Http::response([], 503);
                $this->assertSame(10500, $request['amount']);
                $this->assertSame($this->reference(), $request['merchantOrderId']);
                return Http::response(['orderId' => 'OMO_fixture', 'state' => 'PENDING', 'redirectUrl' => $this->redirect]);
            }
            if (str_contains($request->url(), '/checkout/v2/order/'.$this->reference().'/status')) {
                return Http::response(array_replace(['orderId' => 'OMO_fixture', 'merchantOrderId' => $this->reference(),
                    'amount' => 10500, 'state' => 'COMPLETED', 'metaInfo' => ['udf1' => $this->id],
                    'paymentDetails' => [['state' => 'COMPLETED', 'transactionId' => 'PPfixture123', 'amount' => 10500]]], $this->statusOverrides));
            }
            throw new \RuntimeException('Unexpected request: '.$request->url());
        });
    }

    private function reference(): string { return strtoupper(substr($this->gateway, 0, 2)).str_replace('-', '', $this->id); }
    private function paytmResponse(array $body)
    {
        return Http::response(['body' => $body, 'head' => ['signature' => $this->badSignature ? base64_encode('invalid')
            : PaytmChecksum::sign(json_encode($body, JSON_UNESCAPED_SLASHES), '1234567890123456')]]);
    }
    private function fields(): array
    {
        return ['gateway' => $this->gateway, 'payment_method' => $this->gateway, 'checkout_request_id' => $this->id,
            'name' => 'Buyer', 'email' => 'buyer@example.com', 'phone' => '9999999999', 'address' => 'Street',
            'city' => 'Kochi', 'state' => 'Kerala', 'pincode' => '682001', 'amount' => 1];
    }
    private function start() { return $this->postJson('/payment/initiate', $this->fields()); }
    private function checkStatus() { return $this->postJson('/payment/status', ['checkout_id' => $this->id]); }

    public function test_paytm_uses_server_amount_and_one_persisted_order(): void
    {
        $this->start()->assertOk()->assertJson(['gateway' => 'paytm', 'amount' => 10500, 'txn_token' => 'fixture-token'])
            ->assertDontSee('1234567890123456');
        $this->start()->assertOk();
        Http::assertSentCount(1);
        $this->assertDatabaseCount('orders', 0);
        $this->checkStatus()->assertOk()->assertJson(['verified' => true]);
        $this->checkStatus()->assertOk();
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(9, $this->product->variants()->first()->stock);
        $this->assertDatabaseHas('orders', ['payment_method' => 'paytm', 'payment_status' => 'paid', 'total_amount' => 105]);
    }

    public function test_paytm_rejects_invalid_status_signature_and_mismatched_amount_merchant_order(): void
    {
        $this->start();
        $this->badSignature = true;
        $this->checkStatus()->assertStatus(503);
        $this->badSignature = false;
        foreach ([['txnAmount' => '1.00'], ['orderId' => 'other'], ['mid' => 'other'], ['txnType' => 'PREAUTH']] as $bad) {
            $this->statusOverrides = $bad;
            $this->checkStatus()->assertStatus(503);
        }
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_paytm_pending_is_not_paid_and_refunds_are_not_fulfilled(): void
    {
        $this->start();
        $this->statusOverrides = ['resultInfo' => ['resultStatus' => 'PENDING']];
        $this->checkStatus()->assertStatus(202);
        $this->statusOverrides = ['refundAmt' => '105.00'];
        $this->checkStatus()->assertStatus(202)->assertJson(['status' => 'refunded']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_paytm_signed_callback_recovers_without_shopper_cookie_and_rejects_forgery(): void
    {
        $this->start();
        $params = ['MID' => 'MIDfixture12345678901', 'ORDERID' => $this->reference(), 'STATUS' => 'TXN_SUCCESS', 'RESPMSG' => 'Success  '];
        $params['CHECKSUMHASH'] = PaytmChecksum::sign($params, '1234567890123456');
        $this->withSession(['customer_payment_owner' => 'another-browser', 'active_tenant_id' => null]);
        $this->post('/payments/paytm/'.$this->id.'/callback', $params + ['EXTRA' => 'forged'])->assertUnauthorized();
        $body = http_build_query($params);
        $this->call('POST', '/payments/paytm/'.$this->id.'/callback', [], [], [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded'], $body)->assertStatus(303);
        $this->assertDatabaseCount('orders', 1);
        $this->get('/payments/return/'.$this->id)->assertOk()->assertSee('Return to the browser')->assertDontSee('buyer@example.com');
    }

    public function test_paytm_webhook_checks_signature_and_queries_provider(): void
    {
        $this->start();
        $params = ['MID' => 'MIDfixture12345678901', 'ORDERID' => $this->reference(), 'STATUS' => 'TXN_SUCCESS'];
        $this->postJson('/payments/paytm/'.$this->store->id.'/webhook', $params)->assertUnauthorized();
        $params['CHECKSUMHASH'] = PaytmChecksum::sign($params, '1234567890123456');
        $this->postJson('/payments/paytm/'.$this->store->id.'/webhook', $params)->assertOk();
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_phonepe_v2_auth_redirect_and_status_are_verified(): void
    {
        $this->gateway = 'phonepe';
        $this->start()->assertOk()->assertJson(['gateway' => 'phonepe', 'payment_url' => $this->redirect, 'amount' => 10500])
            ->assertDontSee('fixture-client-secret')->assertDontSee('oauth-fixture-token');
        $this->checkStatus()->assertOk()->assertJson(['verified' => true]);
        $this->checkStatus()->assertOk();
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(9, $this->product->variants()->first()->stock);
        $this->assertDatabaseHas('orders', ['gateway_name' => 'phonepe', 'payment_status' => 'paid', 'gateway_payment_id' => 'PPfixture123']);
    }

    public function test_phonepe_pending_failed_wrong_amount_wrong_order_and_wrong_transaction_are_not_paid(): void
    {
        $this->gateway = 'phonepe';
        $this->start();
        $this->statusOverrides = ['state' => 'PENDING'];
        $this->checkStatus()->assertStatus(202);
        foreach ([['amount' => 100], ['orderId' => 'another-order'], ['merchantOrderId' => 'other'],
            ['paymentDetails' => [['state' => 'COMPLETED', 'amount' => 1, 'transactionId' => 'x']]]] as $bad) {
            $this->statusOverrides = $bad;
            $this->checkStatus()->assertStatus(503);
        }
        $this->statusOverrides = ['state' => 'FAILED'];
        $this->checkStatus()->assertStatus(202)->assertJson(['restart_allowed' => true]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_phonepe_rejects_untrusted_checkout_redirect_hosts(): void
    {
        $this->gateway = 'phonepe';
        $this->redirect = 'https://phonepe.com.attacker.example/pay';
        $this->start()->assertStatus(409)->assertDontSee('attacker.example');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_phonepe_authenticated_webhook_recovers_without_browser_and_cannot_trust_payload_amount(): void
    {
        $this->gateway = 'phonepe';
        $this->start();
        $payload = ['event' => 'checkout.order.completed', 'payload' => ['merchantOrderId' => $this->reference(), 'amount' => 1]];
        $url = '/payments/phonepe/'.$this->store->id.'/webhook';
        $this->postJson($url, $payload)->assertUnauthorized();
        $this->withSession(['customer_payment_owner' => 'different', 'active_tenant_id' => null]);
        $this->withHeader('Authorization', hash('sha256', 'fixture-user:fixture-webhook-password'))->postJson($url, $payload)->assertOk();
        $this->assertDatabaseHas('orders', ['tenant_id' => $this->store->id, 'total_amount' => 105, 'payment_status' => 'paid']);
    }

    public function test_both_gateways_preserve_captured_money_when_stock_disappears(): void
    {
        $this->gateway = 'phonepe';
        $this->start();
        $variant = $this->product->variants()->first();
        $variant->update(['stock' => 0]);
        $this->checkStatus()->assertStatus(202)->assertJson(['status' => 'review']);
        $variant->update(['stock' => 5]);
        $this->checkStatus()->assertStatus(202)->assertJson(['status' => 'review']); // PhonePe refunds require a merchant check before review retry.
        $owner = User::factory()->create(['tenant_id' => $this->store->id, 'type' => 'admin']);
        $this->actingAs($owner)->post('/'.$this->store->id.'/admin/settings/payment/review/'.$this->id, ['confirm_not_refunded' => 1])->assertRedirect();
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(4, $variant->fresh()->stock);
    }

    public function test_creation_timeouts_are_recovered_by_stored_merchant_reference(): void
    {
        $this->gateway = 'phonepe';
        $this->creationOutage = true;
        $this->start()->assertStatus(409);
        $this->creationOutage = false;
        $this->checkStatus()->assertOk()->assertJson(['verified' => true]);
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_other_browser_and_gateway_switch_cannot_reuse_checkout(): void
    {
        $this->start();
        $this->gateway = 'phonepe';
        $this->start()->assertUnprocessable();
        $this->withSession(['customer_payment_owner' => 'other']);
        $this->checkStatus()->assertNotFound();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_provider_outage_never_creates_paid_order(): void
    {
        $this->gateway = 'phonepe';
        $this->start();
        $this->outage = true;
        $this->checkStatus()->assertStatus(503);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_settings_save_current_credentials_encrypt_secrets_and_do_not_render_them(): void
    {
        $owner = User::factory()->create(['tenant_id' => $this->store->id, 'type' => 'admin']);
        $url = '/'.$this->store->id.'/admin/settings/payment';
        $this->actingAs($owner)->post($url, ['paytm_enabled' => 1, 'phonepe_enabled' => 1])->assertRedirect()->assertSessionHasNoErrors();
        $this->get($url)->assertOk()->assertSee('PhonePe Payments')->assertSee('Client Version')->assertSee('Paytm Payments')
            ->assertDontSee('value="1234567890123456"', false)->assertDontSee('value="fixture-client-secret"', false)
            ->assertDontSee('value="fixture-webhook-password"', false)->assertDontSee('name="phonepe_salt_key"', false);
        $raw = DB::table('tenant_payment_settings')->first();
        $this->assertNotSame('1234567890123456', $raw->paytm_merchant_key);
        $this->assertNotSame('fixture-client-secret', $raw->phonepe_client_secret);
    }
    public function test_provider_return_confirms_owned_payment_and_clears_only_its_cart(): void
    {
        $this->gateway = 'phonepe';
        $this->start();
        $this->get('/payments/return/'.$this->id)->assertOk()->assertSee('Your payment is confirmed')
            ->assertSee('View your order')->assertSessionMissing('cart')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_expired_paytm_token_checks_status_before_reopening_and_never_recharges_completed_order(): void
    {
        $this->start();
        $payment = CustomerPayment::first();
        $data = $payment->checkout_data;
        $data['expires_at'] = time() - 1;
        $payment->update(['checkout_data' => $data]);
        $this->start()->assertOk()->assertJson(['verified' => true]);
        $this->assertCount(1, Http::recorded(fn ($request) => str_contains($request->url(), '/initiateTransaction')));
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_valid_notifications_for_other_paytm_orders_are_ignored_without_retries(): void
    {
        $params = ['MID' => 'MIDfixture12345678901', 'ORDERID' => 'non-metohub-order', 'STATUS' => 'TXN_SUCCESS'];
        $params['CHECKSUMHASH'] = PaytmChecksum::sign($params, '1234567890123456');
        $this->postJson('/payments/paytm/'.$this->store->id.'/webhook', $params)->assertOk();
        Http::assertNothingSent();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_paytm_stock_review_recovers_once_stock_returns(): void
    {
        $this->start();
        $variant = $this->product->variants()->first();
        $variant->update(['stock' => 0]);
        $this->checkStatus()->assertStatus(202)->assertJson(['status' => 'review']);
        $variant->update(['stock' => 3]);
        $this->checkStatus()->assertOk();
        $this->assertSame(2, $variant->fresh()->stock);
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_invalid_gateway_secrets_are_not_flashed_and_incomplete_phonepe_cannot_enable(): void
    {
        $owner = User::factory()->create(['tenant_id' => $this->store->id, 'type' => 'admin']);
        $this->actingAs($owner)->post('/'.$this->store->id.'/admin/settings/payment', [
            'paytm_enabled' => 1, 'paytm_merchant_key' => 'invalid-key',
            'phonepe_client_secret' => 'never-flash-this', 'phonepe_webhook_password' => 'short'])
            ->assertRedirect()->assertSessionHasErrors('paytm_merchant_key')
            ->assertSessionMissing('_old_input.paytm_merchant_key')->assertSessionMissing('_old_input.phonepe_client_secret')
            ->assertSessionMissing('_old_input.phonepe_webhook_password');
        TenantPaymentSetting::withoutGlobalScopes()->first()->update(['phonepe_client_secret' => null]);
        $this->postJson('/'.$this->store->id.'/admin/settings/payment', ['phonepe_enabled' => 1])->assertUnprocessable();
    }

    public function test_phonepe_sdk_data_envelope_is_authenticated_and_reconciled(): void
    {
        $this->gateway = 'phonepe';
        $this->start();
        $this->withHeader('Authorization', hash('sha256', 'fixture-user:fixture-webhook-password'))
            ->postJson('/payments/phonepe/'.$this->store->id.'/webhook', ['data' => [
                'merchantOrderId' => $this->reference(), 'orderId' => 'OMO_fixture', 'state' => 'COMPLETED', 'amount' => 1]])->assertOk();
        $this->assertDatabaseHas('orders', ['payment_status' => 'paid', 'total_amount' => 105]);
    }

    public function test_phonepe_refund_callback_is_not_misclassified_as_order_completion(): void
    {
        $this->gateway = 'phonepe';
        $this->start();
        $this->withHeader('Authorization', hash('sha256', 'fixture-user:fixture-webhook-password'))
            ->postJson('/payments/phonepe/'.$this->store->id.'/webhook', ['data' => [
                'merchantOrderId' => $this->reference(), 'orderId' => 'OMO_fixture', 'state' => 'COMPLETED',
                'merchantRefundId' => 'refund-fixture']])->assertOk();
        $this->assertDatabaseHas('customer_payments', ['id' => $this->id, 'status' => 'review']);
        $this->checkStatus()->assertStatus(202);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_refund_hold_cannot_be_overwritten_by_a_status_check_already_in_flight(): void
    {
        $this->gateway = 'phonepe';
        $this->start();
        $stale = CustomerPayment::first();
        CustomerPayment::whereKey($stale->id)->update(['status' => 'review', 'review_reason' => 'Refund notice received']);
        app(\App\Services\CustomerPayments::class)->completeVerified($stale, 'PPfixture123');
        $this->assertDatabaseHas('customer_payments', ['id' => $stale->id, 'status' => 'review']);
        $this->assertDatabaseCount('orders', 0);
    }

}
