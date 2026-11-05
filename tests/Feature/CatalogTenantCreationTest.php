<?php
namespace Tests\Feature;
use App\Models\{Tenant, User, Collection, Product};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CatalogTenantCreationTest extends TestCase {
    use RefreshDatabase;
    public function test_merchant_and_superadmin_creations_are_assigned_to_the_selected_store(): void {
        foreach (['admin', 'super_admin'] as $role) {
            $store = Tenant::create(['name' => 'Store '.$role]);
            $other = Tenant::create(['name' => 'Other '.$role]);
            $actor = User::factory()->create(['type' => $role, 'tenant_id' => $role === 'admin' ? $store->id : null]);
            $this->actingAs($actor)->withSession(['active_tenant_id' => $other->id]);
            $base = '/'.$store->id.'/admin';
            $name = 'Collection '.$role;
            $this->post($base.'/collections', ['name' => $name, 'status' => 1, 'tenant_id' => $other->id])->assertRedirect();
            $collection = Collection::withoutGlobalScopes()->where('name', $name)->firstOrFail();
            $this->assertSame($store->id, (int) $collection->tenant_id);
            $this->get($base.'/collections')->assertOk()->assertSee($name);
            $title = 'Product '.$role;
            $this->post($base.'/products', ['title' => $title, 'status' => 'active', 'tenant_id' => $other->id, 'collection_id' => $collection->id,
                'variants' => [['size' => 'M', 'price' => 499, 'stock' => 5]]])->assertRedirect();
            $product = Product::withoutGlobalScopes()->where('title', $title)->firstOrFail();
            $this->assertSame($store->id, (int) $product->tenant_id);
            $this->get($base.'/products')->assertOk()->assertSee($title);
            if ($role === 'super_admin') {
                $this->get('/'.$other->id.'/admin/products')->assertOk()->assertDontSee($title);
                $this->get('/'.$other->id.'/admin/collections')->assertOk()->assertDontSee($name);
            }
            $this->get('/store/'.$store->slug.'/all-products')->assertOk()->assertSee($title);
        }
        $this->assertSame(0, Product::withoutGlobalScopes()->whereNull('tenant_id')->count());
        $this->assertSame(0, Collection::withoutGlobalScopes()->whereNull('tenant_id')->count());
    }
}
