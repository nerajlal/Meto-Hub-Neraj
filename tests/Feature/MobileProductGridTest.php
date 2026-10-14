<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileProductGridTest extends TestCase
{
    use RefreshDatabase;

    public function test_saved_mobile_density_reaches_all_themes_home_catalog_and_related_products(): void
    {
        $tenant = Tenant::create(['name' => 'Mobile Store', 'theme' => 'template_1']);
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $collection = Collection::create(['tenant_id' => $tenant->id, 'name' => 'Pantry', 'slug' => 'pantry', 'status' => true]);
        foreach (['Rice', 'Lentils'] as $title) {
            $product = Product::create(['tenant_id' => $tenant->id, 'title' => $title, 'status' => 'active', 'collection_id' => $collection->id]);
            $product->variants()->create(['size' => '1kg', 'price' => 100, 'stock' => 10]);
        }
        $this->actingAs($admin);
        foreach ([1, 2, 3] as $columns) {
            $this->post('/'.$tenant->id.'/admin/settings/storefront', [
                'store_name' => 'Mobile Store', 'about_title' => 'About us', 'mobile_grid_cols' => $columns,
                'primary_color' => '#10B981', 'dark_color' => '#0F172A', 'accent_color' => '#ECFDF5', 'currency' => 'INR',
            ])->assertRedirect()->assertSessionHas('success');
            $this->assertEquals($columns, $tenant->fresh()->mobile_grid_cols);
            foreach (array_keys(config('storefront_themes')) as $theme) {
                $tenant->update(['theme' => $theme]);
                foreach (['', '/all-products', '/product?id='.$product->id] as $page) {
                    $this->get('/store/mobile-store'.$page)->assertOk()
                        ->assertSee('grid-cols-mobile-'.$columns, false)
                        ->assertSee('css/storefront-mobile-grid.css', false);
                }
            }
        }
    }
}
