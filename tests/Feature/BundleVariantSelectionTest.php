<?php
namespace Tests\Feature;

use App\Models\{Tenant, User, Product, Bundle};
use App\Services\CheckoutCartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BundleVariantSelectionTest extends TestCase
{
    use RefreshDatabase;

    private function setupStore(): array
    {
        $tenant = Tenant::create(['name' => 'Bundle Shop']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $product = Product::create(['tenant_id' => $tenant->id, 'title' => 'Chips', 'status' => 'active']);
        $small = $product->variants()->create(['size' => '100g', 'price' => 100, 'stock' => 20]);
        $large = $product->variants()->create(['size' => '250g', 'price' => 200, 'stock' => 10]);
        return [$tenant, $product, $small, $large];
    }

    public function test_selected_variants_and_quantities_survive_create_edit_and_checkout(): void
    {
        [$tenant, $product, $small, $large] = $this->setupStore();
        $base = '/'.$tenant->id.'/admin/bundles';
        $this->get($base.'/create')->assertOk()->assertSee('data-bundle-picker', false);
        $payload = ['title' => 'Snack Duo', 'status' => 'active', 'discount_type' => 'fixed', 'discount_value' => 50,
            'products' => [$product->id, $product->id], 'variant_ids' => [$small->id, $large->id], 'quantities' => [1, 2]];
        $this->post($base, $payload)->assertSessionHasNoErrors()->assertRedirect();
        $bundle = Bundle::firstOrFail();
        $this->assertCount(2, $bundle->products);
        $this->assertEquals(500, $bundle->base_price);
        $this->assertEquals(450, $bundle->total_price);
        $this->assertFalse($bundle->is_out_of_stock);
        $this->get($base.'/'.$bundle->id.'/edit')->assertOk()->assertSee('data-bundle-picker', false)->assertSee('250g');
        $this->put($base.'/'.$bundle->id, array_replace($payload, ['quantities' => [2, 2]]))->assertSessionHasNoErrors()->assertRedirect();
        $bundle->refresh();
        $this->assertEquals(550, $bundle->total_price);
        $prepared = DB::transaction(fn () => app(CheckoutCartService::class)->prepare([['type' => 'bundle', 'bundle_id' => $bundle->id, 'quantity' => 2]]));
        $this->assertEquals(550, $prepared[0]['price']);
        $this->assertEquals(16, $small->fresh()->stock);
        $this->assertEquals(6, $large->fresh()->stock);
    }

    public function test_foreign_products_mismatched_and_missing_variants_are_rejected_without_writes(): void
    {
        [$tenant, $product, $small, $large] = $this->setupStore();
        $foreign = Tenant::create(['name' => 'Another Shop']);
        $other = Product::create(['tenant_id' => $foreign->id, 'title' => 'Other chips', 'status' => 'active']);
        $variant = $other->variants()->create(['size' => 'Other', 'price' => 10, 'stock' => 10]);
        $base = ['title' => 'Invalid', 'discount_type' => 'fixed', 'discount_value' => 0, 'products' => [$product->id]];
        foreach ([$base, $base + ['variant_ids' => [$variant->id]], array_replace($base, ['products' => [$other->id], 'variant_ids' => [$variant->id]]), $base + ['variant_ids' => [$small->id], 'quantities' => [0]]] as $payload) {
            $this->postJson('/'.$tenant->id.'/admin/bundles', $payload)->assertUnprocessable();
        }
        $this->assertSame(0, Bundle::withoutGlobalScopes()->count());
    }

    public function test_stock_checks_use_selected_variant_and_aggregate_duplicate_lines(): void
    {
        [$tenant, $product, $small, $large] = $this->setupStore();
        $bundle = Bundle::create(['tenant_id' => $tenant->id, 'title' => 'Large only', 'slug' => 'large-only', 'type' => 'bundle', 'status' => 'active', 'discount_type' => 'fixed', 'discount_value' => 0]);
        $large->update(['stock' => 1]);
        $bundle->products()->attach($product->id, ['product_variant_id' => $large->id, 'quantity' => 1]);
        $this->assertFalse($bundle->fresh()->is_out_of_stock);
        $bundle->products()->attach($product->id, ['product_variant_id' => $large->id, 'quantity' => 1]);
        $this->assertTrue($bundle->fresh()->is_out_of_stock);
        $product->update(['continue_selling_when_out_of_stock' => true]);
        $this->assertFalse($bundle->fresh()->is_out_of_stock);
    }
}
