<?php
namespace Tests\Feature;

use App\Models\{Tenant, Slider, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SliderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_can_explicitly_enable_disable_and_restore_slide_without_losing_images(): void
    {
        $tenant = Tenant::create(['name' => 'Slider shop', 'theme' => 'template_31']);
        $slider = Slider::create(['tenant_id' => $tenant->id, 'title' => 'Seasonal slide', 'image_desktop' => 'sliders/desktop.jpg', 'image_mobile' => 'sliders/mobile.jpg', 'status' => true, 'order' => 4]);
        $this->actingAs(User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']));
        $this->get('/'.$tenant->id.'/admin/settings/slider')->assertOk()->assertSee('role="switch"', false);
        $endpoint = '/'.$tenant->id.'/admin/settings/slider/'.$slider->id.'/status';
        $this->patchJson($endpoint, ['status' => false])->assertOk()->assertJson(['status' => false]);
        $this->patchJson($endpoint, ['status' => false])->assertOk()->assertJson(['status' => false]);
        $this->get($tenant->storefrontUrl())->assertOk()->assertDontSee('/storage/sliders/desktop.jpg', false);
        $this->assertSame('sliders/desktop.jpg', $slider->fresh()->image_desktop);
        $this->assertSame(4, $slider->fresh()->order);
        $this->patchJson($endpoint, ['status' => true])->assertOk()->assertJson(['status' => true]);
        $this->get($tenant->storefrontUrl())->assertOk()->assertSee('/storage/sliders/desktop.jpg', false);
        $this->patchJson($endpoint, ['status' => 'wrong'])->assertUnprocessable();
        $other = Tenant::create(['name' => 'Other store']);
        $foreign = Slider::create(['tenant_id' => $other->id, 'title' => 'Other slide', 'image_desktop' => 'foreign.jpg', 'image_mobile' => 'foreign.jpg', 'status' => true]);
        $this->patchJson('/'.$tenant->id.'/admin/settings/slider/'.$foreign->id.'/status', ['status' => false])->assertNotFound();
        $this->assertTrue($foreign->fresh()->status);
    }
}
