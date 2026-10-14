<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Services\StorefrontTheme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontHeroTest extends TestCase
{
    use RefreshDatabase;

    private function settings(array $extra = []): array
    {
        return array_replace(['store_name' => 'Custom Store', 'about_title' => 'About us',
            'mobile_grid_cols' => 2, 'primary_color' => '#10B981', 'dark_color' => '#0F172A',
            'accent_color' => '#ECFDF5', 'currency' => 'INR'], $extra);
    }

    public function test_admin_hero_text_is_saved_for_their_store_and_used_safely_in_all_fifteen_themes(): void
    {
        $tenant = Tenant::create(['name' => 'Custom Store']);
        $other = Tenant::create(['name' => 'Other Store']);
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $copy = ['hero_eyebrow' => 'Made for our neighbourhood', 'hero_heading' => "Your daily favourites\nSelected by us",
            'hero_description' => '<script>alert("unsafe")</script> Fresh finds every day.', 'hero_button_label' => 'Shop our favourites'];
        $this->actingAs($admin)->get('/'.$tenant->id.'/admin/settings/storefront')->assertOk()
            ->assertSee('Homepage Hero Text')->assertSee('name="hero_heading"', false);
        $this->post('/'.$tenant->id.'/admin/settings/storefront', $this->settings($copy))->assertRedirect()->assertSessionHas('success');
        foreach ($copy as $key => $value) $this->assertSame($value, $tenant->fresh()->getAttribute($key));
        $this->assertNull($other->fresh()->hero_heading);
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $tenant->update(['theme' => $theme]);
            $this->get($tenant->storefrontUrl())->assertOk()->assertSee($copy['hero_eyebrow'])
                ->assertSee($copy['hero_heading'])->assertSee($copy['hero_description'])
                ->assertSee($copy['hero_button_label'])->assertDontSee('<script>alert("unsafe")</script>', false);
        }
        $this->postJson('/'.$other->id.'/admin/settings/storefront', $this->settings($copy))->assertForbidden();
    }

    public function test_blank_fields_restore_theme_defaults_and_omitted_fields_preserve_custom_copy(): void
    {
        $tenant = Tenant::create(['name' => 'Custom Store', 'theme' => 'template_13', 'hero_heading' => 'Our original headline']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $this->post('/'.$tenant->id.'/admin/settings/storefront', $this->settings())->assertRedirect();
        $this->assertSame('Our original headline', $tenant->fresh()->hero_heading);
        $this->post('/'.$tenant->id.'/admin/settings/storefront', $this->settings(['hero_heading' => '   ']))->assertRedirect();
        $this->assertNull($tenant->fresh()->hero_heading);
        $this->get($tenant->storefrontUrl())->assertOk()->assertSee('The art of choosing well.');
        $this->get($tenant->storefrontUrl(['preview' => 1, 'preview_theme' => 'template_14']))->assertOk()->assertSee('Good finds. Better stories.');
        $this->assertSame('template_13', $tenant->fresh()->theme);
    }

    public function test_hero_text_lengths_are_validated_and_failed_input_is_retained(): void
    {
        $tenant = Tenant::create(['name' => 'Custom Store']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        foreach (['hero_eyebrow' => 120, 'hero_heading' => 200, 'hero_description' => 1000, 'hero_button_label' => 60] as $field => $max) {
            $this->postJson('/'.$tenant->id.'/admin/settings/storefront', $this->settings([$field => str_repeat('x', $max + 1)]))
                ->assertUnprocessable()->assertJsonValidationErrors($field);
        }
        $this->withSession(['_old_input' => ['hero_heading' => 'Keep my unsaved headline']])
            ->get('/'.$tenant->id.'/admin/settings/storefront')->assertOk()->assertSee('Keep my unsaved headline');
        $this->assertSame('Welcome to Custom Store.', app(StorefrontTheme::class)->hero($tenant)['heading']);
    }
}
