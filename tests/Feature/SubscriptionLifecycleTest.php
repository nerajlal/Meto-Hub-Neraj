<?php

namespace Tests\Feature;

use App\Models\StoreSubscription;
use App\Models\SubscriptionPayment;
use App\Models\Tenant;
use App\Models\User;
use App\Services\PlanAccess;
use App\Services\SubscriptionBilling;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Razorpay\Api\Api;
use Tests\TestCase;

class SubscriptionLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $store;

    private FakeBillingGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.razorpay.key' => 'test_key', 'services.razorpay.secret' => 'test_secret', 'services.razorpay.webhook_secret' => 'test_webhook']);
        $this->store = Tenant::create(['name' => 'Billing Store', 'plan' => 'base']);
        $this->actingAs(User::factory()->create(['tenant_id' => $this->store->id, 'type' => 'admin']));
        $this->gateway = new FakeBillingGateway;
        $this->app->bind(Api::class, fn () => $this->gateway);
    }

    private function local(string $status = 'active'): StoreSubscription
    {
        $this->store->update(['razorpay_subscription_id' => 'sub_owned', 'subscription_status' => $status]);

        return StoreSubscription::create(['tenant_id' => $this->store->id, 'checkout_attempt' => fake()->uuid(), 'gateway_id' => 'sub_owned',
            'gateway_plan_id' => 'plan_owned', 'plan' => 'pro', 'cycle' => 'monthly', 'amount' => 24900, 'currency' => 'INR', 'status' => $status]);
    }

    private function webhook(string $event = 'subscription.charged', string $id = 'event_1', ?array $snapshot = null)
    {
        $payload = ['event' => $event, 'payload' => ['subscription' => ['entity' => $snapshot ?? $this->gateway->remote], 'payment' => ['entity' => $this->gateway->paymentData]]];
        $body = json_encode($payload);

        return $this->call('POST', '/billing/razorpay/webhook', [], [], [], ['CONTENT_TYPE' => 'application/json',
            'HTTP_X_RAZORPAY_SIGNATURE' => hash_hmac('sha256', $body, 'test_webhook'), 'HTTP_X_RAZORPAY_EVENT_ID' => $id], $body);
    }

    public function test_signed_webhook_records_renewal_dates_payments_and_invoices_once(): void
    {
        $local = $this->local();
        $this->webhook()->assertOk();
        $this->webhook()->assertOk()->assertJson(['duplicate' => true]);
        $this->assertDatabaseCount('subscription_payments', 1);
        $this->assertDatabaseCount('subscription_invoices', 1);
        $this->assertDatabaseCount('billing_webhook_events', 1);
        $this->assertSame('pro', $this->store->fresh()->plan);
        $this->assertSame(1, $local->fresh()->paid_count);
        $this->assertNotNull($local->fresh()->next_charge_at);
        $this->assertNotNull($local->fresh()->invoices_synced_at);
        $this->assertTrue(app(PlanAccess::class)->hasPaidAccess($this->store->fresh()));
        $this->get('/'.$this->store->id.'/admin/billing')->assertOk()->assertSee('pay_owned')->assertSee('249.00');
    }

    public function test_unsigned_events_and_unknown_subscriptions_do_not_change_billing(): void
    {
        $this->local();
        $this->postJson('/billing/razorpay/webhook', ['event' => 'subscription.charged'])->assertUnauthorized();
        $this->gateway->remote['id'] = 'sub_unknown';
        $this->webhook()->assertOk()->assertJson(['ignored' => true]);
        $this->assertDatabaseCount('subscription_payments', 0);
        $this->assertDatabaseCount('billing_webhook_events', 0);
    }

    public function test_old_activation_event_uses_authoritative_cancelled_state_instead(): void
    {
        $local = $this->local();
        $old = $this->gateway->remote;
        $this->gateway->remote['status'] = 'cancelled';
        $this->webhook('subscription.activated', 'old_event', $old)->assertOk();
        $this->assertSame('cancelled', $local->fresh()->status);
        $this->assertSame('cancelled', $this->store->fresh()->subscription_status);
    }

    public function test_authorization_callback_does_not_activate_unpaid_plan(): void
    {
        $local = $this->local('created');
        $this->gateway->remote['status'] = 'authenticated';
        $this->gateway->remote['paid_count'] = 0;
        $this->gateway->remote['current_end'] = null;
        $this->gateway->remote['current_start'] = null;
        $this->gateway->invoices = [];
        $this->gateway->paymentData['status'] = 'authorized';
        $this->gateway->paymentData['invoice_id'] = null;
        $signature = hash_hmac('sha256', 'pay_owned|sub_owned', 'test_secret');
        $this->postJson('/'.$this->store->id.'/admin/billing/verify', ['razorpay_payment_id' => 'pay_owned', 'razorpay_subscription_id' => 'sub_owned', 'razorpay_signature' => $signature])->assertOk();
        $this->assertSame('base', $this->store->fresh()->plan);
        $this->assertSame('authenticated', $local->fresh()->status);
        $this->assertFalse(app(PlanAccess::class)->hasPaidAccess($this->store->fresh()));
    }

    public function test_pending_checkout_is_reused_and_live_subscription_cannot_be_duplicated(): void
    {
        $this->postJson('/'.$this->store->id.'/admin/billing/subscribe', ['plan_tier' => 'base', 'cycle' => 'monthly'])->assertOk();
        $this->postJson('/'.$this->store->id.'/admin/billing/subscribe', ['plan_tier' => 'base', 'cycle' => 'monthly'])->assertOk();
        $this->assertSame(1, $this->gateway->creates);
        $this->postJson('/'.$this->store->id.'/admin/billing/subscribe', ['plan_tier' => 'pro', 'cycle' => 'monthly'])->assertConflict();
        $this->gateway->remote['status'] = 'active';
        $this->gateway->remote['paid_count'] = 1;
        $this->postJson('/'.$this->store->id.'/admin/billing/subscribe', ['plan_tier' => 'base', 'cycle' => 'monthly'])->assertConflict();
        $this->assertSame(1, $this->gateway->creates);
    }

    public function test_uncertain_creation_is_not_retried_and_can_be_recovered_by_reconciliation(): void
    {
        $this->gateway->failCreate = true;
        $this->postJson('/'.$this->store->id.'/admin/billing/subscribe', ['plan_tier' => 'base', 'cycle' => 'monthly'])->assertStatus(503);
        $this->postJson('/'.$this->store->id.'/admin/billing/subscribe', ['plan_tier' => 'base', 'cycle' => 'monthly'])->assertConflict();
        $this->assertSame(1, $this->gateway->creates);
        $this->assertDatabaseHas('store_subscriptions', ['status' => 'creating', 'gateway_id' => null]);
        $this->artisan('billing:reconcile', ['--tenant' => $this->store->id])->assertSuccessful();
        $this->assertSame('sub_owned', $this->store->fresh()->razorpay_subscription_id);
        $this->assertDatabaseCount('store_subscriptions', 1);
    }

    public function test_cancel_preserves_remaining_paid_period_and_rejects_new_checkout_until_it_ends(): void
    {
        $local = $this->local();
        $this->post('/'.$this->store->id.'/admin/billing/cancel')->assertRedirect();
        $this->assertTrue($local->fresh()->cancel_at_cycle_end);
        $this->assertSame(1, $this->gateway->cancelAtEnd);
        $this->assertTrue(app(PlanAccess::class)->hasPaidAccess($this->store->fresh()));
        $this->postJson('/'.$this->store->id.'/admin/billing/subscribe', ['plan_tier' => 'base', 'cycle' => 'monthly'])->assertConflict();
    }

    public function test_receipts_are_owned_by_the_store_and_only_captured_payments_have_receipts(): void
    {
        $this->local();
        $this->webhook()->assertOk();
        $payment = SubscriptionPayment::firstOrFail();
        $this->get('/'.$this->store->id.'/admin/billing/receipts/'.$payment->id)->assertOk()->assertSee('Payment receipt', false)->assertSee('249.00');
        $other = Tenant::create(['name' => 'Other store']);
        $this->actingAs(User::factory()->create(['tenant_id' => $other->id, 'type' => 'admin']));
        $this->get('/'.$other->id.'/admin/billing/receipts/'.$payment->id)->assertNotFound();
    }

    public function test_superadmin_sees_actual_subscription_collection_and_refund_totals(): void
    {
        $this->local();
        $this->gateway->paymentData['amount_refunded'] = 10000;
        $this->webhook()->assertOk();
        $this->actingAs(User::factory()->create(['type' => 'super_admin', 'tenant_id' => null]));
        $this->get('/superadmin')->assertOk()->assertSee('Subscription collections')->assertSee('249.00')->assertSee('100.00');
        $this->get('/superadmin/tenants/'.$this->store->id)->assertOk()->assertSee('pay_owned')->assertSee('Monthly');
    }

    public function test_failed_charge_records_actual_outstanding_invoice_without_granting_paid_access(): void
    {
        $local = $this->local('pending');
        $this->gateway->remote['status'] = 'pending';
        $this->gateway->remote['paid_count'] = 0;
        $this->gateway->remote['current_end'] = null;
        $this->gateway->paymentData['status'] = 'failed';
        $this->gateway->invoices[0] = array_replace($this->gateway->invoices[0], ['status' => 'issued', 'amount_paid' => 0, 'amount_due' => 24900, 'paid_at' => null]);
        $this->webhook('subscription.pending')->assertOk();
        $this->assertDatabaseHas('subscription_invoices', ['amount_due' => 24900, 'status' => 'issued']);
        $this->assertDatabaseHas('subscription_payments', ['status' => 'failed', 'paid_at' => null]);
        $this->assertFalse(app(PlanAccess::class)->hasPaidAccess($this->store->fresh()));
        $this->get('/'.$this->store->id.'/admin/billing/receipts/'.SubscriptionPayment::first()->id)->assertNotFound();
    }

    public function test_cross_store_invoice_and_mismatched_provider_plan_are_rejected(): void
    {
        $local = $this->local();
        $this->gateway->invoices[0]['subscription_id'] = 'sub_another_store';
        $this->webhook()->assertStatus(502);
        $this->assertDatabaseCount('billing_webhook_events', 0);
        $this->assertDatabaseCount('subscription_payments', 0);
        $this->gateway->invoices[0]['subscription_id'] = 'sub_owned';
        $this->gateway->remote['plan_id'] = 'plan_unexpected';
        $this->webhook()->assertStatus(502);
        $this->assertDatabaseCount('billing_webhook_events', 0);
    }

    public function test_refunds_are_reconciled_without_duplicating_the_original_payment(): void
    {
        $this->local();
        $this->webhook()->assertOk();
        $this->gateway->paymentData['amount_refunded'] = 10000;
        $this->webhook('subscription.updated', 'event_refund')->assertOk();
        $this->assertDatabaseCount('subscription_payments', 1);
        $this->assertDatabaseHas('subscription_payments', ['gateway_id' => 'pay_owned', 'amount_refunded' => 10000]);
    }

    public function test_failed_renewal_cannot_grant_the_providers_advanced_but_unpaid_period(): void
    {
        $local = $this->local('pending');
        $this->store->forceFill(['created_at' => now()->subMonths(2)])->save();
        $this->gateway->remote['status'] = 'pending';
        $this->gateway->invoices[0]['billing_start'] = now()->subMonth()->timestamp;
        $this->gateway->invoices[0]['billing_end'] = now()->subDay()->timestamp;
        app(SubscriptionBilling::class)->sync($local);
        $this->assertTrue($local->fresh()->current_end->isFuture());
        $this->assertFalse(app(PlanAccess::class)->hasPaidAccess($this->store->fresh()));
    }

    public function test_completed_paid_period_cannot_be_extended_by_editing_the_store(): void
    {
        $local = $this->local();
        $this->gateway->remote['current_end'] = now()->subDay()->timestamp;
        $this->gateway->remote['charge_at'] = now()->subDay()->timestamp;
        $this->gateway->invoices[0]['billing_end'] = now()->subDay()->timestamp;
        $this->store->forceFill(['created_at' => now()->subMonth()])->save();
        app(SubscriptionBilling::class)->sync($local);
        $this->store->update(['name' => 'Changed store name']);
        $this->assertFalse(app(PlanAccess::class)->hasPaidAccess($this->store->fresh()));
        $this->assertSame(['cod'], app(\App\Services\CheckoutPaymentPolicy::class)->availableMethods($this->store->fresh()));
        $this->get('/'.$this->store->id.'/admin/orders')->assertRedirect(route('admin.billing', ['tenant' => $this->store->id]));
        $this->postJson('/'.$this->store->id.'/admin/products', [])->assertStatus(402);
    }

    public function test_expired_trials_allow_only_billing_and_paid_base_still_restricts_premium_features(): void
    {
        $this->store->forceFill(['created_at' => now()->subDays(8)])->save();
        $this->get('/'.$this->store->id.'/admin/billing')->assertOk();
        $this->get('/'.$this->store->id.'/admin/orders')->assertRedirect(route('admin.billing', ['tenant' => $this->store->id]));
        $this->postJson('/'.$this->store->id.'/admin/products', [])->assertStatus(402);
        $local = $this->local();
        $this->gateway->remote['plan_id'] = 'plan_owned';
        $local->update(['plan' => 'base']);
        app(SubscriptionBilling::class)->sync($local);
        $this->get('/'.$this->store->id.'/admin/analytics')->assertForbidden();
        $this->postJson('/'.$this->store->id.'/admin/settings/domain', ['domain' => 'example.com'])->assertForbidden();
        $this->assertFalse(app(PlanAccess::class)->allows($this->store->fresh(), 'remove_branding'));
        $this->get('/store/'.$this->store->slug)->assertOk()->assertSee('Powered by');
        $this->store->update(['domain' => 'custom.example']);
        $this->get('https://custom.example/')->assertForbidden();
        $this->store->update(['plan' => 'pro']);
        $this->assertTrue(app(PlanAccess::class)->allows($this->store->fresh(), 'advanced_analytics'));
        $this->get('/store/'.$this->store->slug)->assertOk()->assertDontSee('Powered by');
        $this->get('https://custom.example/')->assertOk();
    }
}

