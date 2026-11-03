<?php
namespace Tests\Feature;
use App\Models\{Tenant, User};
use App\Services\{PlanAccess, SuperAdminStoreOverview};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class SuperAdminTrialTest extends TestCase {
    use RefreshDatabase;
    public function test_superadmin_can_extend_shorten_and_audit_trial_without_changing_registration(): void {
        $tenant = Tenant::create(['name' => 'Trial Store']);
        $tenant->forceFill(['created_at' => now()->subDays(50)])->save();
        $registered = $tenant->fresh()->created_at->toDateTimeString();
        $admin = User::factory()->create(['type' => 'super_admin']);
        $this->actingAs($admin);
        $url = '/superadmin/tenants/'.$tenant->id.'/trial';
        $end = now()->addDays(3)->startOfMinute();
        $this->patch($url, ['trial_ends_at' => $end->format('Y-m-d\TH:i'), 'reason' => 'Support extension'])->assertRedirect();
        $tenant->refresh();
        $access = app(PlanAccess::class);
        $this->assertTrue($access->inTrial($tenant));
        $this->assertTrue($access->adminAvailable($tenant));
        $this->assertTrue($access->storefrontAvailable($tenant));
        $this->assertSame($registered, $tenant->created_at->toDateTimeString());
        $this->assertSame($end->toDateTimeString(), $tenant->trial_ends_at->toDateTimeString());
        $this->assertDatabaseHas('tenant_trial_changes', ['tenant_id' => $tenant->id, 'changed_by' => $admin->id, 'reason' => 'Support extension']);
        $overview = app(SuperAdminStoreOverview::class);
        $this->assertTrue($overview->filter(Tenant::query(), ['status' => 'trial'])->whereKey($tenant->id)->exists());
        $this->patch($url, ['trial_ends_at' => now()->subDay()->format('Y-m-d\TH:i'), 'reason' => 'End trial'])->assertRedirect();
        $this->assertFalse($access->inTrial($tenant->fresh()));
        $this->assertTrue($overview->filter(Tenant::query(), ['status' => 'trial_expired'])->whereKey($tenant->id)->exists());
        $this->assertDatabaseCount('tenant_trial_changes', 2);
        $this->get('/superadmin/tenants')->assertOk()->assertSee('Edit trial');
    }
    public function test_merchant_cannot_override_trial_and_invalid_input_is_rejected(): void {
        $tenant = Tenant::create(['name' => 'Trial Store']);
        $url = '/superadmin/tenants/'.$tenant->id.'/trial';
        $this->actingAs(User::factory()->create(['type' => 'admin', 'tenant_id' => $tenant->id]));
        $this->patchJson($url, ['trial_ends_at' => now()->addDays(30)->format('Y-m-d\TH:i'), 'reason' => 'Unauthorized'])->assertForbidden();
        $this->actingAs(User::factory()->create(['type' => 'super_admin']));
        $this->patchJson($url, ['trial_ends_at' => 'bad'])->assertUnprocessable();
        $this->patchJson($url, ['trial_ends_at' => now()->subDay()->format('Y-m-d\TH:i'), 'reason' => 'Invalid'])->assertUnprocessable();
        $this->assertDatabaseCount('tenant_trial_changes', 0);
    }
}
