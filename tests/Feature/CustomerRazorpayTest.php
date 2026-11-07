<?php

namespace Tests\Feature;

use App\Models\{CustomerPayment, Order, Product, Tenant, TenantPaymentSetting, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{DB, Http, Mail};
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerRazorpayTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $store;
    private Product $product;
    private string $checkout;
    private array $remotePayment = [];
    private array $remoteOrder = [];
    private bool $outage = false;
    private array $discoveryOrders = [];

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Http::preventStrayRequests();
        $this->store = Tenant::create(['name' => 'Razorpay Shop', 'currency' => 'INR', 'tax_name' => 'Tax', 'tax_rate' => 5]);
        $this->product = Product::create(['tenant_id' => $this->store->id, 'title' => 'Chips', 'status' => 'active']);
        $this->product->variants()->create(['size' => '100g', 'price' => 100, 'stock' => 10]);
        TenantPaymentSetting::create(['tenant_id' => $this->store->id, 'cod_enabled' => true,
            'razorpay_enabled' => true, 'razorpay_key' => 'rzp_test_demo', 'razorpay_secret' => 'merchant-secret',
            'razorpay_webhook_secret' => 'merchant-webhook-secret']);
        $this->checkout = (string) Str::uuid();
        $this->withSession(['active_tenant_id' => $this->store->id, 'cart' => [$this->line()]]);
        Http::fake(function ($request) {
            if ($this->outage) return Http::response([], 503);
            if ($request->method() === 'POST' && str_ends_with($request->url(), '/orders')) {
                return Http::response(['id' => 'order_demo', 'amount' => $request['amount'], 'currency' => $request['currency']]);
            }
            if (str_contains($request->url(), '/orders?')) return Http::response(['items' => $this->discoveryOrders]);
            if (str_ends_with($request->url(), '/payments/pay_demo')) return Http::response(array_replace([
                'id' => 'pay_demo', 'order_id' => 'order_demo', 'status' => 'captured', 'captured' => true,
                'amount' => 10500, 'currency' => 'INR', 'amount_refunded' => 0], $this->remotePayment));
            if (str_ends_with($request->url(), '/orders/order_demo/payments')) return Http::response(['items' => [['id' => 'pay_demo', 'status' => 'captured']]]);
            if (str_ends_with($request->url(), '/orders/order_demo')) return Http::response(array_replace([
                'id' => 'order_demo', 'amount' => 10500, 'currency' => 'INR', 'status' => 'paid', 'amount_paid' => 10500,
                'receipt' => $this->checkout, 'notes' => ['tenant_id' => (string) $this->store->id, 'checkout_id' => $this->checkout]], $this->remoteOrder));
            throw new \RuntimeException('Unexpected provider request: '.$request->url());
        });
    }

    private function line(): array
    {
        return ['product_id' => $this->product->id, 'variant_id' => $this->product->variants()->first()->id,
            'quantity' => 1, 'size' => '100g', 'type' => 'product', 'price' => 1];
    }

    private function fields(): array
    {
        return ['gateway' => 'razorpay', 'payment_method' => 'razorpay', 'checkout_request_id' => $this->checkout,
            'name' => 'Buyer', 'email' => 'buyer@example.com', 'phone' => '9999999999', 'address' => 'Street',
            'city' => 'Kochi', 'state' => 'Kerala', 'pincode' => '682001', 'amount' => 1, 'currency' => 'USD'];
    }

    private function initiate()
    {
        return $this->postJson('/payment/initiate', $this->fields());
    }

    private function proof(): array
    {
        return ['gateway' => 'razorpay', 'checkout_id' => $this->checkout, 'razorpay_payment_id' => 'pay_demo',
            'razorpay_order_id' => 'order_demo', 'razorpay_signature' => hash_hmac('sha256', 'order_demo|pay_demo', 'merchant-secret')];
    }

    private function webhook(string $signature = '')
    {
        $body = json_encode(['event' => 'payment.captured', 'payload' => ['payment' => ['entity' => ['order_id' => 'order_demo', 'id' => 'pay_demo']]]]);
        return $this->call('POST', '/payments/razorpay/'.$this->store->id.'/webhook', [], [], [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json',
                'HTTP_X_RAZORPAY_SIGNATURE' => $signature ?: hash_hmac('sha256', $body, 'merchant-webhook-secret')], $body);
    }

    public function test_server_prices_the_cart_and_tax_and_reuses_the_checkout(): void
    {
        $this->initiate()->assertOk()->assertJson(['amount' => 10500, 'currency' => 'INR']);
        $this->initiate()->assertOk()->assertJson(['order_id' => 'order_demo']);
        $this->assertDatabaseCount('customer_payments', 1);
        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $this->product->variants()->first()->stock);
        Http::assertSentCount(1);
        $stored = DB::table('customer_payments')->first();
        $this->assertStringNotContainsString('merchant-secret', $stored->credentials);
        $this->assertStringNotContainsString('buyer@example.com', $stored->snapshot);
    }

    public function test_forged_paid_submission_cannot_create_a_paid_order(): void
    {
        $this->postJson('/order/place', $this->fields() + ['gateway_payment_id' => 'pay_fake'])->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
        $this->postJson('/order/place', array_replace($this->fields(), ['payment_method' => 'cod', 'gateway_payment_id' => 'pay_fake']))->assertOk();
        $order = Order::withoutGlobalScopes()->first();
        $this->assertSame('pending', $order->payment_status);
        $this->assertNull($order->gateway_payment_id);
    }

    public function test_verified_payment_creates_exactly_one_order_and_reserves_stock_once(): void
    {
        $this->initiate()->assertOk();
        $this->postJson('/payment/verify', $this->proof())->assertOk()->assertJson(['verified' => true]);
        $this->postJson('/payment/verify', $this->proof())->assertOk();
        $this->webhook()->assertOk();
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(9, $this->product->variants()->first()->stock);
        $order = Order::withoutGlobalScopes()->first();
        $this->assertEquals(105, $order->total_amount);
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('pay_demo', $order->gateway_payment_id);
        $this->assertDatabaseCount('merchant_order_alerts', 1);
        Mail::assertSent(\App\Mail\OrderSummaryMail::class, 1);
    }

    public function test_bad_signature_is_rejected_before_calling_provider(): void
    {
        $this->initiate();
        $this->postJson('/payment/verify', array_replace($this->proof(), ['razorpay_signature' => str_repeat('0', 64)]))->assertUnprocessable();
        Http::assertSentCount(1);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_amount_currency_order_and_capture_must_all_match(): void
    {
        $this->initiate();
        foreach ([['amount' => 100], ['currency' => 'USD'], ['order_id' => 'order_other'],
            ['status' => 'authorized', 'captured' => false], ['amount_refunded' => 100]] as $bad) {
            $this->remotePayment = $bad;
            $this->postJson('/payment/verify', $this->proof())->assertUnprocessable();
        }
        $this->remotePayment = [];
        $this->remoteOrder = ['notes' => ['tenant_id' => '999', 'checkout_id' => $this->checkout]];
        $this->postJson('/payment/verify', $this->proof())->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_another_browser_cannot_read_or_complete_a_checkout(): void
    {
        $this->initiate();
        $this->withSession(['customer_payment_owner' => 'another-browser']);
        $this->postJson('/payment/verify', $this->proof())->assertNotFound();
        $this->postJson('/payment/status', ['checkout_id' => $this->checkout])->assertNotFound();
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_signed_webhook_recovers_payment_without_browser_and_rejects_forged_events(): void
    {
        $this->initiate();
        $this->webhook('invalid')->assertUnauthorized();
        $other = Tenant::create(['name' => 'Other Store']);
        $this->withSession(['active_tenant_id' => $other->id, 'cart' => []]);
        $this->webhook()->assertOk();
        $this->assertDatabaseHas('orders', ['tenant_id' => $this->store->id, 'payment_status' => 'paid']);
        $this->assertSame('completed', CustomerPayment::first()->status);
    }

    public function test_payment_survives_stock_failure_and_reconciliation_recovers_without_double_charge(): void
    {
        $this->initiate();
        $variant = $this->product->variants()->first();
        $variant->update(['stock' => 0]);
        $this->postJson('/payment/verify', $this->proof())->assertStatus(202)->assertJson(['status' => 'review']);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('customer_payments', ['provider_payment_id' => 'pay_demo', 'status' => 'review']);
        $variant->update(['stock' => 5, 'price' => 250]);
        $this->artisan('payments:reconcile', ['--checkout' => $this->checkout])->assertSuccessful();
        $this->assertDatabaseHas('orders', ['payment_status' => 'paid', 'total_amount' => 105]);
        $this->assertSame(4, $variant->fresh()->stock);
    }

    public function test_api_outage_never_marks_paid_or_retries_order_creation(): void
    {
        $this->initiate();
        $this->outage = true;
        $this->postJson('/payment/verify', $this->proof())->assertStatus(503);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('customer_payments', ['status' => 'ready']);
    }

    public function test_unknown_creation_result_is_durable_and_not_reposted(): void
    {
        $this->outage = true;
        $this->initiate()->assertStatus(409);
        $this->initiate()->assertStatus(409);
        Http::assertSentCount(1);
        $this->assertDatabaseHas('customer_payments', ['status' => 'creation_unknown']);
    }

    public function test_incomplete_providers_and_unknown_verification_are_rejected(): void
    {
        foreach (['stripe', 'phonepe', 'online'] as $gateway) {
            $this->postJson('/payment/initiate', array_replace($this->fields(), ['gateway' => $gateway]))->assertUnprocessable();
            $this->postJson('/order/place', array_replace($this->fields(), ['payment_method' => $gateway]))->assertUnprocessable();
        }
        $this->postJson('/payment/verify', ['gateway' => 'stripe'])->assertUnprocessable();
        Http::assertNothingSent();
    }

    public function test_payment_settings_encrypt_and_do_not_render_saved_secrets(): void
    {
        $owner = User::factory()->create(['tenant_id' => $this->store->id, 'type' => 'admin']);
        $this->actingAs($owner)->get('/'.$this->store->id.'/admin/settings/payment')->assertOk()
            ->assertDontSee('value="merchant-secret"', false)->assertDontSee('value="merchant-webhook-secret"', false)
            ->assertSee('payment.captured')->assertSee('order.paid')->assertSee('id="razorpay_webhook_secret"', false)
            ->assertSee('Paytm Payments')->assertSee('Client Version')->assertDontSee('Stripe Checkout')->assertDontSee('name="stripe_secret"', false);
        $this->assertStringNotContainsString('@yield', $this->get('/'.$this->store->id.'/admin/settings/payment')->getContent());
        $raw = DB::table('tenant_payment_settings')->where('tenant_id', $this->store->id)->value('razorpay_secret');
        $this->assertNotSame('merchant-secret', $raw);
        $this->assertSame('merchant-secret', TenantPaymentSetting::withoutGlobalScopes()->first()->razorpay_secret);
    }
    public function test_shared_variant_stock_is_checked_across_duplicate_cart_lines_before_payment(): void
    {
        $line = $this->line();
        $line['quantity'] = 6;
        $this->withSession(['cart' => [$line, $line]]);
        $this->initiate()->assertUnprocessable();
        Http::assertNothingSent();
        $this->assertDatabaseCount('customer_payments', 0);
    }

    public function test_foreign_product_and_invalid_delivery_details_cannot_reach_gateway(): void
    {
        $other = Tenant::create(['name' => 'Other']);
        $this->product->update(['tenant_id' => $other->id]);
        $this->initiate()->assertUnprocessable();
        $this->postJson('/payment/initiate', array_replace($this->fields(), ['name' => ['invalid']]))->assertUnprocessable();
        Http::assertNothingSent();
    }

    public function test_pending_create_timeout_is_reconciled_by_receipt_without_a_second_post(): void
    {
        $this->outage = true;
        $this->initiate()->assertStatus(409);
        $this->outage = false;
        $this->discoveryOrders = [['id' => 'order_demo', 'amount' => 10500, 'currency' => 'INR',
            'receipt' => $this->checkout, 'notes' => ['checkout_id' => $this->checkout, 'tenant_id' => (string) $this->store->id]]];
        $this->travel(2)->minutes();
        $this->postJson('/payment/status', ['checkout_id' => $this->checkout])->assertOk()->assertJson(['verified' => true]);
        $this->assertDatabaseCount('orders', 1);
        $posts = Http::recorded(fn ($request) => $request->method() === 'POST');
        $this->assertCount(1, $posts);
    }

    public function test_failed_creation_only_allows_restart_after_provider_lookup_confirms_no_order(): void
    {
        $this->outage = true;
        $this->initiate()->assertStatus(409);
        $this->outage = false;
        $this->travel(2)->minutes();
        $this->postJson('/payment/status', ['checkout_id' => $this->checkout])->assertStatus(202)->assertJson(['restart_allowed' => true]);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_full_refund_before_fulfilment_closes_review_without_creating_an_order(): void
    {
        $this->initiate();
        $this->remotePayment = ['amount_refunded' => 10500, 'status' => 'refunded', 'captured' => true];
        $this->postJson('/payment/verify', $this->proof())->assertStatus(202)->assertJson(['status' => 'refunded']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_superadmin_settings_use_the_store_url_and_preserve_secrets_on_blank_input(): void
    {
        $other = Tenant::create(['name' => 'Superadmin home']);
        $super = User::factory()->create(['type' => 'super_admin', 'tenant_id' => $other->id]);
        $this->actingAs($super)->post('/'.$this->store->id.'/admin/settings/payment', [
            'cod_enabled' => 1, 'razorpay_enabled' => 1, 'razorpay_key' => 'rzp_test_demo', 'razorpay_secret' => '',
            'stripe_enabled' => 1, 'phonepe_enabled' => 0])->assertRedirect()->assertSessionHasNoErrors();
        $settings = TenantPaymentSetting::withoutGlobalScopes()->where('tenant_id', $this->store->id)->first();
        $this->assertSame('merchant-secret', $settings->razorpay_secret);
        $this->assertTrue($settings->razorpay_enabled);
        $this->assertFalse($settings->stripe_enabled);
        $this->assertFalse($settings->phonepe_enabled);
        $this->assertDatabaseMissing('tenant_payment_settings', ['tenant_id' => $other->id]);
    }

    public function test_existing_plaintext_secrets_are_encrypted_by_migration(): void
    {
        $migration = require database_path('migrations/2026_11_07_200000_create_customer_payments_table.php');
        $migration->down();
        DB::table('tenant_payment_settings')->where('tenant_id', $this->store->id)->update(['razorpay_secret' => 'legacy-secret']);
        $migration->up();
        $this->assertNotSame('legacy-secret', DB::table('tenant_payment_settings')->where('tenant_id', $this->store->id)->value('razorpay_secret'));
        $this->assertSame('legacy-secret', TenantPaymentSetting::withoutGlobalScopes()->first()->razorpay_secret);
    }

    public function test_bundle_variant_quantities_and_discounts_are_frozen_and_reserved_once(): void
    {
        $bundle = \App\Models\Bundle::create(['tenant_id' => $this->store->id, 'title' => 'Pair', 'slug' => 'pair',
            'type' => 'bundle', 'status' => 'active', 'discount_type' => 'fixed', 'discount_value' => 50]);
        $bundle->products()->attach($this->product->id, ['product_variant_id' => $this->product->variants()->first()->id, 'quantity' => 2]);
        $this->withSession(['cart' => [['bundle_id' => $bundle->id, 'quantity' => 1, 'type' => 'bundle', 'price' => 1]]]);
        $this->initiate()->assertOk()->assertJson(['amount' => 15750]);
        $this->remotePayment = ['amount' => 15750];
        $this->remoteOrder = ['amount' => 15750, 'amount_paid' => 15750];
        $this->postJson('/payment/verify', $this->proof())->assertOk();
        $this->postJson('/payment/status', ['checkout_id' => $this->checkout])->assertOk();
        $this->assertSame(8, $this->product->variants()->first()->stock);
        $this->assertEquals(1, $bundle->fresh()->total_sales);
        $this->assertDatabaseHas('orders', ['total_amount' => 157.50]);
    }

    public function test_product_discount_and_tax_are_verified_against_the_saved_quote(): void
    {
        $discount = \App\Models\Discount::create(['tenant_id' => $this->store->id, 'code' => 'TEN',
            'type' => 'percentage', 'value' => 10, 'status' => 'active']);
        $discount->products()->attach($this->product->id);
        $this->initiate()->assertOk()->assertJson(['amount' => 9450]);
        $this->remotePayment = ['amount' => 9450];
        $this->remoteOrder = ['amount' => 9450, 'amount_paid' => 9450];
        $this->postJson('/payment/verify', $this->proof())->assertOk();
        $this->assertDatabaseHas('orders', ['subtotal' => 100, 'discount_amount' => 10, 'tax_amount' => 4.5, 'total_amount' => 94.5]);
        $this->assertEquals(1, $discount->fresh()->uses_count);
    }

    public function test_authenticated_cart_cleanup_preserves_new_unpaid_cart_rows(): void
    {
        $buyer = User::factory()->create(['tenant_id' => $this->store->id, 'type' => 'user']);
        $cart = \App\Models\Cart::create(['tenant_id' => $this->store->id, 'user_id' => $buyer->id,
            'product_id' => $this->product->id, 'product_variant_id' => $this->product->variants()->first()->id, 'quantity' => 1, 'size' => '100g']);
        $this->actingAs($buyer);
        $this->initiate()->assertOk();
        $cart->update(['quantity' => 2]);
        $this->postJson('/payment/verify', $this->proof())->assertOk();
        $this->assertDatabaseHas('carts', ['id' => $cart->id, 'quantity' => 2]);
        $this->assertDatabaseHas('orders', ['user_id' => $buyer->id, 'total_amount' => 105]);
    }

    public function test_captured_review_is_visible_in_merchant_admin(): void
    {
        $this->initiate();
        $this->product->variants()->first()->update(['stock' => 0]);
        $this->postJson('/payment/verify', $this->proof())->assertStatus(202);
        $owner = User::factory()->create(['tenant_id' => $this->store->id, 'type' => 'admin']);
        $this->actingAs($owner)->get('/'.$this->store->id.'/admin/settings/payment')->assertOk()
            ->assertSee('customer payment(s) need order review')->assertSee($this->checkout)->assertSee('pay_demo');
    }

    public function test_checkout_cannot_silently_reuse_an_old_address_after_editing_the_form(): void
    {
        $this->initiate()->assertOk();
        $this->postJson('/payment/initiate', array_replace($this->fields(), ['address' => 'Changed address']))->assertUnprocessable();
        Http::assertSentCount(1);
    }

    public function test_invalid_credentials_are_not_flashed_back_to_the_session(): void
    {
        $owner = User::factory()->create(['tenant_id' => $this->store->id, 'type' => 'admin']);
        $this->actingAs($owner)->post('/'.$this->store->id.'/admin/settings/payment', [
            'razorpay_enabled' => 1, 'razorpay_key' => 'rzp_test_demo', 'razorpay_secret' => 'do-not-flash-secret',
            'razorpay_webhook_secret' => 'short'])->assertRedirect()->assertSessionHasErrors('razorpay_webhook_secret')
            ->assertSessionMissing('_old_input.razorpay_secret')->assertSessionMissing('_old_input.razorpay_webhook_secret');
    }

    public function test_old_payment_status_refresh_cannot_clear_a_new_guest_cart(): void
    {
        $this->initiate();
        $this->postJson('/payment/verify', $this->proof())->assertOk()->assertSessionMissing('cart');
        $this->withSession(['cart' => [$this->line()]]);
        $this->postJson('/payment/status', ['checkout_id' => $this->checkout])->assertOk()->assertSessionHas('cart');
    }

}