class FakeBillingGateway
{
    public array $remote;

    public array $paymentData;

    public array $invoices;

    public int $creates = 0;

    public int $cancelAtEnd = 0;

    public bool $failCreate = false;

    public object $subscription;

    public object $invoice;

    public object $payment;

    public object $plan;

    public function __construct()
    {
        $this->remote = ['id' => 'sub_owned', 'plan_id' => 'plan_owned', 'status' => 'active', 'paid_count' => 1,
            'current_start' => now()->timestamp, 'current_end' => now()->addMonth()->timestamp, 'charge_at' => now()->addMonth()->timestamp];
        $this->paymentData = ['id' => 'pay_owned', 'invoice_id' => 'inv_owned', 'amount' => 24900, 'currency' => 'INR', 'status' => 'captured', 'method' => 'upi', 'created_at' => now()->timestamp];
        $this->invoices = [['id' => 'inv_owned', 'subscription_id' => 'sub_owned', 'payment_id' => 'pay_owned', 'amount' => 24900, 'amount_paid' => 24900, 'amount_due' => 0, 'currency' => 'INR', 'status' => 'paid', 'billing_start' => now()->timestamp, 'billing_end' => now()->addMonth()->timestamp, 'paid_at' => now()->timestamp]];
        foreach (['subscription', 'invoice', 'payment', 'plan'] as $type) {
            $this->{$type} = new FakeBillingResource($this, $type);
        }
    }
}

