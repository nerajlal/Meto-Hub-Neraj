<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreNameSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function settings(array $overrides = []): array
    {
        return array_replace(['store_name' => 'Renamed Grocery', 'about_title' => 'About our store',
            'mobile_grid_cols' => 2, 'primary_color' => '#10B981', 'dark_color' => '#0F172A',
            'accent_color' => '#ECFDF5', 'currency' => 'INR'], $overrides);
    }

    public function test_store_story_image_and_content_are_saved_and_render_below_reviews(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $tenant = Tenant::create(['name' => 'Story Store', 'theme' => 'template_31']);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $url = '/'.$tenant->id.'/admin/settings/storefront';
        $this->get($url)->assertOk()->assertSee('Store story image');
        $this->post($url, $this->settings(['about_title' => 'Our own story', 'about_text' => 'Our merchant supplied introduction.', 'about_image' => \Illuminate\Http\UploadedFile::fake()->image('story.png')]))->assertRedirect()->assertSessionHasNoErrors();
        $image = $tenant->fresh()->about_image;
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($image);
        \App\Models\Review::create(['tenant_id' => $tenant->id, 'name' => 'Test shopper', 'review' => 'Published fixture review', 'rating' => 5, 'status' => 'published']);
        $html = $this->get('/v1?tenant_id='.$tenant->id)->assertOk()->assertSee('Our own story')->assertSee('Our merchant supplied introduction.')->assertSee($image)->getContent();
        $this->assertLessThan(strpos($html, 'class="ps-story"'), strpos($html, 'class="sf-reviews-section"'));
        $this->get('/store/'.$tenant->slug.'/about')->assertOk()->assertSee($image);
        $this->post($url, $this->settings())->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame($image, $tenant->fresh()->about_image);
        $this->post($url, $this->settings(['remove_about_image' => 1]))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertNull($tenant->fresh()->about_image);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($image);
    }

    public function test_homepage_highlights_are_editable_escaped_and_can_be_hidden(): void
    {
        $tenant = Tenant::create(['name' => 'Badge Store', 'theme' => 'template_31']);
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $base = '/'.$tenant->id.'/admin/settings/storefront';
        $this->actingAs($admin)->get($base)->assertOk()->assertSee('Homepage highlight strip');
        $this->post($base, $this->settings(['trust_badges_text' => "Vacuum Packed\nQuality Checked\n<script>bad()</script>"]))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame(['Vacuum Packed', 'Quality Checked', '<script>bad()</script>'], $tenant->fresh()->trust_badges);
        $this->get('/v1?tenant_id='.$tenant->id)->assertOk()->assertSee('ps-trust-strip')->assertSee('Vacuum Packed')->assertSee('&lt;script&gt;bad()', false)->assertDontSee('<script>bad()</script>', false);
        $this->postJson($base, $this->settings(['trust_badges_text' => implode("\n", array_fill(0, 9, 'Too many'))]))->assertUnprocessable();
        $this->post($base, $this->settings(['trust_badges_text' => '']))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame([], $tenant->fresh()->trust_badges);
        $this->get('/v1?tenant_id='.$tenant->id)->assertOk()->assertDontSee('class="ps-trust-strip"', false);
    }

    public function test_existing_store_defaults_to_its_registered_owner_contact_details(): void
    {
        $tenant = Tenant::create(['name' => 'Fresh Store', 'theme' => 'template_1']);
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin',
            'name' => 'Store Owner', 'email' => 'owner@example.com', 'phone' => '+91 98765 43210']);
        $this->actingAs($admin)->get('/'.$tenant->id.'/admin/settings/storefront')->assertOk()
            ->assertSee('Registered contact:')->assertSee('Store Owner')
            ->assertSee('name="contact_email" value="owner@example.com"', false)
            ->assertSee('name="contact_phone" value="+91 98765 43210"', false)
            ->assertSee('name="whatsapp_number" value="919876543210"', false);
    }

    public function test_custom_contact_details_and_failed_form_input_are_preserved(): void
    {
        $tenant = Tenant::create(['name' => 'Fresh Store', 'theme' => 'template_1',
            'contact_email' => 'support@example.com', 'contact_phone' => '+91 11111 22222',
            'whatsapp_number' => '911111122222']);
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin',
            'email' => 'owner@example.com', 'phone' => '+91 98765 43210']);
        $this->actingAs($admin)->get('/'.$tenant->id.'/admin/settings/storefront')->assertOk()
            ->assertSee('name="contact_email" value="support@example.com"', false)
            ->assertSee('name="contact_phone" value="+91 11111 22222"', false)
            ->assertSee('name="whatsapp_number" value="911111122222"', false);
        $this->withSession(['_old_input' => ['contact_phone' => '']])
            ->get('/'.$tenant->id.'/admin/settings/storefront')->assertOk()
            ->assertSee('name="contact_phone" value=""', false);
    }

    public function test_admin_can_rename_store_without_changing_public_link_or_other_tenants(): void
    {
        $tenant = Tenant::create(['name' => 'Original Grocery', 'theme' => 'template_1']);
        $other = Tenant::create(['name' => 'Other Store', 'theme' => 'template_1']);
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin', 'site_name' => $tenant->name]);
        $otherAdmin = User::factory()->create(['tenant_id' => $other->id, 'type' => 'admin', 'site_name' => $other->name]);
        $url = $tenant->storefrontUrl();
        $this->actingAs($admin)->get('/'.$tenant->id.'/admin/settings/storefront')->assertOk()
            ->assertSee('name="store_name"', false)->assertSee('value="Original Grocery"', false);
        $this->post('/'.$tenant->id.'/admin/settings/storefront', $this->settings())->assertRedirect()->assertSessionHas('success');
        $this->assertSame('Renamed Grocery', $tenant->fresh()->name);
        $this->assertSame('Renamed Grocery', $admin->fresh()->site_name);
        $this->assertSame($url, $tenant->fresh()->storefrontUrl());
        $this->assertSame('Other Store', $other->fresh()->name);
        $this->assertSame('Other Store', $otherAdmin->fresh()->site_name);
        foreach (array_keys(config('storefront_themes')) as $theme) {
            $tenant->update(['theme' => $theme]);
            $this->get($url)->assertOk()->assertSee('Renamed Grocery');
        }
        $this->postJson('/'.$other->id.'/admin/settings/storefront', $this->settings())->assertForbidden();
    }

    public function test_store_name_cannot_be_blank_or_exceed_database_length(): void
    {
        $tenant = Tenant::create(['name' => 'Original Grocery', 'theme' => 'template_1']);
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $this->actingAs($admin);
        foreach (['', '   ', str_repeat('a', 192)] as $name) {
            $this->postJson('/'.$tenant->id.'/admin/settings/storefront', $this->settings(['store_name' => $name]))
                ->assertUnprocessable()->assertJsonValidationErrors('store_name');
        }
        $this->assertSame('Original Grocery', $tenant->fresh()->name);
    }
}
