<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Services\StorefrontTheme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontThemesTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_tags_use_admin_branding_with_readable_text_in_every_theme(): void
    {
        $tenant = $this->store();
        $product = $this->catalog($tenant);
        $product->update(['tags' => ['New']]);
        foreach (['#183e32' => '#ffffff', '#ffe600' => '#000000'] as $color => $foreground) {
            $tenant->update(['primary_color' => $color, 'dark_color' => $color]);
            foreach (array_keys(config('storefront_themes')) as $theme) {
                $tenant->update(['theme' => $theme]);
                $this->get('/store/'.$tenant->slug.'/all-products')->assertOk()
                    ->assertSee('New')
                    ->assertSee('--store-badge-bg: var(--accent-color);', false)
                    ->assertSee('--store-badge-fg: '.$foreground.';', false)
                    ->assertSee('--store-stock-fg: '.$foreground.';', false);
            }
        }
        $this->assertSame('#000000', app(StorefrontTheme::class)->contrastText('#ffffff'));
        $this->assertSame('#ffffff', app(StorefrontTheme::class)->contrastText('#000000'));
    }

    private function store(): Tenant
    {
        return Tenant::create(['name' => 'Neighbourhood Grocer', 'theme' => 'template_1']);
    }

    private function catalog(Tenant $tenant): Product
    {
        $collection = Collection::create(['tenant_id' => $tenant->id, 'name' => 'Pantry', 'slug' => 'pantry', 'status' => true]);
        $product = Product::create(['tenant_id' => $tenant->id, 'title' => 'Rice', 'status' => 'active', 'collection_id' => $collection->id]);
        $product->variants()->create(['size' => '1kg', 'price' => 100, 'stock' => 10]);
        return $product;
    }

    public function test_template_one_keeps_its_original_home_layout(): void
    {
        $tenant = $this->store();
        $this->get('/v1?tenant_id='.$tenant->id)->assertOk()
            ->assertSee('hero-slider-container')->assertDontSee('class="theme-welcome ', false)
            ->assertDontSee('class="theme-department-sidebar"', false);
    }

    public function test_settings_lists_nine_themes_with_correct_preview_urls(): void
    {
        $tenant = $this->store();
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $response = $this->actingAs($admin)->get("/{$tenant->id}/admin/settings/theme")->assertOk();
        $response->assertViewHas('themes', fn ($themes) => count($themes) === 9);
        foreach (app(StorefrontTheme::class)->all() as $id => $theme) {
            $response->assertSee($theme['name'])->assertSee('preview_theme='.$id);
        }
    }

    public function test_admin_can_activate_each_theme_but_not_an_unknown_theme(): void
    {
        $tenant = $this->store();
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        foreach (array_keys(app(StorefrontTheme::class)->all()) as $id) {
            $this->post("/{$tenant->id}/admin/settings/theme", ['theme' => $id])->assertRedirect();
            $this->assertEquals($id, $tenant->fresh()->theme);
        }
        $this->postJson("/{$tenant->id}/admin/settings/theme", ['theme' => 'unknown'])->assertUnprocessable();
        $this->assertEquals('template_9', $tenant->fresh()->theme);
    }

    public function test_every_theme_renders_home_catalog_product_and_checkout(): void
    {
        $tenant = $this->store();
        $product = $this->catalog($tenant);
        foreach (array_keys(app(StorefrontTheme::class)->all()) as $id) {
            $tenant->update(['theme' => $id]);
            $this->withSession(['active_tenant_id' => $tenant->id, 'cart' => [
                'rice' => ['product_id' => $product->id, 'size' => '1kg', 'type' => 'product', 'quantity' => 1,
                    'price' => 100, 'name' => 'Rice', 'image' => '/Images/default.png'],
            ]]);
            foreach (['/v1', '/v1/all-products', '/v1/collections?slug=pantry', '/v1/product?id='.$product->id, '/v1/checkout', '/v1/combos'] as $url) {
                $response = $this->get($url)->assertOk()->assertSee('data-storefront-theme="'.$id.'"', false);
                if ($url === '/v1/checkout') {
                    $response->assertSee('value="cod"', false)->assertDontSee('value="online"', false);
                }
            }
        }
    }

    public function test_preview_does_not_activate_theme_and_links_preserve_preview(): void
    {
        $tenant = $this->store();
        $this->catalog($tenant);
        $response = $this->get("/v1?tenant_id={$tenant->id}&preview=1&preview_theme=template_4");
        $response->assertOk()->assertSee('data-storefront-theme="template_4"', false)
            ->assertSee('preview_theme=template_4')->assertSee('name="preview_theme" value="template_4"', false);
        $this->assertEquals('template_1', $tenant->fresh()->theme);
        $this->get('/v1')->assertOk()->assertSee('data-storefront-theme="template_1"', false);
        $this->get('/v1?preview=1&preview_theme=unknown')->assertNotFound();
    }

    public function test_legacy_links_keep_the_tenants_selected_theme(): void
    {
        $tenant = $this->store();
        $tenant->update(['theme' => 'template_5']);
        $this->catalog($tenant);
        foreach (['/v2', '/v3', '/v4', '/v5', '/v2/collection/pantry'] as $url) {
            $this->withSession(['active_tenant_id' => $tenant->id])->get($url)
                ->assertOk()->assertSee('data-storefront-theme="template_5"', false);
        }
    }

    public function test_new_themes_have_distinct_structures_and_render_admin_catalog_data(): void
    {
        $tenant = $this->store();
        $tenant->update(['primary_color' => '#125678', 'contact_phone' => '1234567890']);
        $product = $this->catalog($tenant);
        \App\Models\Slider::create(['tenant_id' => $tenant->id, 'title' => 'Merchant special',
            'image_desktop' => 'https://example.com/merchant-desktop.png',
            'image_mobile' => 'https://example.com/merchant-mobile.png', 'status' => true, 'order' => 1]);
        foreach (['template_2' => 'market', 'template_3' => 'quick', 'template_4' => 'pantry', 'template_5' => 'corner', 'template_6' => 'pop', 'template_7' => 'atelier', 'template_8' => 'vista', 'template_9' => 'noir'] as $theme => $style) {
            $response = $this->get('/v1?tenant_id='.$tenant->id.'&preview=1&preview_theme='.$theme)->assertOk();
            $response->assertSee('data-theme-header="'.$style.'"', false)
                ->assertSee('data-theme-home="'.$style.'"', false)
                ->assertSee('sf-product-'.$style)->assertSee('Rice')->assertSee('Pantry')
                ->assertSee('Merchant special')->assertSee('merchant-desktop.png')->assertSee('merchant-mobile.png')
                ->assertSee('#125678')->assertSee('1234567890');
            $this->assertEquals('template_1', $tenant->fresh()->theme);
        }
    }

    public function test_account_and_wishlist_use_the_selected_theme(): void
    {
        $tenant = $this->store();
        $tenant->update(['theme' => 'template_4']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'user']))
            ->withSession(['active_tenant_id' => $tenant->id]);
        foreach (['/account/orders', '/account/profile', '/account/reorder', '/v1/wishlist'] as $url) {
            $this->get($url)->assertOk()->assertSee('data-storefront-theme="template_4"', false);
        }
    }
    public function test_product_cards_catalogs_and_details_have_distinct_layouts_and_working_options(): void
    {
        $tenant = $this->store();
        $product = $this->catalog($tenant);
        $product->update(['description' => 'Store-managed product details.']);
        $product->variants()->first()->update(['price' => 250, 'stock' => 0]);
        $cheaper = $product->variants()->create(['size' => "Small 'special' pack", 'price' => 100, 'compare_at_price' => 120, 'stock' => 8]);
        $discount = \App\Models\Discount::create(['tenant_id' => $tenant->id, 'code' => 'EDIT20', 'type' => 'percentage', 'value' => 20, 'status' => 'active']);
        $product->discounts()->attach($discount);
        $product->images()->create(['path' => 'https://example.com/product.jpg', 'order' => 1]);
        $tenant->update(['delivery_info' => 'Delivery arranged by the store.']);
        foreach (app(StorefrontTheme::class)->all() as $id => $theme) {
            $query = '?tenant_id='.$tenant->id.'&preview=1&preview_theme='.$id;
            $catalog = $this->get('/v1/all-products'.$query)->assertOk();
            $catalog->assertSee('data-product-catalog="'.$theme['layout'].'"', false)
                ->assertSee('data-card-style="'.$theme['layout'].'"', false)->assertSee('80.00');
            $detail = $this->get('/v1/product'.$query.'&id='.$product->id)->assertOk();
            $detail->assertSee('data-product-detail="'.$theme['layout'].'"', false)
                ->assertSee('Store-managed product details.')->assertSee('Delivery arranged by the store.')
                ->assertSee('value="'.$cheaper->id.'"', false)->assertSee('80.00')
                ->assertSee('selectProductVariant')->assertSee('syncMainProductUI');
            $this->get('/v1/collections'.$query.'&slug=pantry')->assertOk()
                ->assertSee('data-product-catalog="'.$theme['layout'].'"', false);
        }
    }

    public function test_product_presentation_handles_no_variants_and_retains_real_image_urls(): void
    {
        $tenant = $this->store();
        $product = Product::create(['tenant_id' => $tenant->id, 'title' => 'Unconfigured product', 'status' => 'active']);
        $product->images()->create(['path' => 'https://example.com/real-image.jpg', 'order' => 1]);
        foreach (array_keys(app(StorefrontTheme::class)->all()) as $id) {
            $this->get('/v1/product?tenant_id='.$tenant->id.'&preview=1&preview_theme='.$id.'&id='.$product->id)
                ->assertOk()->assertSee('Out of stock');
            $this->get('/v1/all-products?tenant_id='.$tenant->id.'&preview=1&preview_theme='.$id)->assertOk();
        }
        $data = app(\App\Services\StorefrontProduct::class)->data($product);
        $this->assertNull($data['selected']);
        $this->assertEquals('https://example.com/real-image.jpg', $data['images']->first());
    }

    public function test_price_filtering_and_sorting_use_the_lowest_variant_price_on_every_theme(): void
    {
        $tenant = $this->store();
        $product = $this->catalog($tenant);
        $product->variants()->first()->update(['price' => 300]);
        $product->variants()->create(['size' => 'Small', 'price' => 100, 'stock' => 10]);
        $expensive = Product::create(['tenant_id' => $tenant->id, 'title' => 'Premium rice', 'status' => 'active', 'collection_id' => $product->collection_id]);
        $expensive->variants()->create(['size' => 'Large', 'price' => 500, 'stock' => 10]);
        foreach (array_keys(app(StorefrontTheme::class)->all()) as $id) {
            $query = '?tenant_id='.$tenant->id.'&preview=1&preview_theme='.$id;
            foreach (['/v1/all-products', '/v1/collections'] as $page) {
                $this->get($page.$query.'&slug=pantry&min_price=90&max_price=150&sort=price_asc')
                    ->assertOk()->assertViewHas('products', fn ($products) => $products->count() === 1 && $products->first()->id === $product->id);
                $this->get($page.$query.'&slug=pantry&sort=price_desc')->assertOk()
                    ->assertViewHas('products', fn ($products) => $products->first()->id === $expensive->id);
            }
        }
    }

}
