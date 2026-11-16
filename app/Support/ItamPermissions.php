<?php

namespace App\Support;

use App\Enums\ItamRole;

class ItamPermissions
{
    /**
     * Role-to-permission matrix matching Section 9 (Roles and Permissions).
     *
     * @var array<string, list<string>>
     */
    private const ROLE_PERMISSIONS = [
        ItamRole::SystemItAdmin->value => [
            'dashboard.view',
            'my_assets.view',
            'my_assets.acknowledge',
            'assets.view',
            'assets.lookup',
            'assets.create',
            'assets.edit',
            'assets.retired.view',
            'assets.dispose',
            'transactions.manage',
            'transactions.history',
            'people.view',
            'procurement.view',
            'procurement.manage',
            'maintenance.view',
            'maintenance.manage',
            'software.view',
            'software.manage',
            'audits.scan',
            'audits.history.view',
            'audits.exceptions.view',
            'reports.view',
            'financial.view',
            'admin.categories',
            'admin.locations',
            'admin.users',
            'admin.roles',
            'admin.settings',
            'admin.audit_logs',
        ],
        ItamRole::ItSupport->value => [
            'dashboard.view',
            'my_assets.view',
            'my_assets.acknowledge',
            'assets.view',
            'assets.lookup',
            'assets.edit',
            'assets.retired.view',
            'transactions.manage',
            'transactions.history',
            'people.view',
            'maintenance.view',
            'maintenance.manage',
            'software.view',
            'audits.scan',
            'audits.history.view',
            'audits.exceptions.view',
            'reports.view',
        ],
        ItamRole::DepartmentManager->value => [
            'dashboard.view',
            'my_assets.view',
            'my_assets.acknowledge',
            'assets.view',
            'assets.lookup',
            'transactions.history',
            'people.view',
            'maintenance.view',
            'audits.exceptions.view',
            'reports.view',
        ],
        ItamRole::Finance->value => [
            'dashboard.view',
            'my_assets.view',
            'my_assets.acknowledge',
            'assets.view',
            'assets.lookup',
            'assets.retired.view',
            'assets.dispose',
            'people.view',
            'procurement.view',
            'procurement.manage',
            'maintenance.view',
            'software.view',
            'software.manage',
            'reports.view',
            'financial.view',
        ],
        ItamRole::Employee->value => [
            'dashboard.view',
            'my_assets.view',
            'my_assets.acknowledge',
            'transactions.history',
        ],
        ItamRole::Auditor->value => [
            'dashboard.view',
            'my_assets.view',
            'my_assets.acknowledge',
            'assets.view',
            'assets.lookup',
            'assets.retired.view',
            'transactions.history',
            'people.view',
            'procurement.view',
            'maintenance.view',
            'software.view',
            'audits.history.view',
            'audits.exceptions.view',
            'reports.view',
            'financial.view',
            'admin.audit_logs',
        ],
    ];

    /**
     * Get all permissions granted to a role.
     *
     * @return list<string>
     */
    public static function permissionsFor(ItamRole|string $role): array
    {
        $key = $role instanceof ItamRole ? $role->value : $role;

        return self::ROLE_PERMISSIONS[$key] ?? [];
    }

    /**
     * Determine whether a role has the specified permission.
     */
    public static function hasPermission(ItamRole|string $role, string $permission): bool
    {
        return in_array($permission, self::permissionsFor($role), true);
    }

    /**
     * Structured permission matrix rows for the Administration -> Roles view.
     *
     * @return list<array{
     *     perm: string,
     *     system_it_admin: string,
     *     it_support: string,
     *     department_manager: string,
     *     finance: string,
     *     employee: string,
     *     auditor: string
     * }>
     */
    public static function matrixForUi(): array
    {
        return [
            [
                'perm' => 'Full configuration, security, user roles & audit logs',
                'system_it_admin' => 'Yes',
                'it_support' => 'No',
                'department_manager' => 'No',
                'finance' => 'No',
                'employee' => 'No',
                'auditor' => 'Read (Logs)',
            ],
            [
                'perm' => 'Register & edit operational asset records',
                'system_it_admin' => 'Yes',
                'it_support' => 'Limited',
                'department_manager' => 'Read (Dept)',
                'finance' => 'Read',
                'employee' => 'Own Only',
                'auditor' => 'Read',
            ],
            [
                'perm' => 'Assign, return, transfer & loan assets',
                'system_it_admin' => 'Yes',
                'it_support' => 'Yes',
                'department_manager' => 'Read (Dept)',
                'finance' => 'No',
                'employee' => 'Own History',
                'auditor' => 'Read History',
            ],
            [
                'perm' => 'Maintenance & repair updates',
                'system_it_admin' => 'Yes',
                'it_support' => 'Yes',
                'department_manager' => 'Read (Dept)',
                'finance' => 'Read',
                'employee' => 'No',
                'auditor' => 'Read',
            ],
            [
                'perm' => 'Procurement, suppliers, asset value & disposal',
                'system_it_admin' => 'Yes',
                'it_support' => 'No',
                'department_manager' => 'No',
                'finance' => 'Yes',
                'employee' => 'No',
                'auditor' => 'Read',
            ],
            [
                'perm' => 'Run physical audits & view exceptions',
                'system_it_admin' => 'Yes',
                'it_support' => 'Yes',
                'department_manager' => 'Read (Dept)',
                'finance' => 'No',
                'employee' => 'No',
                'auditor' => 'Read',
            ],
            [
                'perm' => 'View & acknowledge personally assigned equipment',
                'system_it_admin' => 'Yes',
                'it_support' => 'Yes',
                'department_manager' => 'Yes',
                'finance' => 'Yes',
                'employee' => 'Yes',
                'auditor' => 'Yes',
            ],
            [
                'perm' => 'Reports & compliance exports',
                'system_it_admin' => 'Yes',
                'it_support' => 'Operational',
                'department_manager' => 'Dept Scope',
                'finance' => 'Financial',
                'employee' => 'No',
                'auditor' => 'Read',
            ],
        ];
    }
}
