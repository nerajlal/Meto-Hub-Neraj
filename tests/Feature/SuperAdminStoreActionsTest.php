<?php
namespace Tests\Feature;

use App\Models\{Tenant, User, Product};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminStoreActionsTest extends TestCase
{
    use RefreshDatabase;
    private function loginSuperadmin(): void { $this->actingAs(User::factory()->create(['type' => 'super_admin', 'tenant_id' => null])); }

    public function test_freeze_blocks_storefront_and_merchant_admin_until_unfrozen(): void
    {
        $tenant = Tenant::create(['name' => 'Freeze store']);
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $this->loginSuperadmin();
        $url = '/superadmin/tenants/'.$tenant->id.'/freeze';
        $this->patch($url, ['frozen' => true, 'reason' => 'Under review'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertTrue($tenant->fresh()->is_frozen);
        $this->get('/superadmin/tenants')->assertOk()->assertSee('Frozen')->assertSee('Manage store');
        $this->get($tenant->storefrontUrl().'?preview_admin=1')->assertStatus(503)->assertSee('Store temporarily unavailable');
        $this->actingAs($admin)->get('/'.$tenant->id.'/admin')->assertForbidden();
        $this->actingAs($admin)->patchJson($url, ['frozen' => false])->assertForbidden();
        $this->loginSuperadmin();
        $this->patch($url, ['frozen' => false])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertFalse($tenant->fresh()->is_frozen);
        $this->get($tenant->storefrontUrl())->assertOk();
        $this->actingAs($admin)->get('/'.$tenant->id.'/admin')->assertOk();
    }

    public function test_confirmed_deletion_soft_deletes_store_and_preserves_history(): void
    {
        $tenant = Tenant::create(['name' => 'Retain history']);
        $admin = User::factory()->create(['tenant_id' => $tenant->id, 'type' => 'admin']);
        $product = Product::create(['tenant_id' => $tenant->id, 'title' => 'Keep record', 'status' => 'active']);
        $url = '/superadmin/tenants/'.$tenant->id;
        $this->actingAs($admin)->delete($url, ['confirm_name' => $tenant->name])->assertForbidden();
        $this->loginSuperadmin();
        $this->delete($url, ['confirm_name' => 'Wrong store'])->assertSessionHasErrors('confirm_name');
        $this->assertNotSoftDeleted($tenant);
        $this->delete($url, ['confirm_name' => $tenant->name])->assertRedirect('/superadmin/tenants')->assertSessionHasNoErrors();
        $this->assertSoftDeleted($tenant);
        $replacement = Tenant::create(['name' => $tenant->name]);
        $this->assertNotSame($tenant->slug, $replacement->slug);
        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->get($tenant->storefrontUrl())->assertNotFound();
        $this->actingAs($admin)->get('/'.$tenant->id.'/admin')->assertNotFound();
    }

    public function test_live_recurring_billing_requires_cancellation_before_deleting(): void
    {
        $tenant = Tenant::create(['name' => 'Paid store', 'razorpay_subscription_id' => 'sub_live', 'subscription_status' => 'active']);
        $this->loginSuperadmin();
        $url = '/superadmin/tenants/'.$tenant->id;
        $this->delete($url, ['confirm_name' => $tenant->name])->assertSessionHasErrors('confirm_name');
        $this->assertNotSoftDeleted($tenant);
        $this->patch($url.'/freeze', ['frozen' => true])->assertSessionHasNoErrors();
        $this->assertSame('active', $tenant->fresh()->subscription_status);
        $this->patchJson($url.'/freeze', ['frozen' => 'invalid'])->assertUnprocessable();
        $tenant->update(['subscription_status' => 'cancelled']);
        $this->delete($url, ['confirm_name' => $tenant->name])->assertSessionHasNoErrors();
        $this->assertSoftDeleted($tenant);
    }
}
