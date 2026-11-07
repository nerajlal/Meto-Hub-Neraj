<?php
namespace Tests\Feature;

use App\Models\{Bundle, Collection, Product, Tenant, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Storage};
use Tests\TestCase;

class OfferCollectionTest extends TestCase
{
    use RefreshDatabase;

    private function offer(Tenant $tenant, string $type, string $title): Bundle
    {
        return Bundle::create(['tenant_id' => $tenant->id, 'title' => $title, 'slug' => strtolower(str_replace(' ', '-', $title)), 'type' => $type, 'status' => 'active', 'discount_type' => 'fixed', 'discount_value' => 20, 'min_quantity' => 2]);
    }

    public function test_all_offer_types_share_one_editable_collection_and_are_store_scoped(): void
    {
        Storage::fake('public');
        $tenant = Tenant::create(['name' => 'Our Shop', 'theme' => 'template_31']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $combo = $this->offer($tenant, 'bundle', 'Snack combo');
        $pack = $this->offer($tenant, 'pack', 'Snack pack');
        $pool = $this->offer($tenant, 'pool', 'Mix and match');
        $collection = $combo->collection;
        $this->assertTrue($collection->isOfferCollection());
        $this->assertEquals($collection->id, $pack->collection_id);
        $this->assertEquals($collection->id, $pool->collection_id);
        $this->assertSame(1, Collection::where('system_key', 'offers')->count());
        $product = Product::create(['tenant_id' => $tenant->id, 'title' => 'Eligible crisps', 'status' => 'active']);
        $variant = $product->variants()->create(['size' => '250g', 'price' => 100, 'stock' => 20]);
        foreach ([$combo, $pack, $pool] as $offer) $offer->products()->attach($product->id, ['product_variant_id' => $variant->id, 'quantity' => 1]);
        $other = Tenant::create(['name' => 'Other Shop']);
        $foreign = $this->offer($other, 'pool', 'Private foreign offer');
        $this->assertNotEquals($collection->id, $foreign->collection_id);
        $this->get('/'.$tenant->id.'/admin/collections')->assertOk()->assertSee('3 offers');
        $this->get('/'.$tenant->id.'/admin/collections/'.$collection->id)->assertOk()->assertSee('Mix and match');
        $this->put('/'.$tenant->id.'/admin/collections/'.$collection->id, ['name' => 'Special offers', 'description' => 'Merchant supplied description', 'status' => 1, 'image' => UploadedFile::fake()->image('offers.jpg')])->assertRedirect()->assertSessionHasNoErrors();
        $collection->refresh();
        Storage::disk('public')->assertExists($collection->image);
        $next = $this->offer($tenant, 'pack', 'Another snack pack');
        $this->assertEquals($collection->id, $next->collection_id);
        $this->assertSame('Special offers', $collection->fresh()->name);
        foreach (['template_1', 'template_2', 'template_31'] as $theme) {
            $tenant->update(['theme' => $theme]);
            $this->get('/store/'.$tenant->slug.'/collections?slug='.$collection->slug)->assertOk()
                ->assertSee('Special offers')->assertSee('Merchant supplied description')->assertSee($collection->image)
                ->assertSee('Snack combo')->assertSee('Snack pack')->assertSee('Mix and match')->assertSee('Eligible crisps')
                ->assertDontSee('Private foreign offer');
        }
        $this->delete('/'.$tenant->id.'/admin/collections/'.$collection->id)->assertRedirect();
        $this->assertNotNull($collection->fresh());
        $collection->update(['status' => false]);
        $this->get('/store/'.$tenant->slug.'/collections?slug='.$collection->slug)->assertNotFound();
    }

    public function test_superadmin_store_access_assigns_the_authorized_store_collection(): void
    {
        $tenant = Tenant::create(['name' => 'Managed Shop']);
        $other = Tenant::create(['name' => 'Unrelated Shop']);
        $superadmin = User::factory()->create(['type' => 'super_admin', 'tenant_id' => null]);
        $product = Product::create(['tenant_id' => $tenant->id, 'title' => 'Rice', 'status' => 'active']);
        $variant = $product->variants()->create(['size' => '1kg', 'price' => 100, 'stock' => 10]);
        $this->actingAs($superadmin)->withSession(['active_tenant_id' => $other->id])
            ->post('/'.$tenant->id.'/admin/bundles', ['title' => 'Managed combo', 'status' => 'active', 'discount_type' => 'fixed', 'discount_value' => 10, 'products' => [$product->id], 'variant_ids' => [$variant->id]])
            ->assertRedirect()->assertSessionHasNoErrors();
        $bundle = Bundle::withoutGlobalScopes()->where('title', 'Managed combo')->firstOrFail();
        $this->assertEquals($tenant->id, $bundle->tenant_id);
        $this->assertEquals($tenant->id, Collection::withoutGlobalScopes()->findOrFail($bundle->collection_id)->tenant_id);
        $this->assertFalse(Collection::withoutGlobalScopes()->where('tenant_id', $other->id)->where('system_key', 'offers')->exists());
    }

    public function test_migration_assigns_existing_offers_once_without_moving_products(): void
    {
        $tenant = Tenant::create(['name' => 'Existing Store']);
        $migration = require database_path('migrations/2026_11_07_140000_add_automatic_offer_collections.php');
        $migration->down();
        DB::table('bundles')->insert(['tenant_id' => $tenant->id, 'title' => 'Existing pack', 'slug' => 'existing-pack', 'type' => 'pack', 'status' => 'active', 'discount_type' => 'fixed', 'discount_value' => 10]);
        $migration->up();
        $bundle = Bundle::withoutGlobalScopes()->where('tenant_id', $tenant->id)->firstOrFail();
        $collection = Collection::withoutGlobalScopes()->findOrFail($bundle->collection_id);
        $this->assertEquals($tenant->id, $collection->tenant_id);
        $this->assertTrue($collection->isOfferCollection());
        app(\App\Services\OfferCollection::class)->assign($bundle);
        $this->assertSame(1, Collection::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('system_key', 'offers')->count());
    }
}
