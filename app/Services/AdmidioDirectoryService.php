<?php

namespace App\Services;

use App\Auth\AdmidioRoleResolver;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdmidioDirectoryService
{
    public const CATEGORY_ID_DEPARTMENT = 7;

    public function __construct(
        private readonly ?AdmidioRoleResolver $roleResolver = null
    ) {}

    /**
     * Ensure departments and employees are synced from Admidio database.
     */
    public static function ensureSynced(): void
    {
        try {
            $service = app(self::class);

            if (Schema::hasTable('fg_itam_departments') && Schema::hasTable('adm_roles')) {
                $hasAdmidioDepts = DB::table('fg_itam_departments')
                    ->whereIn('name', ['Account Dept', 'Information Technology', 'Human Resources'])
                    ->count() >= 3;

                if (! $hasAdmidioDepts) {
                    $service->syncDepartments();
                }
            }

            if (Schema::hasTable('fg_itam_employees') && Schema::hasTable('adm_users')) {
                $employeeCount = DB::table('fg_itam_employees')->count();
                if ($employeeCount < 50) {
                    $service->syncEmployees();
                }
            }
        } catch (\Throwable $e) {
            // Silently fall back if running in constrained environments
        }
    }

    /**
     * Sync departments from Admidio adm_roles (rol_cat_id = 7) into fg_itam_departments.
     */
    public function syncDepartments(): int
    {
        if (! Schema::hasTable('adm_roles') || ! Schema::hasTable('fg_itam_departments')) {
            return 0;
        }

        $roles = DB::table('adm_roles as r')
            ->where('r.rol_cat_id', self::CATEGORY_ID_DEPARTMENT)
            ->where('r.rol_valid', 1)
            ->select('r.rol_id', 'r.rol_name')
            ->orderBy('r.rol_name')
            ->get();

        if ($roles->isEmpty()) {
            return 0;
        }

        $roleNames = $roles->pluck('rol_name')->map(fn ($n) => (string) $n)->all();

        // Remove legacy mock departments that do not exist in Admidio roles
        DB::table('fg_itam_departments')->whereNotIn('name', $roleNames)->delete();

        // Retrieve department leaders
        $leaders = [];
        if (Schema::hasTable('adm_members') && Schema::hasTable('adm_users')) {
            $fieldIds = Schema::hasTable('adm_user_fields')
                ? DB::table('adm_user_fields')
                    ->whereIn('usf_name_intern', ['FIRST_NAME', 'LAST_NAME'])
                    ->pluck('usf_id', 'usf_name_intern')
                : collect();

            $firstId = (int) ($fieldIds->get('FIRST_NAME') ?? 2);
            $lastId = (int) ($fieldIds->get('LAST_NAME') ?? 1);

            $leaderRows = DB::table('adm_members as m')
                ->join('adm_users as u', 'u.usr_id', '=', 'm.mem_usr_id')
                ->leftJoin('adm_user_data as fn', function ($j) use ($firstId): void {
                    $j->on('fn.usd_usr_id', '=', 'u.usr_id')->where('fn.usd_usf_id', $firstId);
                })
                ->leftJoin('adm_user_data as ln', function ($j) use ($lastId): void {
                    $j->on('ln.usd_usr_id', '=', 'u.usr_id')->where('ln.usd_usf_id', $lastId);
                })
                ->where('m.mem_leader', 1)
                ->where('u.usr_valid', 1)
                ->select([
                    'm.mem_rol_id',
                    'u.usr_login_name',
                    'fn.usd_value as first_name',
                    'ln.usd_value as last_name',
                ])
                ->get();

            foreach ($leaderRows as $lRow) {
                $first = trim((string) ($lRow->first_name ?? ''));
                $last = trim((string) ($lRow->last_name ?? ''));
                $cleanName = User::cleanDisplayName(trim("{$first} {$last}"), (string) $lRow->usr_login_name);
                if (! isset($leaders[$lRow->mem_rol_id])) {
                    $leaders[$lRow->mem_rol_id] = $cleanName;
                }
            }
        }

        $resolver = $this->roleResolver ?? app(AdmidioRoleResolver::class);

        $count = 0;
        $now = now();

        foreach ($roles as $role) {
            $name = (string) $role->rol_name;
            $leader = $leaders[$role->rol_id] ?? '—';

            $aliases = $resolver->expandDepartmentAliases([$name]);

            $assetsQuery = Asset::query()->whereIn('department', $aliases);

            $activeCount = (clone $assetsQuery)->whereIn('status', ['Assigned', 'Loan'])->count();
            $availableCount = (clone $assetsQuery)->where('status', 'Available')->count();
            $repairCount = (clone $assetsQuery)->where('status', 'Repair')->count();
            $missingCount = (clone $assetsQuery)->where('status', 'Missing')->count();
            $purchaseValue = (float) (clone $assetsQuery)->sum('purchase_cost');

            DB::table('fg_itam_departments')->updateOrInsert(
                ['name' => $name],
                [
                    'owner_title' => $leader,
                    'active_assets_count' => $activeCount,
                    'available_count' => $availableCount,
                    'repair_count' => $repairCount,
                    'missing_count' => $missingCount,
                    'purchase_value' => $purchaseValue,
                    'exceptions_count' => 0,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
            $count++;
        }

        return $count;
    }

    /**
     * Sync users from Admidio adm_users + adm_user_data + adm_members into fg_itam_employees.
     */
    public function syncEmployees(): int
    {
        if (! Schema::hasTable('adm_users') || ! Schema::hasTable('fg_itam_employees')) {
            return 0;
        }

        $fieldIds = Schema::hasTable('adm_user_fields')
            ? DB::table('adm_user_fields')
                ->whereIn('usf_name_intern', ['FIRST_NAME', 'LAST_NAME', 'EMAIL'])
                ->pluck('usf_id', 'usf_name_intern')
            : collect();

        $firstId = (int) ($fieldIds->get('FIRST_NAME') ?? 2);
        $lastId = (int) ($fieldIds->get('LAST_NAME') ?? 1);
        $emailId = (int) ($fieldIds->get('EMAIL') ?? 11);

        // Fetch user departments from active memberships
        $userDepartments = [];
        if (Schema::hasTable('adm_members') && Schema::hasTable('adm_roles')) {
            $memberRows = DB::table('adm_members as m')
                ->join('adm_roles as r', 'r.rol_id', '=', 'm.mem_rol_id')
                ->where('r.rol_cat_id', self::CATEGORY_ID_DEPARTMENT)
                ->where('r.rol_valid', 1)
                ->where(function ($q): void {
                    $q->whereNull('m.mem_begin')->orWhere('m.mem_begin', '<=', now()->toDateString());
                })
                ->where(function ($q): void {
                    $q->whereNull('m.mem_end')->orWhere('m.mem_end', '>=', now()->toDateString());
                })
                ->select('m.mem_usr_id', 'r.rol_name', 'm.mem_leader')
                ->orderByDesc('m.mem_leader')
                ->get();

            foreach ($memberRows as $mRow) {
                if (! isset($userDepartments[$mRow->mem_usr_id])) {
                    $userDepartments[$mRow->mem_usr_id] = (string) $mRow->rol_name;
                }
            }
        }

        $rawUsers = DB::table('adm_users as u')
            ->leftJoin('adm_user_data as fn', function ($j) use ($firstId): void {
                $j->on('fn.usd_usr_id', '=', 'u.usr_id')->where('fn.usd_usf_id', $firstId);
            })
            ->leftJoin('adm_user_data as ln', function ($j) use ($lastId): void {
                $j->on('ln.usd_usr_id', '=', 'u.usr_id')->where('ln.usd_usf_id', $lastId);
            })
            ->leftJoin('adm_user_data as em', function ($j) use ($emailId): void {
                $j->on('em.usd_usr_id', '=', 'u.usr_id')->where('em.usd_usf_id', $emailId);
            })
            ->where('u.usr_valid', 1)
            ->select([
                'u.usr_id',
                'u.usr_login_name',
                'u.usr_email',
                'fn.usd_value as first_name',
                'ln.usd_value as last_name',
                'em.usd_value as data_email',
            ])
            ->get();

        if ($rawUsers->isEmpty()) {
            return 0;
        }

        // Get active assets by custodian
        $activeAssets = Asset::query()
            ->whereNotNull('assigned_user')
            ->where('assigned_user', '!=', '')
            ->whereIn('status', ['Assigned', 'Loan'])
            ->get(['id', 'assigned_user', 'status', 'expected_return_date', 'purchase_cost']);

        $now = now();
        $records = [];
        $syncedIds = [];

        foreach ($rawUsers as $u) {
            $usrId = (int) $u->usr_id;
            $loginName = (string) ($u->usr_login_name ?? '');
            $first = trim((string) ($u->first_name ?? ''));
            $last = trim((string) ($u->last_name ?? ''));
            $fullName = trim("{$first} {$last}");
            $hasDepartment = isset($userDepartments[$usrId]);

            // Filter out anonymous database placeholders that lack login name, department, and named identity
            $isRecognizable = (strlen($fullName) > 2 && ! is_numeric($fullName))
                || ($loginName !== '' && strlen($loginName) > 1)
                || $hasDepartment;

            if (! $isRecognizable) {
                continue;
            }

            $displayName = User::cleanDisplayName($fullName, $loginName !== '' ? $loginName : "User #{$usrId}");
            $email = $u->usr_email ?: ($u->data_email ?: null);
            $dept = $userDepartments[$usrId] ?? 'General';

            $empLower = strtolower(trim($displayName));
            $matched = $activeAssets->filter(function (Asset $a) use ($empLower): bool {
                $u = strtolower(trim((string) $a->assigned_user));

                return $u === $empLower || str_contains($u, $empLower) || str_contains($empLower, $u);
            });

            $assignedCount = $matched->count();
            $pendingCount = $matched->filter(
                fn (Asset $a): bool => $a->status === 'Loan' || ! empty($a->expected_return_date)
            )->count();
            $assetValue = (float) $matched->sum('purchase_cost');

            $records[] = [
                'id' => $usrId,
                'employee_id' => 'EMP-'.str_pad((string) $usrId, 4, '0', STR_PAD_LEFT),
                'name' => $displayName,
                'email' => $email,
                'department' => $dept,
                'location' => 'Jitra Office',
                'assigned_assets_count' => $assignedCount,
                'pending_returns_count' => $pendingCount,
                'exceptions_count' => 0,
                'asset_value' => $assetValue,
                'status' => 'Active',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $syncedIds[] = $usrId;
        }

        // Remove old mock records from fg_itam_employees that are not in the synced Admidio ID list
        if (! empty($syncedIds)) {
            DB::table('fg_itam_employees')->whereNotIn('id', $syncedIds)->delete();
        }

        // Upsert in chunks
        foreach (array_chunk($records, 200) as $chunk) {
            DB::table('fg_itam_employees')->upsert(
                $chunk,
                ['id'],
                ['name', 'email', 'department', 'assigned_assets_count', 'pending_returns_count', 'asset_value', 'status', 'updated_at']
            );
        }

        return count($records);
    }

    /**
     * Refresh assigned assets and pending returns counts for all employees.
     */
    public function syncEmployeeAssetCounts(): void
    {
        if (! Schema::hasTable('fg_itam_employees') || ! Schema::hasTable('fg_itam_assets')) {
            return;
        }

        $activeAssets = Asset::query()
            ->whereNotNull('assigned_user')
            ->where('assigned_user', '!=', '')
            ->whereIn('status', ['Assigned', 'Loan'])
            ->get(['id', 'assigned_user', 'status', 'expected_return_date', 'purchase_cost']);

        $employees = DB::table('fg_itam_employees')->select('id', 'name')->get();

        foreach ($employees as $emp) {
            $empLower = strtolower(trim((string) $emp->name));
            $matched = $activeAssets->filter(function (Asset $a) use ($empLower): bool {
                $u = strtolower(trim((string) $a->assigned_user));

                return $u === $empLower || str_contains($u, $empLower) || str_contains($empLower, $u);
            });

            $assignedCount = $matched->count();
            $pendingCount = $matched->filter(
                fn (Asset $a): bool => $a->status === 'Loan' || ! empty($a->expected_return_date)
            )->count();
            $val = (float) $matched->sum('purchase_cost');

            DB::table('fg_itam_employees')
                ->where('id', $emp->id)
                ->update([
                    'assigned_assets_count' => $assignedCount,
                    'pending_returns_count' => $pendingCount,
                    'asset_value' => $val,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Perform full sync of departments and employees.
     *
     * @return array{departments: int, employees: int}
     */
    public function syncAll(): array
    {
        return [
            'departments' => $this->syncDepartments(),
            'employees' => $this->syncEmployees(),
        ];
    }
}
