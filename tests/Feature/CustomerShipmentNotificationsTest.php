<?php

namespace Tests\Feature;

use App\Jobs\DeliverCustomerShipmentAlert;
use App\Mail\OrderShippedMail;
use App\Models\{CustomerShipmentAlert, DeliveryPartner, Order, Tenant, User};
use App\Services\CustomerShipmentNotifications;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Mail, Queue};
use Tests\TestCase;

class CustomerShipmentNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $tenant = Tenant::create(['name' => 'Parcel Boutique', 'theme' => 'template_1']);
        $customer = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'user']);
        $partner = DeliveryPartner::create(config('delivery_partners.dtdc') + ['tenant_id' => $tenant->id, 'status' => 1]);
        $order = Order::create(['tenant_id' => $tenant->id, 'user_id' => $customer->id, 'order_number' => 'SHIP-1', 'status' => 'processing',
            'customer_name' => 'Customer', 'customer_email' => 'customer@example.test', 'subtotal' => 100, 'total_amount' => 100, 'shipping_cost' => 0, 'discount_amount' => 0,
            'payment_method' => 'cod', 'payment_status' => 'pending', 'shipping_address' => []]);
        return [$tenant, $customer, $partner, $order];
    }

    public function test_shipment_updates_queue_once_and_customer_can_track_across_themes(): void
    {
        Queue::fake(); Mail::fake();
        [$tenant, $customer, $partner, $order] = $this->fixture();
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $url = '/'.$tenant->id.'/admin/orders/'.$order->id.'/update-status';
        $payload = ['status' => 'shipped', 'tracking_id' => 'TEST/A? B', 'delivery_partner_id' => $partner->id];
        $this->postJson($url, $payload)->assertOk();
        $this->postJson($url, $payload)->assertOk();
        $this->assertDatabaseCount('customer_shipment_alerts', 1);
        $alert = CustomerShipmentAlert::firstOrFail();
        // RefreshDatabase wraps tests in a transaction, so afterCommit does not run here.
        app(CustomerShipmentNotifications::class)->enqueue($alert);
        Queue::assertPushed(DeliverCustomerShipmentAlert::class, 1);
        $service = app(CustomerShipmentNotifications::class);
        $service->deliver($alert->id); $service->deliver($alert->id);
        Mail::assertSent(OrderShippedMail::class, 1);
        Mail::assertSent(OrderShippedMail::class, fn ($mail) => $mail->hasTo('customer@example.test') && str_contains($mail->render(), 'TEST%2FA%3F%20B'));
        $this->actingAs($customer)->withSession(['active_tenant_id' => $tenant->id]);
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $tenant->update(['theme' => $theme]);
            $this->get('/account/orders?store='.$tenant->slug)->assertOk()->assertSee('TEST/A? B')->assertSee('DTDC')->assertSee('Track shipment');
        }
        $otherCustomer = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'user']);
        $this->actingAs($otherCustomer)->get('/account/orders?store='.$tenant->slug)->assertOk()->assertDontSee('TEST/A? B');
        $this->assertSame('https://www.dtdc.com/track-your-shipment/?awb=TEST%2FA%3F%20B', $order->fresh()->trackingUrl());
    }

    public function test_stale_shipments_are_skipped_and_invalid_urls_are_not_exposed(): void
    {
        Mail::fake(); Queue::fake();
        [$tenant, $customer, $partner, $order] = $this->fixture();
        $order->update(['status' => 'shipped', 'tracking_number' => 'OLD', 'delivery_partner_id' => $partner->id]);
        $service = app(CustomerShipmentNotifications::class);
        $service->record($order);
        $alert = CustomerShipmentAlert::firstOrFail();
        $order->update(['tracking_number' => 'NEW']);
        $service->deliver($alert->id);
        Mail::assertNothingSent();
        $this->assertNotNull($alert->fresh()->skipped_at);
        $service->record($order);
        $partner->update(['tracking_url_template' => 'javascript:alert(1)']);
        $this->assertNull($order->fresh()->trackingUrl());
        $order->update(['customer_email' => null]);
        $service->record($order);
        $this->assertDatabaseCount('customer_shipment_alerts', 2);
    }

    public function test_mail_failures_are_retried_without_changing_shipment(): void
    {
        Queue::fake();
        [$tenant, $customer, $partner, $order] = $this->fixture();
        $order->update(['status' => 'shipped', 'tracking_number' => 'RETRY', 'delivery_partner_id' => $partner->id]);
        $service = app(CustomerShipmentNotifications::class);
        $service->record($order);
        $alert = CustomerShipmentAlert::firstOrFail();
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Simulated mail outage'));
        $service->deliver($alert->id);
        $this->assertNull($alert->fresh()->sent_at);
        $this->assertNotNull($alert->fresh()->retry_at);
        $this->assertSame('shipped', $order->fresh()->status);
        Mail::swap(new \Illuminate\Mail\MailManager($this->app));
        Mail::fake();
        $this->travel(6)->minutes();
        $service->deliver($alert->id);
        Mail::assertSent(OrderShippedMail::class, 1);
        $this->assertNotNull($alert->fresh()->sent_at);
    }
}
