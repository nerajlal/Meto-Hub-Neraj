<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderDocumentsTest extends TestCase
{
    use DatabaseMigrations;

    public function runDatabaseMigrations()
    {
        $this->refreshTestDatabase();
        $this->beforeApplicationDestroyed(function () {
            \Illuminate\Foundation\Testing\RefreshDatabaseState::$migrated = false;
        });
    }

    private function setupOrder(): array
    {
        $tenant = Tenant::create(['name' => 'Test Boutique', 'contact_address' => 'Actual sender street', 'contact_phone' => '1234567890', 'currency' => 'INR']);
        $owner = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $this->actingAs($owner);
        $order = Order::create(['tenant_id' => $tenant->id, 'order_number' => 'ORD-DOC-1', 'status' => 'pending', 'payment_method' => 'cod', 'payment_status' => 'pending', 'subtotal' => 600, 'shipping_cost' => 50, 'discount_amount' => 20, 'tax_amount' => 30, 'total_amount' => 660, 'customer_name' => 'Buyer', 'shipping_address' => ['address' => 'Buyer street', 'city' => 'Kochi', 'zip' => '682001']]);
        $order->items()->create(['name' => 'Blue shirt', 'size' => 'M', 'price' => 300, 'quantity' => 2, 'total' => 600, 'type' => 'product']);
        return [$tenant, $owner, $order];
    }

    public function test_payment_record_is_audited_and_repeated_requests_preserve_original_record(): void
    {
        [$tenant, $owner, $order] = $this->setupOrder();
        $url = "/{$tenant->id}/admin/orders/{$order->id}/mark-paid";
        $this->post($url)->assertRedirect();
        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame($owner->id, $order->payment_recorded_by);
        $timestamp = $order->payment_recorded_at->toISOString();
        $this->travel(2)->hours();
        $this->post($url)->assertRedirect();
        $this->assertSame($timestamp, $order->fresh()->payment_recorded_at->toISOString());
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_documents_use_store_details_and_recorded_order_amounts(): void
    {
        [$tenant, , $order] = $this->setupOrder();
        $url = "/{$tenant->id}/admin/orders/{$order->id}/print";
        $this->get($url)->assertOk()->assertSee('SHIPPING LABEL')->assertSee('Actual sender street')->assertSee('Buyer street')->assertSee('660.00')->assertDontSee('Online Fulfillment Centre');
        $this->get($url.'?document=invoice')->assertOk()->assertSee('ORDER INVOICE')->assertSee('Blue shirt')->assertSee('600.00')->assertSee('660.00')->assertSee('Discount')->assertHeader('Cache-Control', 'no-store, private');
        $this->get($url.'?document=invalid')->assertNotFound();
    }

    public function test_cancelled_order_cannot_be_marked_paid_and_foreign_orders_are_inaccessible(): void
    {
        [$tenant, , $order] = $this->setupOrder();
        $order->update(['status' => 'cancelled']);
        $base = "/{$tenant->id}/admin/orders/{$order->id}";
        $this->postJson($base.'/mark-paid')->assertStatus(422);
        $other = Tenant::create(['name' => 'Other Store']);
        $order->update(['tenant_id' => $other->id]);
        $this->post($base.'/mark-paid')->assertNotFound();
        $this->get($base.'/print')->assertNotFound();
    }

    public function test_status_update_rejects_invalid_status_and_foreign_delivery_partner(): void
    {
        [$tenant, , $order] = $this->setupOrder();
        $url = "/{$tenant->id}/admin/orders/{$order->id}/update-status";
        $this->postJson($url, ['status' => 'arbitrary'])->assertUnprocessable();
        $this->postJson($url, ['status' => 'shipped', 'delivery_partner_id' => 999999])->assertUnprocessable();
        $this->postJson($url, ['status' => 'processing'])->assertOk();
        $this->assertSame('processing', $order->fresh()->status);
    }
}
