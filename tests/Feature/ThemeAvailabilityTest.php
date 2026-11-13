<?php
namespace Tests\Feature;

use App\Models\{Tenant, User, ThemeAvailability};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ThemeAvailabilityTest extends TestCase
{
    use RefreshDatabase;
    private function superadmin(): User { return User::factory()->create(['type' => 'super_admin', 'tenant_id' => null]); }

    public function test_default_catalog_is_available_and_superadmin_can_reserve_themes(): void
    {
        $this->actingAs($this->superadmin());
        $this->get('/superadmin/themes')->assertOk()->assertSee('Crunchiko')->assertViewHas('themes', fn ($themes) => $themes->every(fn ($t) => $t['is_available']));
        $this->patch('/superadmin/themes/template_31/availability', ['is_available' => false])->assertRedirect();
        $this->assertDatabaseHas('theme_availabilities', ['theme' => 'template_31', 'is_available' => false]);
        $this->get('/superadmin/themes')->assertOk()->assertViewHas('themes', fn ($themes) => !$themes['template_31']['is_available']);
        $this->get('/themes')->assertOk()->assertDontSee('data-public-theme="template_31"', false);
        $this->patchJson('/superadmin/themes/template_31/availability', ['is_available' => 'invalid'])->assertUnprocessable();
        $this->patch('/superadmin/themes/template_31/availability', ['is_available' => true])->assertRedirect();
        $this->get('/themes')->assertOk()->assertSee('data-public-theme="template_31"', false);
    }

    public function test_existing_customers_keep_hidden_design_but_other_merchants_cannot_select_or_preview_it(): void
    {
        ThemeAvailability::create(['theme' => 'template_31', 'is_available' => false]);
        $existing = Tenant::create(['name' => 'Existing custom store', 'theme' => 'template_31']);
        $other = Tenant::create(['name' => 'Standard store', 'theme' => 'template_1']);
        $owner = User::factory()->create(['tenant_id' => $existing->id, 'type' => 'admin']);
        $this->get($existing->storefrontUrl())->assertOk()->assertSee('data-theme-home="pantryselect"', false);
        $this->actingAs($owner)->get('/'.$existing->id.'/admin/settings/theme')->assertOk()->assertViewHas('themes', fn ($themes) => !collect($themes)->contains('id', 'template_31'));
        $this->post('/'.$existing->id.'/admin/settings/theme', ['theme' => 'template_31'])->assertRedirect();
        $this->actingAs(User::factory()->create(['tenant_id' => $other->id, 'type' => 'admin']));
        $this->post('/'.$other->id.'/admin/settings/theme', ['theme' => 'template_31'])->assertForbidden();
        $this->patch('/superadmin/themes/template_31/availability', ['is_available' => true])->assertForbidden();
        $this->get($other->storefrontUrl(['preview' => 1, 'preview_theme' => 'template_31']))->assertNotFound();
        $this->actingAs($this->superadmin());
        $this->get($other->storefrontUrl(['preview' => 1, 'preview_theme' => 'template_31']))->assertOk();
        $this->post('/'.$other->id.'/admin/settings/theme', ['theme' => 'template_31'])->assertRedirect();
        $this->assertSame('template_31', $other->fresh()->theme);
    }

    public function test_signup_rejects_hidden_theme_and_uses_an_available_default(): void
    {
        Mail::fake();
        ThemeAvailability::create(['theme' => 'template_1', 'is_available' => false]);
        ThemeAvailability::create(['theme' => 'template_31', 'is_available' => false]);
        $payload = ['name' => 'New owner', 'business' => 'New business', 'country' => 'India', 'email' => 'new-theme-owner@example.com', 'whatsapp' => '919999999999', 'password' => 'password123'];
        $this->postJson(route('saas.register'), $payload + ['theme' => 'template_31'])->assertUnprocessable()->assertJsonValidationErrors('theme');
        $this->assertDatabaseCount('tenants', 0);
        $this->postJson(route('saas.register'), $payload)->assertCreated()->assertJson(['success' => true]);
        $this->assertSame('template_2', Tenant::firstOrFail()->theme);
    }
    public function test_all_reserved_catalog_blocks_new_signup_without_affecting_existing_stores(): void
    {
        foreach (array_keys(config('storefront_themes')) as $theme) ThemeAvailability::create(['theme' => $theme, 'is_available' => false]);
        $tenant = Tenant::create(['name' => 'Existing store', 'theme' => 'template_31']);
        $this->get($tenant->storefrontUrl())->assertOk();
        $this->postJson(route('saas.register'), ['name' => 'Owner', 'business' => 'New store', 'country' => 'India', 'email' => 'blocked@example.com', 'whatsapp' => '919999999999', 'password' => 'password123'])
            ->assertUnprocessable()->assertJsonValidationErrors('theme');
        $this->assertDatabaseCount('tenants', 1);
    }

}
