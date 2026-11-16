<?php

namespace App\Models;

use App\Enums\ItamRole;
use App\Support\ItamPermissions;
use Illuminate\Contracts\Auth\Authenticatable;

class User implements Authenticatable
{
    /**
     * @param  list<string>  $departments
     * @param  list<string>  $managedDepartments
     * @param  list<string>  $expandedDepartmentScope
     * @param  list<array{rol_id: int, rol_name: string, rol_cat_id: int, mem_leader: int}>  $admidioRoles
     */
    public function __construct(
        public int $usr_id,
        public string $username,
        public string $name,
        public ?string $email,
        public string $passwordHash,
        public ItamRole $itamRole,
        public string $roleSource = 'admidio',
        public bool $isActive = true,
        public array $departments = [],
        public array $managedDepartments = [],
        public array $expandedDepartmentScope = [],
        public array $admidioRoles = [],
        public ?string $lastLogin = null,
    ) {}

    public function getAuthIdentifierName(): string
    {
        return 'usr_id';
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->usr_id;
    }

    public function getAuthPasswordName(): string
    {
        return 'usr_password';
    }

    public function getAuthPassword(): string
    {
        return $this->passwordHash;
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
        // Remember tokens are not stored on shared adm_users table.
    }

    public function getRememberTokenName(): string
    {
        return '';
    }

    /**
     * Get the role value string (e.g., 'system_it_admin').
     */
    public function getRoleAttribute(): string
    {
        return $this->itamRole->value;
    }

    /**
     * Get the human-readable role label (e.g., 'System & IT Admin').
     */
    public function getRoleLabelAttribute(): string
    {
        return $this->itamRole->label();
    }

    /**
     * Get two-letter uppercase initials for avatar display.
     */
    public function getInitialsAttribute(): string
    {
        $parts = array_values(array_filter(explode(' ', trim($this->name))));
        $initials = '';
        foreach ($parts as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
            if (strlen($initials) >= 2) {
                break;
            }
        }

        return $initials !== '' ? $initials : 'FG';
    }

    /**
     * Primary department label for display.
     */
    public function getPrimaryDepartmentAttribute(): string
    {
        if (! empty($this->managedDepartments)) {
            return $this->managedDepartments[0];
        }

        if (! empty($this->departments)) {
            return $this->departments[0];
        }

        return 'General';
    }

    /**
     * Check whether the user holds one of the given roles.
     *
     * @param  ItamRole|string|list<ItamRole|string>  $roles
     */
    public function hasRole(ItamRole|string|array $roles): bool
    {
        $list = is_array($roles) ? $roles : [$roles];

        foreach ($list as $role) {
            $target = $role instanceof ItamRole ? $role->value : $role;
            if ($this->itamRole->value === $target) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether the user has the given permission.
     */
    public function hasPermission(string $permission): bool
    {
        return ItamPermissions::hasPermission($this->itamRole, $permission);
    }

    /**
     * Convenience helper for financial column/report visibility.
     */
    public function canViewFinancialData(): bool
    {
        return $this->hasPermission('financial.view');
    }

    /**
     * Convenience helper to check if the user is scoped to department(s) only.
     */
    public function isDepartmentScoped(): bool
    {
        return $this->itamRole === ItamRole::DepartmentManager;
    }

    /**
     * Return department filter names for Department Manager scoping.
     *
     * @return list<string>
     */
    public function departmentScopeNames(): array
    {
        if (! empty($this->expandedDepartmentScope)) {
            return $this->expandedDepartmentScope;
        }

        return ! empty($this->managedDepartments) ? $this->managedDepartments : $this->departments;
    }

    /**
     * Check whether the user is permitted to access records for the given department.
     */
    public function canAccessDepartment(?string $department): bool
    {
        if (! $this->isDepartmentScoped()) {
            return true;
        }

        if ($department === null || trim($department) === '') {
            return false;
        }

        $target = strtolower(trim($department));
        $allowed = array_map(fn (string $d): string => strtolower(trim($d)), $this->departmentScopeNames());

        return in_array($target, $allowed, true);
    }

    /**
     * Constrain an Eloquent/Query Builder instance to this user's department scope when applicable.
     */
    public function scopeDepartmentQuery(mixed $query, string $column = 'department'): mixed
    {
        if (! $this->isDepartmentScoped()) {
            return $query;
        }

        $names = array_values(array_unique(array_filter(array_map(
            fn (string $d): string => strtolower(trim($d)),
            $this->departmentScopeNames()
        ))));

        if (empty($names)) {
            return $query->whereRaw('1 = 0');
        }

        $placeholders = implode(',', array_fill(0, count($names), '?'));

        return $query->whereRaw("LOWER(COALESCE({$column}, '')) IN ({$placeholders})", $names);
    }

    /**
     * Dynamic property compatibility for attributes like $user->role or $user->initials.
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'id' => $this->usr_id,
            'role' => $this->getRoleAttribute(),
            'role_label' => $this->getRoleLabelAttribute(),
            'initials' => $this->getInitialsAttribute(),
            'primary_department' => $this->getPrimaryDepartmentAttribute(),
            default => null,
        };
    }
}
