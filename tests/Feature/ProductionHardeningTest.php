<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Order;
use App\Models\Product;
use App\Models\Reel;
use App\Models\Tenant;
use App\Models\User;
use App\Services\InstagramPostUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProductionHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function merchant(): Tenant
    {
        Http::preventStrayRequests();
        Mail::fake();
        $tenant = Tenant::create(['name' => 'Test Store']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));

        return $tenant;
    }

    private function product(Tenant $tenant, int $stock = 5): Product
    {
        $product = Product::create(['tenant_id' => $tenant->id, 'title' => 'Test shirt', 'status' => 'active']);
        $product->variants()->create(['size' => 'M', 'price' => 100, 'stock' => $stock, 'sku' => 'SHIRT-M']);

        return $product;
    }

    private function payload(Product $product): array
    {
        return ['customer_type' => 'new', 'customer_name' => 'Test Customer', 'customer_email' => 'buyer@example.test', 'customer_phone' => '9000000000',
            'shipping_address_line1' => 'Test address', 'shipping_city' => 'Kochi', 'shipping_state' => 'Kerala', 'shipping_country' => 'India',
            'payment_status' => 'pending', 'payment_method' => 'cod', 'status' => 'pending', 'shipping_cost' => 0, 'discount_amount' => 0,
            'items' => [['product_id' => $product->id, 'variant_id' => $product->variants()->first()->id, 'quantity' => 2, 'price' => 75]]];
    }

    public function test_manual_order_reserves_stock_and_preserves_manual_selling_price(): void
    {
        $tenant = $this->merchant();
        $product = $this->product($tenant);
        $variant = $product->variants()->first();
        $this->postJson("/{$tenant->id}/admin/orders", $this->payload($product))->assertRedirect();
        $this->assertEquals(3, $variant->fresh()->stock);
        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'quantity' => 2, 'price' => 75, 'total' => 150]);
        $this->assertDatabaseHas('orders', ['tenant_id' => $tenant->id, 'total_amount' => 150]);
        $this->assertTrue(Order::first()->items->first()->options['manual_stock']['reserved']);
        Mail::assertNothingSent();
    }

    public function test_manual_order_rejects_other_store_customers_and_mismatched_variants(): void
    {
        $tenant = $this->merchant();
        $product = $this->product($tenant);
        $other = Tenant::create(['name' => 'Other store']);
        $foreignProduct = $this->product($other);
        $customer = User::factory()->create(['tenant_id' => $other->id, 'type' => 'user']);
        $payload = $this->payload($product);
        $this->postJson("/{$tenant->id}/admin/orders", array_replace($payload, ['customer_type' => 'existing', 'user_id' => $customer->id]))->assertUnprocessable()->assertJsonValidationErrors('user_id');
        foreach ([$foreignProduct, $this->product($tenant)] as $wrong) {
            $payload['items'][0]['variant_id'] = $wrong->variants()->first()->id;
            $this->postJson("/{$tenant->id}/admin/orders", $payload)->assertUnprocessable()->assertJsonValidationErrors('items');
        }
        $this->assertDatabaseCount('orders', 0);
        $this->assertEquals(5, $product->variants()->first()->stock);
    }

    public function test_duplicate_lines_and_later_item_failures_roll_back_stock_and_order(): void
    {
        $tenant = $this->merchant();
        $product = $this->product($tenant, 3);
        $payload = $this->payload($product);
        $payload['items'][] = $payload['items'][0];
        $this->postJson("/{$tenant->id}/admin/orders", $payload)->assertUnprocessable();
        $this->assertEquals(3, $product->variants()->first()->stock);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertDatabaseCount('merchant_order_alerts', 0);
    }

    public function test_cancellation_is_idempotent_and_reactivation_reserves_stock_again(): void
    {
        $tenant = $this->merchant();
        $product = $this->product($tenant);
        $this->postJson("/{$tenant->id}/admin/orders", $this->payload($product))->assertRedirect();
        $order = Order::firstOrFail();
        $endpoint = "/{$tenant->id}/admin/orders/{$order->id}/update-status";
        $this->postJson($endpoint, ['status' => 'cancelled'])->assertOk();
        $this->postJson($endpoint, ['status' => 'cancelled'])->assertOk();
        $this->assertEquals(5, $product->variants()->first()->stock);
        $this->postJson($endpoint, ['status' => 'processing'])->assertOk();
        $this->postJson($endpoint, ['status' => 'shipped'])->assertOk();
        $this->assertEquals(3, $product->variants()->first()->stock);
        $this->postJson($endpoint, ['status' => 'cancelled'])->assertOk();
        $product->variants()->update(['stock' => 1]);
        $this->postJson($endpoint, ['status' => 'processing'])->assertUnprocessable();
        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertEquals(1, $product->variants()->first()->stock);
    }

    public function test_cancelled_creation_does_not_reserve_and_legacy_orders_do_not_invent_stock(): void
    {
        $tenant = $this->merchant();
        $product = $this->product($tenant);
        $this->postJson("/{$tenant->id}/admin/orders", array_replace($this->payload($product), ['status' => 'cancelled']))->assertRedirect();
        $order = Order::firstOrFail();
        $this->assertEquals(5, $product->variants()->first()->stock);
        $order->items()->first()->update(['options' => null]);
        $this->postJson("/{$tenant->id}/admin/orders/{$order->id}/update-status", ['status' => 'processing'])->assertOk();
        $this->postJson("/{$tenant->id}/admin/orders/{$order->id}/update-status", ['status' => 'cancelled'])->assertOk();
        $this->assertEquals(5, $product->variants()->first()->stock);
    }

    public function test_instagram_urls_are_canonical_and_other_hosts_schemes_and_credentials_rejected(): void
    {
        $this->assertSame('https://www.instagram.com/reel/AbC_123-/', InstagramPostUrl::normalize('https://instagram.com/reels/AbC_123-/?igsh=test'));
        foreach (['javascript:alert(1)', 'http://instagram.com/reel/abc', 'https://evil.test/reel/abc', 'https://instagram.com.evil.test/p/abc',
            'https://user@instagram.com/p/abc', 'https://instagram.com:444/p/abc', 'https://instagram.com/profile/', 'https://instagram.com/p/abc/embed'] as $url) {
            $this->assertNull(InstagramPostUrl::normalize($url), $url);
        }
    }

    public function test_reel_create_update_and_reorder_reject_other_store_records(): void
    {
        $tenant = $this->merchant();
        $product = $this->product($tenant);
        $other = Tenant::create(['name' => 'Other store']);
        $foreignProduct = $this->product($other);
        $foreignCollection = Collection::create(['tenant_id' => $other->id, 'name' => 'Private collection', 'slug' => 'private', 'status' => true]);
        $payload = ['instagram_url' => 'https://instagram.com/reel/AbC123/?igsh=tracking', 'link_type' => 'product', 'product_id' => $product->id, 'status' => true];
        $this->postJson("/{$tenant->id}/admin/settings/reels", $payload)->assertRedirect();
        $reel = Reel::firstOrFail();
        $this->assertSame('https://www.instagram.com/reel/AbC123/', $reel->instagram_url);
        foreach (["/{$tenant->id}/admin/settings/reels", "/{$tenant->id}/admin/settings/reels/{$reel->id}"] as $endpoint) {
            $method = str_ends_with($endpoint, '/reels') ? 'POST' : 'PUT';
            $this->json($method, $endpoint, array_replace($payload, ['product_id' => $foreignProduct->id]))->assertUnprocessable();
            $this->json($method, $endpoint, ['instagram_url' => $payload['instagram_url'], 'link_type' => 'collection', 'collection_id' => $foreignCollection->id])->assertUnprocessable();
            $this->json($method, $endpoint, array_replace($payload, ['instagram_url' => 'https://evil.test/p/abc']))->assertUnprocessable();
        }
        $foreignReel = Reel::create(['tenant_id' => $other->id, 'instagram_url' => $payload['instagram_url'], 'link_type' => 'product', 'product_id' => $foreignProduct->id, 'status' => true, 'order' => 10]);
        $this->postJson("/{$tenant->id}/admin/settings/reels/reorder", ['order' => [$reel->id, $foreignReel->id]])->assertUnprocessable();
        $this->assertDatabaseHas('reels', ['id' => $foreignReel->id, 'order' => 10]);
    }

    public function test_retired_videos_cannot_write_and_old_links_go_to_reels(): void
    {
        $tenant = $this->merchant();
        $this->get("/{$tenant->id}/admin/settings/videos")->assertRedirect(route('admin.settings.reels'));
        $this->postJson("/{$tenant->id}/admin/settings/videos", ['video1_url' => 'https://example.test/video'])->assertStatus(410);
        $this->assertDatabaseCount('tenant_videos', 0);
    }

    public function test_demo_request_mail_failure_does_not_return_false_success(): void
    {
        Mail::shouldReceive('raw')->once()->andThrow(new \RuntimeException('Test mail failure'));
        $this->postJson('/demo-request', ['email' => 'buyer@example.test', 'business_name' => 'Test store'])->assertStatus(503)->assertJson(['success' => false]);
    }

    public function test_delivery_staff_cancellation_restores_tracked_manual_stock_once(): void
    {
        $tenant = $this->merchant();
        $product = $this->product($tenant);
        $this->postJson("/{$tenant->id}/admin/orders", $this->payload($product))->assertRedirect();
        $staff = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'delivery_boy']);
        $order = Order::firstOrFail();
        $order->update(['delivery_boy_id' => $staff->id]);
        $this->actingAs($staff);
        $this->post("/{$tenant->id}/delivery/orders/{$order->id}/status", ['status' => 'cancelled'])->assertRedirect();
        $this->post("/{$tenant->id}/delivery/orders/{$order->id}/status", ['status' => 'cancelled'])->assertRedirect();
        $this->assertEquals(5, $product->variants()->first()->stock);
    }

    public function test_manual_stock_respects_explicit_overselling_setting_and_default_variant(): void
    {
        $tenant = $this->merchant();
        $product = $this->product($tenant, 0);
        $payload = $this->payload($product);
        unset($payload['items'][0]['variant_id']);
        $this->postJson("/{$tenant->id}/admin/orders", $payload)->assertUnprocessable();
        $product->update(['continue_selling_when_out_of_stock' => true]);
        $this->postJson("/{$tenant->id}/admin/orders", $payload)->assertRedirect();
        $this->assertEquals(-2, $product->variants()->first()->stock);
    }

    public function test_unsafe_legacy_reel_urls_are_not_embedded_in_any_theme(): void
    {
        $tenant = $this->merchant();
        $product = $this->product($tenant);
        Reel::create(['tenant_id' => $tenant->id, 'instagram_url' => 'https://untrusted.example.test/reel/abc', 'link_type' => 'product', 'product_id' => $product->id, 'status' => true]);
        Reel::create(['tenant_id' => $tenant->id, 'instagram_url' => 'https://instagram.com/reel/Valid123/?igsh=tracking', 'link_type' => 'product', 'product_id' => $product->id, 'status' => true]);
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $tenant->update(['theme' => $theme]);
            $this->get($tenant->storefrontUrl())->assertOk()->assertDontSee('https://untrusted.example.test')->assertSee('https://www.instagram.com/reel/Valid123/embed', false);
        }
    }

    public function test_production_preflight_blocks_missing_push_keys(): void
    {
        config(['order_notifications.vapid.public_key' => null, 'order_notifications.vapid.private_key' => null]);
        $this->artisan('app:preflight')->expectsOutput('Web Push needs both public and private VAPID keys.')->assertExitCode(1);
    }
}
