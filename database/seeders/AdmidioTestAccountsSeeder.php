<?php

namespace Database\Seeders;

use App\Enums\ItamRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdmidioTestAccountsSeeder extends Seeder
{
    private const TEST_PASSWORD = 'Test@1234';

    public function run(): void
    {
        $passwordHash = Hash::make(self::TEST_PASSWORD);

        // 1. Explicit ITAM role assignments for existing Admidio accounts:
        //    - usr_id 2 ('admin', 'it nazrul') => System & IT Admin (password left untouched)
        //    - usr_id 3 ('nazrul', 'test test') => Auditor (dev test password set)
        $this->upsertItamRole(2, ItamRole::SystemItAdmin, 'Primary System & IT Admin (it nazrul)');
        $this->upsertItamRole(3, ItamRole::Auditor, 'Designated Auditor account (nazrul / test test)');

        if (DB::table('adm_users')->where('usr_id', 3)->exists()) {
            DB::table('adm_users')
                ->where('usr_id', 3)
                ->update([
                    'usr_password' => $passwordHash,
                    'usr_valid' => 1,
                ]);
        }

        // 2. Cloned/test Admidio accounts covering all remaining roles without mutating real staff passwords.
        $testAccounts = [
            [
                'login' => 'test.sysadmin',
                'first_name' => 'It Nazrul',
                'last_name' => '(Test Admin)',
                'email' => 'test.sysadmin@firstgraderesources.com',
                'memberships' => [
                    ['rol_id' => 1, 'mem_leader' => 0],
                    ['rol_id' => 2, 'mem_leader' => 0],
                    ['rol_id' => 17, 'mem_leader' => 1],
                ],
                'itam_role' => ItamRole::SystemItAdmin,
                'notes' => 'Cloned System & IT Admin test account',
            ],
            [
                'login' => 'test.itsupport',
                'first_name' => 'Hanis Support',
                'last_name' => '(Test IT)',
                'email' => 'test.itsupport@firstgraderesources.com',
                'memberships' => [
                    ['rol_id' => 1, 'mem_leader' => 0],
                    ['rol_id' => 2, 'mem_leader' => 0],
                    ['rol_id' => 17, 'mem_leader' => 0],
                ],
                'itam_role' => null, // Derived dynamically as IT Support despite Administrator rol_id=1
                'notes' => null,
            ],
            [
                'login' => 'test.manager',
                'first_name' => 'Faris Manager',
                'last_name' => '(Test Dept Mgr)',
                'email' => 'test.manager@firstgraderesources.com',
                'memberships' => [
                    ['rol_id' => 2, 'mem_leader' => 0],
                    ['rol_id' => 4, 'mem_leader' => 1], // Creative leader
                ],
                'itam_role' => null, // Derived dynamically as Department Manager
                'notes' => null,
            ],
            [
                'login' => 'test.finance',
                'first_name' => 'Nurhana Finance',
                'last_name' => '(Test Account)',
                'email' => 'test.finance@firstgraderesources.com',
                'memberships' => [
                    ['rol_id' => 2, 'mem_leader' => 0],
                    ['rol_id' => 8, 'mem_leader' => 0], // Account Dept
                ],
                'itam_role' => null, // Derived dynamically as Finance
                'notes' => null,
            ],
            [
                'login' => 'test.employee',
                'first_name' => 'Azril Employee',
                'last_name' => '(Test Staff)',
                'email' => 'test.employee@firstgraderesources.com',
                'memberships' => [
                    ['rol_id' => 2, 'mem_leader' => 0],
                    ['rol_id' => 10, 'mem_leader' => 0], // Quality Department
                ],
                'itam_role' => null, // Derived dynamically as Employee
                'notes' => null,
            ],
        ];

        foreach ($testAccounts as $account) {
            $usrId = $this->upsertAdmidioUser(
                login: $account['login'],
                email: $account['email'],
                passwordHash: $passwordHash,
                firstName: $account['first_name'],
                lastName: $account['last_name'],
                memberships: $account['memberships']
            );

            if ($account['itam_role'] !== null) {
                $this->upsertItamRole($usrId, $account['itam_role'], $account['notes']);
            } else {
                DB::table('fg_itam_user_roles')->where('usr_id', $usrId)->delete();
            }
        }

        // 3. Ensure Creative department and realistic sample assets/exceptions exist for Department Manager & Employee testing.
        $this->seedScopeFixtures();
    }

    /**
     * Upsert a user into adm_users, adm_user_data, and adm_members.
     *
     * @param  list<array{rol_id: int, mem_leader: int}>  $memberships
     */
    private function upsertAdmidioUser(
        string $login,
        string $email,
        string $passwordHash,
        string $firstName,
        string $lastName,
        array $memberships
    ): int {
        $existing = DB::table('adm_users')->where('usr_login_name', $login)->first();

        if ($existing) {
            $usrId = (int) $existing->usr_id;
            DB::table('adm_users')->where('usr_id', $usrId)->update([
                'usr_password' => $passwordHash,
                'usr_email' => $email,
                'usr_valid' => 1,
            ]);
        } else {
            $usrId = (int) DB::table('adm_users')->insertGetId([
                'usr_uuid' => (string) Str::uuid(),
                'usr_login_name' => $login,
                'usr_password' => $passwordHash,
                'usr_email' => $email,
                'usr_valid' => 1,
                'usr_number_login' => 0,
                'usr_number_invalid' => 0,
                'usr_timestamp_create' => now(),
            ]);
        }

        $fieldIds = DB::table('adm_user_fields')
            ->whereIn('usf_name_intern', ['FIRST_NAME', 'LAST_NAME', 'EMAIL'])
            ->pluck('usf_id', 'usf_name_intern');

        $profileValues = [
            'FIRST_NAME' => $firstName,
            'LAST_NAME' => $lastName,
            'EMAIL' => $email,
        ];

        foreach ($profileValues as $intern => $value) {
            if (! isset($fieldIds[$intern])) {
                continue;
            }
            $usfId = (int) $fieldIds[$intern];
            $exists = DB::table('adm_user_data')
                ->where('usd_usr_id', $usrId)
                ->where('usd_usf_id', $usfId)
                ->exists();

            if ($exists) {
                DB::table('adm_user_data')
                    ->where('usd_usr_id', $usrId)
                    ->where('usd_usf_id', $usfId)
                    ->update(['usd_value' => $value]);
            } else {
                DB::table('adm_user_data')->insert([
                    'usd_usr_id' => $usrId,
                    'usd_usf_id' => $usfId,
                    'usd_value' => $value,
                ]);
            }
        }

        foreach ($memberships as $membership) {
            $rolId = (int) $membership['rol_id'];
            $leader = (int) $membership['mem_leader'];

            $exists = DB::table('adm_members')
                ->where('mem_usr_id', $usrId)
                ->where('mem_rol_id', $rolId)
                ->exists();

            if ($exists) {
                DB::table('adm_members')
                    ->where('mem_usr_id', $usrId)
                    ->where('mem_rol_id', $rolId)
                    ->update([
                        'mem_leader' => $leader,
                        'mem_begin' => '2026-01-01',
                        'mem_end' => '9999-12-31',
                    ]);
            } else {
                DB::table('adm_members')->insert([
                    'mem_rol_id' => $rolId,
                    'mem_usr_id' => $usrId,
                    'mem_uuid' => (string) Str::uuid(),
                    'mem_begin' => '2026-01-01',
                    'mem_end' => '9999-12-31',
                    'mem_leader' => $leader,
                    'mem_timestamp_create' => now(),
                ]);
            }
        }

        return $usrId;
    }

    /**
     * Upsert an explicit ITAM role assignment in fg_itam_user_roles.
     */
    private function upsertItamRole(int $usrId, ItamRole $role, ?string $notes = null): void
    {
        DB::table('fg_itam_user_roles')->updateOrInsert(
            ['usr_id' => $usrId],
            [
                'role' => $role->value,
                'is_active' => true,
                'assigned_by' => 2,
                'notes' => $notes,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    /**
     * Ensure Creative department, test employee/manager assets, and department audit exception exist.
     */
    private function seedScopeFixtures(): void
    {
        DB::table('fg_itam_departments')->updateOrInsert(
            ['name' => 'Creative'],
            [
                'owner_title' => 'Creative Lead (Faris)',
                'active_assets_count' => 2,
                'available_count' => 1,
                'repair_count' => 0,
                'missing_count' => 0,
                'purchase_value' => 14500.00,
                'exceptions_count' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('fg_itam_assets')->updateOrInsert(
            ['id' => 'IT-LAP-2026-00801'],
            [
                'asset_tag' => 'TAG-LAP-2026-00801',
                'name' => 'MacBook Pro 14 M4',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Apple',
                'model' => 'MacBook Pro 14',
                'serial_number' => 'MBP-CREATIVE-0801',
                'status' => 'Assigned',
                'department' => 'Creative',
                'assigned_user' => 'MUHAMMAD FARIS MAHADI',
                'assigned_location' => 'KL Office',
                'assigned_date' => '2026-09-01',
                'purchase_date' => '2026-08-20',
                'purchase_cost' => 8900.00,
                'warranty_end' => '2029-08-20',
                'condition' => 'Good',
                'verification_status' => 'Verified',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('fg_itam_assets')->updateOrInsert(
            ['id' => 'IT-MON-2026-00802'],
            [
                'asset_tag' => 'TAG-MON-2026-00802',
                'name' => 'Dell UltraSharp 27 4K',
                'category_id' => 'MON',
                'category_name' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'U2723QE',
                'serial_number' => 'MON-CREATIVE-0802',
                'status' => 'Assigned',
                'department' => 'Creative',
                'assigned_user' => 'Faris Manager (Test Dept Mgr)',
                'assigned_location' => 'KL Office',
                'assigned_date' => '2026-09-05',
                'purchase_date' => '2026-08-25',
                'purchase_cost' => 2800.00,
                'warranty_end' => '2029-08-25',
                'condition' => 'Good',
                'verification_status' => 'Verified',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        DB::table('fg_itam_assets')->updateOrInsert(
            ['id' => 'IT-LAP-2026-00803'],
            [
                'asset_tag' => 'TAG-LAP-2026-00803',
                'name' => 'Lenovo ThinkPad T14 Gen 5',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad T14',
                'serial_number' => 'LNV-EMP-0803',
                'status' => 'Assigned',
                'department' => 'Quality Department',
                'assigned_user' => 'Azril Employee (Test Staff)',
                'assigned_location' => 'KL Office',
                'assigned_date' => '2026-09-10',
                'expected_return_date' => '2026-12-31',
                'purchase_date' => '2026-09-01',
                'purchase_cost' => 4800.00,
                'warranty_end' => '2029-09-01',
                'condition' => 'Good',
                'accessories' => 'USB-C Charger, Laptop Sleeve',
                'verification_status' => 'Verified',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        if (! DB::table('fg_itam_audit_exceptions')->where('asset_id', 'IT-MON-2026-00802')->exists()) {
            DB::table('fg_itam_audit_exceptions')->insert([
                'asset_id' => 'IT-MON-2026-00802',
                'exception_type' => 'Wrong Location',
                'expected_info' => 'KL Office - Level 4 Creative',
                'observed_info' => 'KL Office - Studio B',
                'audit_id' => 'AUD-2026-006',
                'owner_department' => 'Creative',
                'status' => 'Open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
