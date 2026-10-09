<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CommerceSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function store(): Tenant
    {
        return Tenant::create(['name' => 'Test Store', 'theme' => 'template_1']);
    }

    private function user(Tenant $tenant, string $type = 'user'): User
    {
        return User::factory()->create(['tenant_id' => $tenant->id, 'type' => $type]);
    }

    private function product(Tenant $tenant): Product
    {
        $product = Product::create(['title' => 'Rice', 'status' => 'active', 'tenant_id' => $tenant->id]);
        $product->variants()->create(['size' => '1kg', 'price' => 100, 'stock' => 10]);
        $product->variants()->create(['size' => '5kg', 'price' => 450, 'stock' => 3]);
        return $product;
    }

    private function checkout(): array
    {
        return ['name' => 'Customer', 'email' => 'buyer@example.com', 'phone' => '1234567890',
            'address' => 'Main Street', 'city' => 'City', 'state' => 'State', 'pincode' => '123456',
            'delivery_date' => now()->addDay()->toDateString(), 'delivery_time_slot' => 'Morning', 'payment_method' => 'cod'];
    }

    public function test_customers_cannot_access_admin_routes(): void
    {
        $tenant = $this->store();
        $this->actingAs($this->user($tenant))->getJson("/{$tenant->id}/admin/products")->assertForbidden();
    }

    public function test_admin_cannot_access_another_tenant_even_with_query_override(): void
    {
        $own = $this->store();
        $other = $this->store();
        $this->actingAs($this->user($own, 'admin'))
            ->getJson("/{$other->id}/admin/products?tenant_id={$own->id}")->assertForbidden();
    }

    public function test_customer_cannot_select_another_store(): void
    {
        $own = $this->store();
        $other = $this->store();
        $this->actingAs($this->user($own))->getJson("/cart/state?tenant_id={$other->id}")->assertForbidden();
    }

    public function test_subscription_from_another_store_is_rejected_before_provider_call(): void
    {
        $tenant = $this->store();
        $tenant->update(['razorpay_subscription_id' => 'sub_own']);
        $this->actingAs($this->user($tenant, 'admin'))->postJson("/{$tenant->id}/admin/billing/verify", [
            'razorpay_subscription_id' => 'sub_other', 'razorpay_payment_id' => 'pay_test', 'razorpay_signature' => 'signature',
        ])->assertStatus(422);
        $this->assertNotEquals('active', $tenant->fresh()->subscription_status);
    }

    public function test_checkout_uses_selected_variant_price_and_updates_stock(): void
    {
        Mail::fake();
        $this->mock(WhatsAppService::class, function ($mock) { $mock->shouldReceive('sendOrderNotification')->andReturn(true); });
        $tenant = $this->store();
        $user = $this->user($tenant);
        $product = $this->product($tenant);
        $variant = $product->variants()->where('size', '5kg')->first();
        Cart::create(['tenant_id' => $tenant->id, 'user_id' => $user->id, 'product_id' => $product->id,
            'product_variant_id' => $variant->id, 'size' => '5kg', 'quantity' => 2]);
        $this->actingAs($user)->withSession(['active_tenant_id' => $tenant->id])
            ->postJson('/order/place', $this->checkout())->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('orders', ['tenant_id' => $tenant->id, 'total_amount' => 900]);
        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'price' => 450, 'quantity' => 2]);
        $this->assertEquals(1, $variant->fresh()->stock);
        $this->assertDatabaseCount('carts', 0);
    }

    public function test_insufficient_stock_rolls_back_prior_reservations(): void
    {
        $tenant = $this->store();
        $product = $this->product($tenant);
        $this->withSession(['active_tenant_id' => $tenant->id, 'cart' => [
            'small' => ['product_id' => $product->id, 'size' => '1kg', 'type' => 'product', 'quantity' => 1, 'price' => 1],
            'large' => ['product_id' => $product->id, 'size' => '5kg', 'type' => 'product', 'quantity' => 4, 'price' => 1],
        ]])->postJson('/order/place', $this->checkout())->assertUnprocessable();
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertEquals(10, $product->variants()->where('size', '1kg')->first()->stock);
        $this->assertEquals(3, $product->variants()->where('size', '5kg')->first()->stock);
    }

    public function test_guest_checkout_refreshes_price_and_applies_coupon_once(): void
    {
        Mail::fake();
        $this->mock(WhatsAppService::class, function ($mock) { $mock->shouldReceive('sendOrderNotification')->andReturn(true); });
        $tenant = $this->store();
        $product = $this->product($tenant);
        $discount = \App\Models\Discount::create(['tenant_id' => $tenant->id, 'code' => 'SAVE10',
            'type' => 'percentage', 'value' => 10, 'status' => 'active']);
        $product->discounts()->attach($discount);
        $this->withSession(['active_tenant_id' => $tenant->id, 'cart' => [
            'rice' => ['product_id' => $product->id, 'size' => '1kg', 'type' => 'product', 'quantity' => 2, 'price' => 1],
        ]])->postJson('/order/place', $this->checkout())->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('orders', ['subtotal' => 200, 'discount_amount' => 20, 'total_amount' => 180]);
        $this->assertEquals(1, $discount->fresh()->uses_count);
        $order = Order::withoutGlobalScopes()->first();
        $this->assertStringContainsString('Discount:', (new \App\Mail\OrderSummaryMail($order))->render());
    }

    public function test_minimum_order_value_failure_preserves_stock_and_cart(): void
    {
        $tenant = $this->store();
        $tenant->update(['min_order_value' => 500]);
        $product = $this->product($tenant);
        $this->withSession(['active_tenant_id' => $tenant->id, 'cart' => [
            'rice' => ['product_id' => $product->id, 'size' => '1kg', 'type' => 'product', 'quantity' => 1, 'price' => 100],
        ]])->postJson('/order/place', $this->checkout())->assertUnprocessable()->assertSessionHas('cart');
        $this->assertDatabaseCount('orders', 0);
        $this->assertEquals(10, $product->variants()->where('size', '1kg')->first()->stock);
    }

    public function test_analytics_uses_store_orders_and_reports_missing_session_tracking(): void
    {
        $tenant = $this->store();
        Order::create(['tenant_id' => $tenant->id, 'order_number' => 'TEST-1', 'subtotal' => 123,
            'total_amount' => 123, 'customer_name' => 'Buyer', 'customer_email' => 'buyer@example.com',
            'customer_phone' => '1234567890', 'shipping_address' => []]);
        $this->actingAs($this->user($tenant, 'admin'))->get("/{$tenant->id}/admin/analytics/sales")
            ->assertOk()->assertViewHas('metric', 123.0);
        $this->get("/{$tenant->id}/admin/analytics/sessions")->assertOk()
            ->assertSee('Session tracking is not configured');
    }

    public function test_unresolved_tenant_cannot_query_all_products(): void
    {
        $tenant = $this->store();
        $this->product($tenant);
        $this->assertSame(0, Product::count());
    }
}
