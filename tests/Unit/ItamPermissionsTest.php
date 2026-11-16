<?php

namespace Tests\Unit;

use App\Enums\ItamRole;
use App\Support\ItamPermissions;
use PHPUnit\Framework\TestCase;

class ItamPermissionsTest extends TestCase
{
    public function test_role_enum_exposes_all_six_combined_roles(): void
    {
        $values = array_map(fn (ItamRole $role): string => $role->value, ItamRole::cases());

        $this->assertSame([
            'system_it_admin',
            'it_support',
            'department_manager',
            'finance',
            'employee',
            'auditor',
        ], $values);
    }

    public function test_permission_matrix_enforces_key_boundaries(): void
    {
        $this->assertTrue(ItamPermissions::hasPermission(ItamRole::SystemItAdmin, 'admin.users'));
        $this->assertTrue(ItamPermissions::hasPermission(ItamRole::SystemItAdmin, 'transactions.manage'));

        $this->assertFalse(ItamPermissions::hasPermission(ItamRole::ItSupport, 'financial.view'));
        $this->assertFalse(ItamPermissions::hasPermission(ItamRole::ItSupport, 'admin.users'));

        $this->assertFalse(ItamPermissions::hasPermission(ItamRole::DepartmentManager, 'financial.view'));
        $this->assertTrue(ItamPermissions::hasPermission(ItamRole::DepartmentManager, 'audits.exceptions.view'));

        $this->assertTrue(ItamPermissions::hasPermission(ItamRole::Finance, 'financial.view'));
        $this->assertFalse(ItamPermissions::hasPermission(ItamRole::Finance, 'transactions.manage'));

        $this->assertTrue(ItamPermissions::hasPermission(ItamRole::Employee, 'my_assets.acknowledge'));
        $this->assertFalse(ItamPermissions::hasPermission(ItamRole::Employee, 'assets.view'));

        $this->assertTrue(ItamPermissions::hasPermission(ItamRole::Auditor, 'admin.audit_logs'));
        $this->assertFalse(ItamPermissions::hasPermission(ItamRole::Auditor, 'assets.create'));
    }
}
