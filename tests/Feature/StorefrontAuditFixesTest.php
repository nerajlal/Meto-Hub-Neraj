<?php
namespace Tests\Feature;

use App\Models\{Tenant, Product, Bundle, Review};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontAuditFixesTest extends TestCase
{
    use RefreshDatabase;
    private function catalog(): array
    {
        $tenant = Tenant::create(['name' => 'Audit Store', 'theme' => 'template_31']);
        $product = Product::create(['tenant_id' => $tenant->id, 'title' => 'Jackfruit chips', 'status' => 'active']);
        $variant = $product->variants()->create(['price' => 100, 'stock' => 10, 'size' => '100g']);
        $product->images()->create(['path' => 'products/fruit.png', 'order' => 0]);
        $bundle = Bundle::create(['tenant_id' => $tenant->id, 'title' => 'Fruit Pair', 'slug' => 'fruit-pair', 'type' => 'bundle', 'status' => 'active',
            'image' => 'bundles/fruit.png', 'description' => 'A pair of crunchy favourites.', 'discount_type' => 'fixed', 'discount_value' => 10]);
        $bundle->products()->attach($product->id, ['quantity' => 2, 'product_variant_id' => $variant->id]);
        return [$tenant, $product, $bundle];
    }
    public function test_rendered_schema_has_real_context_and_no_blade_code_on_home_product_and_combo(): void
    {
        [$tenant, $product, $bundle] = $this->catalog();
        foreach (['', '/product?id='.$product->id, '/combo?id='.$bundle->id] as $path) {
            $html = $this->get($tenant->storefrontUrl().$path)->assertOk()->getContent();
            preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $match);
            $data = json_decode($match[1], true, flags: JSON_THROW_ON_ERROR);
            $this->assertSame('https://schema.org', $data['@context']);
            $this->assertArrayHasKey('@graph', $data);
            $this->assertStringNotContainsString('<?php', $match[1]);
            $this->assertStringNotContainsString('__contextArgs', $match[1]);
        }
    }
    public function test_combo_has_its_own_title_description_and_image_and_no_random_sales_counts(): void
    {
        [$tenant, $product, $bundle] = $this->catalog();
        $this->get($tenant->storefrontUrl().'/combo?id='.$bundle->id)->assertOk()
            ->assertSee('content="Fruit Pair | Audit Store"', false)->assertSee('content="A pair of crunchy favourites."', false)
            ->assertSee('content="http://localhost/storage/bundles/fruit.png"', false);
        $this->get($tenant->storefrontUrl().'/combos')->assertOk()->assertDontSee(' bought')->assertDontSee('social-proof-tag"', false);
        $bundle->update(['image' => null]);
        $this->get($tenant->storefrontUrl().'/combo?id='.$bundle->id)->assertOk()->assertSee('content="http://localhost/storage/products/fruit.png"', false);
    }
    public function test_empty_checkout_has_no_order_submit_but_real_cart_still_has_checkout_form(): void
    {
        [$tenant, $product] = $this->catalog();
        $url = $tenant->storefrontUrl().'/checkout';
        $this->get($url)->assertOk()->assertSee('Your basket is empty')->assertSee('Continue shopping')->assertDontSee('Complete Order');
        $line = ['type' => 'product', 'product_id' => $product->id, 'name' => $product->title, 'size' => '100g', 'quantity' => 1, 'price' => 100, 'image' => '/storage/products/fruit.png'];
        $this->withSession(['active_tenant_id' => $tenant->id, 'cart' => [$line]])->get($url)->assertOk()->assertSee('Complete Order')->assertDontSee('Your basket is empty');
    }
    public function test_published_reviews_do_not_claim_purchase_verification(): void
    {
        [$tenant, $product] = $this->catalog();
        Review::create(['tenant_id' => $tenant->id, 'product_id' => $product->id, 'name' => 'Store reviewer', 'review' => 'Merchant supplied feedback', 'rating' => 5, 'status' => 'published']);
        $this->get($tenant->storefrontUrl())->assertOk()->assertSee('Customer Reviews')->assertSee('Customer review')->assertSee('Merchant supplied feedback')
            ->assertDontSee('Verified Customer')->assertDontSee('Verified Reviews');
    }
}
