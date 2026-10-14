<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontSlugsTest extends TestCase
{
    use RefreshDatabase;

    public function test_slugs_are_unique_and_stable_when_business_name_changes(): void
    {
        $first = Tenant::create(['name' => 'Fresh Grocery', 'theme' => 'template_1']);
        $second = Tenant::create(['name' => 'Fresh Grocery', 'theme' => 'template_1']);
        $this->assertSame('fresh-grocery', $first->slug);
        $this->assertNotSame($first->slug, $second->slug);
        $first->update(['name' => 'New Grocery Name']);
        $this->assertSame('fresh-grocery', $first->fresh()->slug);
        $this->assertStringEndsWith('/store/fresh-grocery', $first->storefrontUrl());
    }

    public function test_slug_routes_render_all_themes_and_navigation_uses_slug(): void
    {
        $tenant = Tenant::create(['name' => 'Fresh Grocery', 'theme' => 'template_1']);
        $product = Product::create(['title' => 'Rice', 'status' => 'active', 'tenant_id' => $tenant->id]);
        $product->variants()->create(['size' => '1kg', 'price' => 100, 'stock' => 10]);
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $tenant->update(['theme' => $theme]);
            $this->withSession(['cart' => ['rice' => ['product_id' => $product->id, 'size' => '1kg',
                'type' => 'product', 'quantity' => 1, 'price' => 100, 'name' => 'Rice']]]);
            foreach (['', '/all-products', '/product?id='.$product->id, '/checkout'] as $path) {
                $this->get('/store/fresh-grocery'.$path)->assertOk()
                    ->assertSee('data-storefront-theme="'.$theme.'"', false)
                    ->assertSee('/store/fresh-grocery/all-products', false);
            }
        }
    }

    public function test_legacy_link_and_slug_query_resolve_the_same_store(): void
    {
        $tenant = Tenant::create(['name' => 'Fresh Grocery', 'theme' => 'template_2']);
        $this->get('/v1?tenant_id='.$tenant->id)->assertOk()->assertSee('/store/fresh-grocery/all-products', false);
        $this->get('/v1?store=fresh-grocery')->assertOk()->assertSessionHas('active_tenant_id', $tenant->id);
        $this->get('/store/unknown-store')->assertNotFound();
        $this->get('/v1?store[]=bad')->assertNotFound();
    }

    public function test_slug_route_wins_over_query_id_and_switching_store_clears_guest_cart(): void
    {
        $first = Tenant::create(['name' => 'First Store', 'theme' => 'template_1']);
        $second = Tenant::create(['name' => 'Second Store', 'theme' => 'template_2']);
        $this->withSession(['active_tenant_id' => $first->id, 'cart' => ['old' => ['quantity' => 1]]])
            ->get('/store/second-store?tenant_id='.$first->id)->assertOk()
            ->assertSessionHas('active_tenant_id', $second->id)->assertSessionMissing('cart');
    }

    public function test_authenticated_customer_cannot_use_slug_to_access_another_tenant(): void
    {
        $own = Tenant::create(['name' => 'Own Store', 'theme' => 'template_1']);
        Tenant::create(['name' => 'Other Store', 'theme' => 'template_1']);
        $user = User::factory()->create(['tenant_id' => $own->id, 'type' => 'user']);
        $this->actingAs($user)->getJson('/store/other-store')->assertForbidden();
        $this->getJson('/cart/state?store=other-store')->assertForbidden();
    }

    public function test_admin_publish_and_preview_links_use_the_slug(): void
    {
        $tenant = Tenant::create(['name' => 'Fresh Grocery', 'theme' => 'template_1']);
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $this->actingAs($user)->get('/'.$tenant->id.'/admin/settings/theme')->assertOk()
            ->assertSee('/store/fresh-grocery?preview=1', false);
        $this->get('/'.$tenant->id.'/admin/settings/domain')->assertOk()->assertSee($tenant->storefrontUrl(), false);
        $this->get('/store/fresh-grocery?preview=1&preview_theme=template_4')->assertOk()
            ->assertSee('name="store" value="fresh-grocery"', false)->assertSee('preview_theme=template_4');
        $this->assertSame('template_1', $tenant->fresh()->theme);
    }
}
