<?php

namespace Tests\Feature;

use App\Auth\AdmidioRoleResolver;
use App\Enums\ItamRole;
use App\Livewire\Administration\Users as AdminUsersComponent;
use App\Livewire\Assets\AllAssets;
use App\Livewire\Auth\Login;
use App\Livewire\Employee\MyAssets;
use App\Models\AssetAcknowledgement;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class AdmidioAuthAndRbacTest extends TestCase
{
    use DatabaseTransactions;

    private function loadUserByLogin(string $loginName): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['username' => $loginName]);
        $this->assertInstanceOf(User::class, $user, "Failed to hydrate Admidio user [{$loginName}].");

        return $user;
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $this->get('/')
            ->assertRedirect(route('login'));

        $this->get('/assets')
            ->assertRedirect(route('login'));

        $this->get('/my-assets')
            ->assertRedirect(route('login'));
    }

    public function test_admidio_login_succeeds_for_test_accounts_and_rejects_invalid_or_inactive_accounts(): void
    {
        Livewire::test(Login::class)
            ->set('username', 'test.itsupport')
            ->set('password', 'WrongPassword!')
            ->call('authenticate')
            ->assertHasErrors(['username']);

        $this->assertGuest();

        Livewire::test(Login::class)
            ->set('username', 'test.employee')
            ->set('password', 'Test@1234')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('my-assets'));

        $this->assertAuthenticated();
        Auth::logout();

        // Suspended ITAM access in fg_itam_user_roles must fail login
        $emp = $this->loadUserByLogin('test.employee');
        DB::table('fg_itam_user_roles')->updateOrInsert(
            ['usr_id' => $emp->usr_id],
            ['role' => ItamRole::Employee->value, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()]
        );

        Livewire::test(Login::class)
            ->set('username', 'test.employee')
            ->set('password', 'Test@1234')
            ->call('authenticate')
            ->assertHasErrors(['username']);

        $this->assertGuest();
    }

    public function test_admidio_roles_resolve_accurately_for_all_six_roles(): void
    {
        $resolver = app(AdmidioRoleResolver::class);

        // 1. System & IT Admin: it nazrul (usr_id = 2) & test.sysadmin
        $itNazrul = $resolver->resolveForUser(2, 'admin');
        $this->assertSame(ItamRole::SystemItAdmin, $itNazrul['role']);
        $this->assertSame(ItamRole::SystemItAdmin, $this->loadUserByLogin('test.sysadmin')->itamRole);

        // 2. IT Support: Information Technology employees resolve as IT Support even with Administrator rol_id=1
        $testItSupport = $this->loadUserByLogin('test.itsupport');
        $this->assertSame(ItamRole::ItSupport, $testItSupport->itamRole);

        // Check real Admidio IT employee (usr_id = 4, hanisilyani) if present in test DB
        if (DB::table('adm_users')->where('usr_id', 4)->exists()) {
            $hanis = $resolver->resolveForUser(4, 'hanisilyani');
            $this->assertSame(ItamRole::ItSupport, $hanis['role']);
        }

        // 3. Department Manager: Creative leader (faris usr_id = 64 & test.manager)
        $testManager = $this->loadUserByLogin('test.manager');
        $this->assertSame(ItamRole::DepartmentManager, $testManager->itamRole);
        $this->assertContains('Creative', $testManager->managedDepartments);

        if (DB::table('adm_users')->where('usr_id', 64)->exists()) {
            $faris = $resolver->resolveForUser(64, '69');
            $this->assertSame(ItamRole::DepartmentManager, $faris['role']);
            $this->assertContains('Creative', $faris['managed_departments']);
        }

        // 4. Finance: Account Dept
        $testFinance = $this->loadUserByLogin('test.finance');
        $this->assertSame(ItamRole::Finance, $testFinance->itamRole);

        // 5. Employee: Standard employee
        $testEmployee = $this->loadUserByLogin('test.employee');
        $this->assertSame(ItamRole::Employee, $testEmployee->itamRole);

        // 6. Auditor: nazrul (usr_id = 3)
        $auditor = $this->loadUserByLogin('nazrul');
        $this->assertSame(ItamRole::Auditor, $auditor->itamRole);
        $this->assertSame(3, $auditor->usr_id);
    }

    public function test_route_permissions_enforce_six_role_boundaries(): void
    {
        // Employee can access my-assets & transactions.history, denied admin/assets/reports/audits
        $employee = $this->loadUserByLogin('test.employee');
        $this->actingAs($employee)->get(route('my-assets'))->assertOk();
        $this->actingAs($employee)->get(route('transactions.history'))->assertOk();
        $this->actingAs($employee)->get(route('assets.index'))->assertForbidden();
        $this->actingAs($employee)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($employee)->get(route('admin.users'))->assertForbidden();

        // IT Support can access assign & new audit, denied procurement & admin users
        $support = $this->loadUserByLogin('test.itsupport');
        $this->actingAs($support)->get(route('transactions.assign'))->assertOk();
        $this->actingAs($support)->get(route('audits.new'))->assertOk();
        $this->actingAs($support)->get(route('procurement.suppliers'))->assertForbidden();
        $this->actingAs($support)->get(route('admin.users'))->assertForbidden();

        // Finance can access procurement & retired assets, denied transactions.assign & admin.users
        $finance = $this->loadUserByLogin('test.finance');
        $this->actingAs($finance)->get(route('procurement.suppliers'))->assertOk();
        $this->actingAs($finance)->get(route('assets.retired'))->assertOk();
        $this->actingAs($finance)->get(route('transactions.assign'))->assertForbidden();
        $this->actingAs($finance)->get(route('admin.users'))->assertForbidden();

        // Auditor has read access across inventory, transactions.history, audits.history, admin.audit-logs,
        // and is denied write routes like transactions.assign, audits.new, admin.users
        $auditor = $this->loadUserByLogin('nazrul');
        $this->actingAs($auditor)->get(route('assets.index'))->assertOk();
        $this->actingAs($auditor)->get(route('transactions.history'))->assertOk();
        $this->actingAs($auditor)->get(route('audits.history'))->assertOk();
        $this->actingAs($auditor)->get(route('admin.audit-logs'))->assertOk();
        $this->actingAs($auditor)->get(route('transactions.assign'))->assertForbidden();
        $this->actingAs($auditor)->get(route('audits.new'))->assertForbidden();
        $this->actingAs($auditor)->get(route('admin.users'))->assertForbidden();

        // System & IT Admin can access administration routes
        $sysAdmin = $this->loadUserByLogin('test.sysadmin');
        $this->actingAs($sysAdmin)->get(route('admin.users'))->assertOk();
        $this->actingAs($sysAdmin)->get(route('admin.roles'))->assertOk();
        $this->actingAs($sysAdmin)->get(route('admin.settings'))->assertOk();
    }

    public function test_department_manager_sees_only_department_assets_and_exceptions_without_financial_costs(): void
    {
        $manager = $this->loadUserByLogin('test.manager');

        // Ensure there is a non-Creative asset with purchase cost in the DB for negative isolation check
        DB::table('fg_itam_assets')->updateOrInsert(
            ['id' => 'IT-LAP-2026-00999'],
            [
                'asset_tag' => 'TAG-LAP-2026-00999',
                'name' => 'Secret Finance Ultrabook',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Dell',
                'model' => 'XPS 13',
                'serial_number' => 'FIN-ONLY-0999',
                'status' => 'Assigned',
                'department' => 'Finance',
                'assigned_user' => 'Lee Finance',
                'assigned_location' => 'KL Office',
                'purchase_cost' => 7654.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->actingAs($manager)
            ->get(route('assets.index'))
            ->assertOk()
            ->assertSee('IT-MON-2026-00802')
            ->assertDontSee('IT-LAP-2026-00999')
            ->assertDontSee('Add Asset');

        // Out-of-scope asset detail returns 403
        $this->actingAs($manager)
            ->get(route('assets.detail', 'IT-LAP-2026-00999'))
            ->assertForbidden();

        // In-scope Creative asset detail works and hides financial cost
        $this->actingAs($manager)
            ->get(route('assets.detail', 'IT-MON-2026-00802'))
            ->assertOk()
            ->assertSee('Dell UltraSharp 27 4K')
            ->assertDontSee('RM 2,800.00');

        // Department-scoped exception visible
        $this->actingAs($manager)
            ->get(route('audits.exceptions'))
            ->assertOk()
            ->assertSee('IT-MON-2026-00802');

        // CSV complete-register omits Cost (MYR) column for Department Manager
        $csvResponse = $this->actingAs($manager)->get(route('reports.csv', 'complete-register'));
        $csvResponse->assertOk();
        $csvContent = $csvResponse->streamedContent();
        $this->assertStringNotContainsString('Cost (MYR)', $csvContent);
        $this->assertStringContainsString('IT-MON-2026-00802', $csvContent);
        $this->assertStringNotContainsString('IT-LAP-2026-00999', $csvContent);
    }

    public function test_employee_can_view_and_acknowledge_personally_assigned_equipment(): void
    {
        $employee = $this->loadUserByLogin('test.employee');

        Livewire::actingAs($employee)
            ->test(MyAssets::class)
            ->assertSee('IT-LAP-2026-00803')
            ->assertSee('Lenovo ThinkPad T14 Gen 5')
            ->assertSee('Due 2026-12-31')
            ->set('remarks', 'Laptop and USB-C charger received in good condition')
            ->call('acknowledge', 'IT-LAP-2026-00803')
            ->assertSee('Acknowledged');

        $ack = AssetAcknowledgement::where('asset_id', 'IT-LAP-2026-00803')
            ->where('usr_id', $employee->usr_id)
            ->first();

        $this->assertNotNull($ack);
        $this->assertSame('Acknowledged', $ack->status);
    }

    public function test_auditor_and_unauthorized_roles_cannot_mutate_assets(): void
    {
        $auditor = $this->loadUserByLogin('nazrul');

        Livewire::actingAs($auditor)
            ->test(AllAssets::class)
            ->call('saveAsset')
            ->assertForbidden();
    }

    public function test_system_it_admin_can_assign_role_override_and_log_audit_entry(): void
    {
        $sysAdmin = $this->loadUserByLogin('test.sysadmin');
        $target = $this->loadUserByLogin('test.employee');

        Livewire::actingAs($sysAdmin)
            ->test(AdminUsersComponent::class)
            ->call('openEdit', $target->usr_id)
            ->set('editRole', ItamRole::Auditor->value)
            ->set('editIsActive', true)
            ->set('editNotes', 'Temporary internal audit rotation')
            ->call('saveUserRole')
            ->assertHasNoErrors();

        $updated = $this->loadUserByLogin('test.employee');
        $this->assertSame(ItamRole::Auditor, $updated->itamRole);
        $this->assertSame('override', $updated->roleSource);
    }
}
