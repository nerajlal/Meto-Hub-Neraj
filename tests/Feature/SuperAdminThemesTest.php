<?php
namespace Tests\Feature;
use App\Models\{Tenant, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class SuperAdminThemesTest extends TestCase
{
    use RefreshDatabase;
    public function test_all_themes_usage_and_safe_demo_previews_are_available_to_superadmin(): void
    {
        $store = Tenant::create(['name' => 'Demo', 'slug' => 'mens-clothing', 'theme' => 'template_31']);
        $store->forceFill(['slug' => 'mens-clothing'])->save();
        $this->actingAs(User::factory()->create(['type' => 'super_admin', 'tenant_id' => null]));
        $response = $this->get('/superadmin/themes')->assertOk()->assertSee('Storefront Themes');
        foreach (config('storefront_themes') as $id => $theme) $response->assertSee($theme['name'])->assertSee('data-theme-id="'.$id.'"', false);
        $response->assertViewHas('themes', fn ($themes) => $themes['template_31']['stores'] === 1 && str_contains($themes['template_31']['preview_url'], 'preview_theme=template_31'));
        $this->assertSame('template_31', $store->fresh()->theme);
        $this->actingAs(User::factory()->create(['type' => 'admin', 'tenant_id' => $store->id]));
        $this->get('/superadmin/themes')->assertForbidden();
    }
    public function test_empty_database_still_lists_every_theme_without_dead_preview_links(): void
    {
        $this->actingAs(User::factory()->create(['type' => 'super_admin', 'tenant_id' => null]));
        $this->get('/superadmin/themes')->assertOk()->assertViewHas('themes', fn ($themes) => $themes->count() === count(config('storefront_themes')) && $themes->every(fn ($t) => $t['stores'] === 0 && $t['preview_url'] === null));
    }
}
