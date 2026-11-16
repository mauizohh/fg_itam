<?php

namespace App\Enums;

enum ItamRole: string
{
    case SystemItAdmin = 'system_it_admin';
    case ItSupport = 'it_support';
    case DepartmentManager = 'department_manager';
    case Finance = 'finance';
    case Employee = 'employee';
    case Auditor = 'auditor';

    /**
     * Human-readable display label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::SystemItAdmin => 'System & IT Admin',
            self::ItSupport => 'IT Support',
            self::DepartmentManager => 'Department Manager',
            self::Finance => 'Finance',
            self::Employee => 'Employee',
            self::Auditor => 'Auditor',
        };
    }

    /**
     * Brief scope summary aligned with Section 9 Roles and Permissions.
     */
    public function description(): string
    {
        return match ($this) {
            self::SystemItAdmin => 'Full configuration, security, user roles, master data, integrations, audit logs, and operational asset lifecycle management.',
            self::ItSupport => 'Asset lookup, check-in/check-out, assignment, return, repair updates, and physical audits within assigned scope.',
            self::DepartmentManager => 'Read access to assets assigned to their department and relevant audit exceptions.',
            self::Finance => 'Purchase data, asset value, supplier information, cost centre, disposal, and financial reports.',
            self::Employee => 'View and acknowledge personally assigned equipment and return obligations.',
            self::Auditor => 'Read-only access to inventory, transaction history, audit records, and selected verification evidence.',
        };
    }

    /**
     * Badge CSS class for UI display.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::SystemItAdmin => 'pill critical',
            self::ItSupport => 'pill normal',
            self::DepartmentManager => 'pill high',
            self::Finance => 'pill low',
            self::Employee => 'pill closed',
            self::Auditor => 'pill in-progress',
        };
    }

    /**
     * Parse from stored string value with safe fallback.
     */
    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::tryFrom(strtolower(trim($value)));
    }

    /**
     * Key-value options for dropdowns.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $role) {
            $options[$role->value] = $role->label();
        }

        return $options;
    }
}