class FakeBillingResource
{
    public function __construct(private FakeBillingGateway $gateway, private string $type) {}

    public function all(array $options = []): array
    {
        return ['items' => match ($this->type) {
            'invoice' => $this->gateway->invoices, 'subscription' => [$this->gateway->remote], default => []
        }];
    }

    public function create(array $data): object
    {
        if ($this->type === 'plan') {
            return (object) ['id' => 'plan_owned'];
        }
        $this->gateway->creates++;
        $this->gateway->remote = array_replace($this->gateway->remote, ['status' => 'created', 'paid_count' => 0, 'notes' => $data['notes']]);
        if ($this->gateway->failCreate) {
            throw new \RuntimeException('Unknown network result');
        }

        return (object) $this->gateway->remote;
    }

    public function fetch(string $id): object
    {
        $data = match ($this->type) {
            'subscription' => $this->gateway->remote, 'payment' => $this->gateway->paymentData, 'invoice' => $this->gateway->invoices[0], default => ['period' => 'monthly', 'item' => ['amount' => 24900, 'currency' => 'INR']]
        };

        return new FakeBillingEntity($this->gateway, $data);
    }
}

class FakeBillingEntity
{
    public function __construct(private FakeBillingGateway $gateway, private array $data) {}

    public function toArray(): array
    {
        return $this->data;
    }

    public function cancel(array $data): void
    {
        $this->gateway->cancelAtEnd = $data['cancel_at_cycle_end'];
        if (! $this->gateway->cancelAtEnd) {
            $this->gateway->remote['status'] = 'cancelled';
        }
    }
}
