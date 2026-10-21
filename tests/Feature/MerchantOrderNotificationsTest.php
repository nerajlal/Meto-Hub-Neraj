<?php

namespace Tests\Feature;

use App\Jobs\DeliverMerchantOrderAlert;
use App\Mail\NewStoreOrderMail;
use App\Models\MerchantOrderAlert;
use App\Models\MerchantPushDelivery;
use App\Models\MerchantPushSubscription;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MerchantOrderNotifications;
use App\Services\MerchantWebPush;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MerchantOrderNotificationsTest extends TestCase
{
    use DatabaseMigrations;

    // Exercise actual after-commit dispatch. Fresh in-memory databases avoid legacy down migrations.
    public function runDatabaseMigrations()
    {
        $this->refreshTestDatabase();
        $this->beforeApplicationDestroyed(function () {
            \Illuminate\Foundation\Testing\RefreshDatabaseState::$migrated = false;
        });
    }

    private function owner(Tenant $tenant): User
    {
        return User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
    }

    private function order(Tenant $tenant): Order
    {
        $order = Order::create(['tenant_id' => $tenant->id, 'order_number' => 'ORD-'.uniqid(), 'status' => 'pending', 'payment_method' => 'cod', 'payment_status' => 'pending', 'total_amount' => 350, 'subtotal' => 350, 'customer_name' => 'Private Buyer', 'customer_email' => 'buyer@example.com']);
        $order->items()->create(['name' => 'Blue shirt', 'price' => 350, 'quantity' => 1, 'total' => 350, 'type' => 'product']);

        return $order;
    }

    private function subscribeData(string $suffix = 'one'): array
    {
        $encode = fn ($value) => rtrim(strtr(base64_encode($value), '+/', '-_'), '=');

        return ['endpoint' => 'https://fcm.googleapis.com/fcm/send/'.$suffix, 'keys' => ['p256dh' => $encode(chr(4).str_repeat('k', 64)), 'auth' => $encode(str_repeat('a', 16))]];
    }

    private function device(User $owner): MerchantPushSubscription
    {
        $data = $this->subscribeData((string) $owner->id);

        return MerchantPushSubscription::create(['tenant_id' => $owner->tenant_id, 'user_id' => $owner->id, 'endpoint_hash' => hash('sha256', $data['endpoint']), 'endpoint' => $data['endpoint'], 'public_key' => $data['keys']['p256dh'], 'auth_token' => $data['keys']['auth'], 'origin' => 'https://example.com', 'enabled' => true]);
    }

    public function test_checkout_records_one_alert_and_background_job_only_for_its_owner(): void
    {
        Queue::fake();
        Mail::fake();
        $this->mock(WhatsAppService::class, fn ($m) => $m->shouldReceive('sendOrderNotification')->andReturn(true));
        $tenant = Tenant::create(['name' => 'Store']);
        $owner = $this->owner($tenant);
        $other = Tenant::create(['name' => 'Other']);
        $this->owner($other);
        $product = Product::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'title' => 'Shirt', 'status' => 'active']);
        $variant = $product->variants()->create(['size' => 'M', 'price' => 350, 'stock' => 10]);
        $this->withSession(['active_tenant_id' => $tenant->id, 'cart' => [['type' => 'product', 'product_id' => $product->id, 'variant_id' => $variant->id, 'size' => 'M', 'quantity' => 1, 'price' => 1]]]);
        $this->postJson('/order/place', ['name' => 'Buyer', 'email' => 'buyer@example.com', 'phone' => '1234567890', 'address' => 'Address', 'city' => 'City', 'state' => 'State', 'pincode' => '123456', 'delivery_date' => now()->addDay()->toDateString(), 'delivery_time_slot' => 'Morning', 'payment_method' => 'cod'])->assertOk();
        $alert = MerchantOrderAlert::firstOrFail();
        $this->assertSame($owner->id, $alert->user_id);
        $this->assertSame($tenant->id, $alert->tenant_id);
        Queue::assertPushed(DeliverMerchantOrderAlert::class, 1);
        Mail::assertNotSent(NewStoreOrderMail::class);
        app(MerchantOrderNotifications::class)->record(Order::firstOrFail());
        $this->assertDatabaseCount('merchant_order_alerts', 1);
        Queue::assertPushed(DeliverMerchantOrderAlert::class, 1);
    }

    public function test_email_delivery_is_owner_scoped_renderable_and_not_resent_after_success(): void
    {
        Mail::fake();
        Queue::fake();
        config(['mail.default' => 'smtp']);
        $tenant = Tenant::create(['name' => 'Store', 'currency' => 'INR']);
        $owner = $this->owner($tenant);
        $other = Tenant::create(['name' => 'Other']);
        $this->owner($other);
        $service = app(MerchantOrderNotifications::class);
        $order = $this->order($tenant);
        $alert = $service->record($order);
        $service->deliver($alert->id);
        $service->deliver($alert->id);
        Mail::assertSent(NewStoreOrderMail::class, fn ($mail) => $mail->hasTo($owner->email) && $mail->order->id === $order->id && str_contains($mail->orderUrl, '/'.$tenant->id.'/admin/orders/'.$order->id));
        Mail::assertSent(NewStoreOrderMail::class, 1);
        $this->assertNotNull($alert->fresh()->emailed_at);
        $html = (new NewStoreOrderMail($order, 'Store', $service->orderUrl($alert), 'INR'))->render();
        $this->assertStringContainsString('Blue shirt', $html);
        $this->assertStringContainsString('350.00', $html);
    }

    public function test_mail_failure_does_not_remove_alert_and_retries_without_resending_success(): void
    {
        Queue::fake();
        config(['mail.default' => 'smtp']);
        $tenant = Tenant::create(['name' => 'Store']);
        $this->owner($tenant);
        $service = app(MerchantOrderNotifications::class);
        $alert = $service->record($this->order($tenant));
        $originalMailer = app('mail.manager');
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('SMTP unavailable'));
        $service->deliver($alert->id);
        $this->assertNull($alert->fresh()->emailed_at);
        $this->assertSame(1, $alert->fresh()->email_attempts);
        $this->assertTrue($alert->fresh()->email_retry_at->isFuture());
        Mail::swap($originalMailer);
        Mail::fake();
        $service->deliver($alert->id);
        Mail::assertNothingSent();
        $this->travel(6)->minutes();
        $service->deliver($alert->id);
        Mail::assertSent(NewStoreOrderMail::class, 1);
    }

    public function test_rollbacks_never_leave_notification_records_or_dispatch_jobs(): void
    {
        Queue::fake();
        $tenant = Tenant::create(['name' => 'Store']);
        $this->owner($tenant);
        DB::beginTransaction();
        app(MerchantOrderNotifications::class)->record($this->order($tenant));
        DB::rollBack();
        $this->assertDatabaseCount('merchant_order_alerts', 0);
        Queue::assertNothingPushed();
    }

    public function test_bell_feed_read_and_links_are_private_and_available_after_expiry(): void
    {
        Queue::fake();
        $tenant = Tenant::create(['name' => 'Store']);
        $owner = $this->owner($tenant);
        $other = Tenant::create(['name' => 'Other']);
        $otherOwner = $this->owner($other);
        $service = app(MerchantOrderNotifications::class);
        $own = $service->record($this->order($tenant));
        $foreign = $service->record($this->order($other));
        $this->actingAs($owner);
        $prefix = '/'.$tenant->id.'/admin/notifications';
        $this->getJson($prefix.'/feed')->assertOk()->assertJsonPath('unread_count', 1)->assertJsonCount(1, 'alerts')->assertJsonMissing(['id' => $foreign->id])->assertHeader('Cache-Control', 'no-store, private');
        $this->get($prefix)->assertOk()->assertSee('Device notifications')->assertSee('New order');
        $this->postJson($prefix.'/'.$foreign->id.'/read')->assertNotFound();
        $this->get($prefix.'/'.$foreign->id.'/open')->assertNotFound();
        $this->getJson('/'.$other->id.'/admin/notifications/feed')->assertForbidden();
        $this->get($prefix.'/'.$own->id.'/open')->assertRedirect();
        $this->assertNull($own->fresh()->read_at);
        $tenant->forceFill(['created_at' => now()->subDays(10)])->save();
        $this->postJson($prefix.'/'.$own->id.'/read')->assertStatus(402);
        $this->getJson($prefix.'/feed')->assertStatus(402);
        $this->assertNull($own->fresh()->read_at);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'user']))->getJson($prefix.'/feed')->assertForbidden();
    }

    public function test_push_subscription_validation_encryption_owner_scope_and_removal(): void
    {
        config(['order_notifications.vapid.public_key' => 'public', 'order_notifications.vapid.private_key' => 'private']);
        $tenant = Tenant::create(['name' => 'Store']);
        $owner = $this->owner($tenant);
        $this->actingAs($owner);
        $prefix = '/'.$tenant->id.'/admin/notifications';
        $data = $this->subscribeData();
        $this->postJson($prefix.'/subscribe', $data)->assertOk();
        $device = MerchantPushSubscription::firstOrFail();
        $this->assertSame($data['endpoint'], $device->endpoint);
        $this->assertNotSame($data['endpoint'], $device->getRawOriginal('endpoint'));
        $this->assertArrayNotHasKey('endpoint', $device->toArray());
        $this->postJson($prefix.'/subscribe', $data)->assertOk();
        $this->assertDatabaseCount('merchant_push_subscriptions', 1);
        $this->postJson($prefix.'/device-status', ['endpoint' => $data['endpoint']])->assertJsonPath('enabled', true);
        foreach (['http://fcm.googleapis.com/a', 'https://127.0.0.1/a', 'https://attacker.example/a', 'https://user@fcm.googleapis.com/a', 'https://fcm.googleapis.com.attacker.example/a'] as $url) {
            $this->postJson($prefix.'/subscribe', array_replace($data, ['endpoint' => $url]))->assertUnprocessable();
        }
        $this->postJson($prefix.'/subscribe', ['endpoint' => $data['endpoint'], 'keys' => ['p256dh' => 'bad', 'auth' => 'bad']])->assertUnprocessable();
        $this->postJson($prefix.'/unsubscribe', ['endpoint' => $data['endpoint']])->assertOk();
        $this->assertFalse($device->fresh()->enabled);
    }

    public function test_push_delivery_is_generic_retries_and_expired_devices_are_disabled(): void
    {
        Queue::fake();
        Mail::fake();
        config(['mail.default' => 'smtp']);
        $tenant = Tenant::create(['name' => 'Store']);
        $owner = $this->owner($tenant);
        $device = $this->device($owner);
        $service = app(MerchantOrderNotifications::class);
        $alert = $service->record($this->order($tenant));
        $this->mock(MerchantWebPush::class, function ($mock) {
            $mock->shouldReceive('configured')->andReturn(true);
            $mock->shouldReceive('send')->once()->withArgs(function ($device, $payload) {
                return ! str_contains(json_encode($payload), 'Private Buyer') && ! str_contains(json_encode($payload), 'buyer@example.com') && str_contains($payload['url'], '/admin/notifications/');
            })->andReturn(['success' => true, 'expired' => false]);
        });
        $service->deliver($alert->id);
        $service->deliver($alert->id);
        $this->assertNotNull(MerchantPushDelivery::firstOrFail()->sent_at);
        $next = $service->record($this->order($tenant));
        $this->mock(MerchantWebPush::class, function ($mock) {
            $mock->shouldReceive('configured')->andReturn(true);
            $mock->shouldReceive('send')->once()->andReturn(['success' => false, 'expired' => true]);
        });
        $service->deliver($next->id);
        $this->assertFalse($device->fresh()->enabled);
        $this->assertNotNull($next->deliveries()->firstOrFail()->failed_at);
    }

    public function test_endpoint_rebinding_never_sends_previous_owners_order_to_new_account(): void
    {
        Queue::fake();
        Mail::fake();
        config(['mail.default' => 'smtp']);
        $tenant = Tenant::create(['name' => 'Store']);
        $owner = $this->owner($tenant);
        $device = $this->device($owner);
        $service = app(MerchantOrderNotifications::class);
        $alert = $service->record($this->order($tenant));
        $other = Tenant::create(['name' => 'Other']);
        $otherOwner = $this->owner($other);
        $device->update(['user_id' => $otherOwner->id, 'tenant_id' => $other->id]);
        $this->mock(MerchantWebPush::class, fn ($mock) => $mock->shouldReceive('send')->never()->shouldReceive('configured')->andReturn(true));
        $service->deliver($alert->id);
        $this->assertNotNull($alert->deliveries()->firstOrFail()->failed_at);
    }

    public function test_order_notification_login_resumes_only_the_owners_own_admin_path(): void
    {
        $tenant = Tenant::create(['name' => 'Store']);
        $owner = $this->owner($tenant);
        $password = 'secure-password';
        $owner->update(['password' => $password]);
        $target = 'http://localhost/'.$tenant->id.'/admin/notifications/1/open';
        $this->withSession(['url.intended' => $target])->post('/admin/login', ['email' => $owner->email, 'password' => $password])->assertRedirect($target);
        \Illuminate\Support\Facades\Auth::logout();
        $this->withSession(['url.intended' => 'https://attacker.example/'.$tenant->id.'/admin/orders/1'])->post('/admin/login', ['email' => $owner->email, 'password' => $password])->assertRedirect(route('admin.dashboard', ['tenant' => $tenant->id]));
        \Illuminate\Support\Facades\Auth::logout();
        $this->withSession(['url.intended' => 'http://localhost/999/admin/orders/1'])->post('/admin/login', ['email' => $owner->email, 'password' => $password])->assertRedirect(route('admin.dashboard', ['tenant' => $tenant->id]));
        \Illuminate\Support\Facades\Auth::logout();
        $this->withSession(['url.intended' => 'http://localhost/'.$tenant->id.'/admin/%2e%2e/%2e%2e/999/admin/orders/1'])
            ->post('/admin/login', ['email' => $owner->email, 'password' => $password])
            ->assertRedirect(route('admin.dashboard', ['tenant' => $tenant->id]));
    }

    public function test_rejected_checkout_never_creates_an_owner_alert(): void
    {
        Queue::fake();
        $tenant = Tenant::create(['name' => 'Store']);
        $this->owner($tenant);
        $this->withSession(['active_tenant_id' => $tenant->id])->postJson('/order/place', [])->assertUnprocessable();
        $this->assertDatabaseCount('merchant_order_alerts', 0);
        Queue::assertNothingPushed();
    }

    public function test_invalid_queue_configuration_keeps_the_alert_and_never_sends_in_the_request(): void
    {
        Queue::fake();
        Mail::fake();
        config(['order_notifications.queue_connection' => 'sync']);
        $tenant = Tenant::create(['name' => 'Store']);
        $this->owner($tenant);
        $alert = app(MerchantOrderNotifications::class)->record($this->order($tenant));
        $this->assertDatabaseCount('merchant_order_alerts', 1);
        $this->assertNull($alert->fresh()->queued_at);
        Queue::assertNothingPushed();
        Mail::assertNothingSent();
        config(['order_notifications.queue_connection' => 'database']);
        $this->artisan('notifications:deliver-orders')->assertSuccessful();
        Queue::assertPushed(DeliverMerchantOrderAlert::class, 1);
    }

    public function test_operator_retry_preserves_successful_email_channels(): void
    {
        Queue::fake();
        Mail::fake();
        config(['mail.default' => 'smtp']);
        $tenant = Tenant::create(['name' => 'Store']);
        $this->owner($tenant);
        $service = app(MerchantOrderNotifications::class);
        $alert = $service->record($this->order($tenant));
        $alert->update(['email_attempts' => 6, 'email_retry_at' => now()->addDay(), 'queued_at' => null]);
        $this->artisan('notifications:retry-order', ['alert' => $alert->id])->assertSuccessful();
        $this->assertSame(0, $alert->fresh()->email_attempts);
        $service->deliver($alert->id);
        Mail::assertSent(NewStoreOrderMail::class, 1);
        $this->artisan('notifications:retry-order', ['alert' => $alert->id])->assertSuccessful();
        $service->deliver($alert->id);
        Mail::assertSent(NewStoreOrderMail::class, 1);
    }

    public function test_recovery_command_requeues_due_alerts_and_does_not_duplicate_active_jobs(): void
    {
        Queue::fake();
        $tenant = Tenant::create(['name' => 'Store']);
        $this->owner($tenant);
        $alert = app(MerchantOrderNotifications::class)->record($this->order($tenant));
        Queue::assertPushed(DeliverMerchantOrderAlert::class, 1);
        $this->artisan('notifications:deliver-orders')->assertSuccessful();
        Queue::assertPushed(DeliverMerchantOrderAlert::class, 1);
        $this->travel(11)->minutes();
        $this->artisan('notifications:deliver-orders')->assertSuccessful();
        Queue::assertPushed(DeliverMerchantOrderAlert::class, 2);
    }
}
