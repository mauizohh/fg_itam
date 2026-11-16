<?php

namespace App\Auth;

use App\Enums\ItamRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdmidioRoleResolver
{
    private const ROLE_ID_ADMIN = 1;

    private const ROLE_ID_ACCOUNT = 8;

    private const ROLE_ID_IT = 17;

    private const CATEGORY_ID_DEPARTMENT = 7;

    /**
     * Resolve Admidio profile fields, active memberships, and effective ITAM role for a user.
     *
     * @return array{
     *     role: ItamRole,
     *     role_source: string,
     *     is_active: bool,
     *     departments: list<string>,
     *     managed_departments: list<string>,
     *     admidio_roles: list<array{rol_id: int, rol_name: string, rol_cat_id: int, mem_leader: int}>
     * }
     */
    public function resolveForUser(int $usrId, ?string $loginName = null): array
    {
        $memberships = DB::table('adm_members as m')
            ->join('adm_roles as r', 'r.rol_id', '=', 'm.mem_rol_id')
            ->where('m.mem_usr_id', $usrId)
            ->where('r.rol_valid', 1)
            ->where(function ($query): void {
                $query->whereNull('m.mem_begin')
                    ->orWhere('m.mem_begin', '<=', now()->toDateString());
            })
            ->where(function ($query): void {
                $query->whereNull('m.mem_end')
                    ->orWhere('m.mem_end', '>=', now()->toDateString());
            })
            ->select('r.rol_id', 'r.rol_name', 'r.rol_cat_id', 'm.mem_leader')
            ->orderBy('r.rol_id')
            ->get();

        $admidioRoles = [];
        $departments = [];
        $managedDepartments = [];

        foreach ($memberships as $row) {
            $rolId = (int) $row->rol_id;
            $rolCatId = (int) $row->rol_cat_id;
            $rolName = (string) $row->rol_name;
            $isLeader = (int) $row->mem_leader === 1;

            $admidioRoles[] = [
                'rol_id' => $rolId,
                'rol_name' => $rolName,
                'rol_cat_id' => $rolCatId,
                'mem_leader' => $isLeader ? 1 : 0,
            ];

            if ($rolCatId === self::CATEGORY_ID_DEPARTMENT) {
                if (! in_array($rolName, $departments, true)) {
                    $departments[] = $rolName;
                }

                if ($isLeader && ! in_array($rolName, $managedDepartments, true)) {
                    $managedDepartments[] = $rolName;
                }
            }
        }

        $override = null;
        if (Schema::hasTable('fg_itam_user_roles')) {
            $override = DB::table('fg_itam_user_roles')
                ->where('usr_id', $usrId)
                ->first();
        }

        $isActive = $override ? (bool) $override->is_active : true;
        $overrideRole = $override ? ItamRole::tryFromValue($override->role ?? null) : null;

        if ($overrideRole !== null) {
            return [
                'role' => $overrideRole,
                'role_source' => 'override',
                'is_active' => $isActive,
                'departments' => $departments,
                'managed_departments' => $managedDepartments,
                'admidio_roles' => $admidioRoles,
            ];
        }

        $derivedRole = $this->deriveRole($usrId, $loginName, $admidioRoles);

        return [
            'role' => $derivedRole,
            'role_source' => 'admidio',
            'is_active' => $isActive,
            'departments' => $departments,
            'managed_departments' => $managedDepartments,
            'admidio_roles' => $admidioRoles,
        ];
    }

    /**
     * Expand Admidio department names to include ITAM asset/department aliases for scoped queries.
     *
     * @param  list<string>  $departments
     * @return list<string>
     */
    public function expandDepartmentAliases(array $departments): array
    {
        $aliases = [
            'Account Dept' => ['Account Dept', 'Account', 'Finance'],
            'Information Technology' => ['Information Technology', 'IT Operations', 'IT Support', 'Service Desk', 'Infrastructure'],
            'Human Resources' => ['Human Resources', 'Human Resource', 'HR'],
            'Research & Development' => ['Research & Development', 'R&D'],
            'Safety and Aircond Maintenance' => ['Safety and Aircond Maintenance', 'Safety'],
            'Production Management' => ['Production Management', 'Operations'],
        ];

        $expanded = [];
        foreach ($departments as $dept) {
            $expanded[] = $dept;
            if (isset($aliases[$dept])) {
                foreach ($aliases[$dept] as $alias) {
                    $expanded[] = $alias;
                }
            }
        }

        return array_values(array_unique($expanded));
    }

    /**
     * Derive role dynamically from Admidio memberships when no ITAM override exists.
     *
     * @param  list<array{rol_id: int, rol_name: string, rol_cat_id: int, mem_leader: int}>  $admidioRoles
     */
    private function deriveRole(int $usrId, ?string $loginName, array $admidioRoles): ItamRole
    {
        if ($usrId === 2 || strtolower((string) $loginName) === 'admin') {
            return ItamRole::SystemItAdmin;
        }

        $isItMember = false;
        $isAccountMember = false;
        $isDeptLeader = false;

        foreach ($admidioRoles as $role) {
            $rolId = $role['rol_id'];
            $rolName = strtolower(trim($role['rol_name']));
            $rolCatId = $role['rol_cat_id'];
            $isLeader = $role['mem_leader'] === 1;

            if ($rolId === self::ROLE_ID_IT || $rolName === 'information technology') {
                $isItMember = true;
            }

            if ($rolId === self::ROLE_ID_ACCOUNT || str_starts_with($rolName, 'account')) {
                $isAccountMember = true;
            }

            if (
                $rolCatId === self::CATEGORY_ID_DEPARTMENT
                && $isLeader
                && $rolId !== self::ROLE_ID_IT
                && $rolId !== self::ROLE_ID_ACCOUNT
            ) {
                $isDeptLeader = true;
            }
        }

        if ($isItMember) {
            return ItamRole::ItSupport;
        }

        if ($isAccountMember) {
            return ItamRole::Finance;
        }

        if ($isDeptLeader) {
            return ItamRole::DepartmentManager;
        }

        return ItamRole::Employee;
    }
}
