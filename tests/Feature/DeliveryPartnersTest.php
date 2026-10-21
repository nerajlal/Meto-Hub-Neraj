<?php

namespace Tests\Feature;

use App\Models\DeliveryPartner;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryPartnersTest extends TestCase
{
    use RefreshDatabase;

    public function test_courier_presets_are_idempotent_scoped_and_editable(): void
    {
        $store = Tenant::create(['name' => 'Courier Store']);
        $other = Tenant::create(['name' => 'Other Store']);
        $this->actingAs(User::factory()->create(['tenant_id' => $store->id, 'type' => 'admin']));
        $base = '/'.$store->id.'/admin/settings/delivery-partners';
        $this->get($base)->assertOk()->assertSee('Add DTDC')->assertSee('Add India Post');
        foreach (['dtdc', 'india_post', 'dtdc'] as $preset) {
            $this->post($base, ['preset' => $preset, 'tenant_id' => $other->id])->assertRedirect();
        }
        $this->assertDatabaseCount('delivery_partners', 2);
        $this->assertDatabaseHas('delivery_partners', ['tenant_id' => $store->id, 'name' => 'DTDC']);
        $partner = DeliveryPartner::where('name', 'DTDC')->firstOrFail();
        $this->get($base)->assertSee($base.'/'.$partner->id);
        $this->put($base.'/'.$partner->id, config('delivery_partners.dtdc') + ['status' => 1, 'tenant_id' => $other->id, 'is_default' => 1])->assertRedirect();
        $this->assertSame($store->id, $partner->fresh()->tenant_id);
        $this->assertFalse((bool) $partner->fresh()->is_default);
        $this->post($base, ['preset' => 'unknown'])->assertSessionHasErrors('preset');
        $this->post($base, ['name' => 'Unsafe', 'tracking_url_template' => 'javascript:alert(1)', 'status' => 1])->assertSessionHasErrors('tracking_url_template');
        $foreign = DeliveryPartner::create(['tenant_id' => $other->id, 'name' => 'Foreign', 'status' => 1]);
        $this->put($base.'/'.$foreign->id, ['name' => 'Changed', 'status' => 1])->assertNotFound();
        $this->post($base.'/'.$foreign->id.'/default')->assertNotFound();
        $this->delete($base.'/'.$foreign->id)->assertNotFound();
    }
}
