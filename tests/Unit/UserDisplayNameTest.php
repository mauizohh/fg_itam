<?php

namespace Tests\Unit;

use App\Enums\ItamRole;
use App\Models\Asset;
use App\Models\Location;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserDisplayNameTest extends TestCase
{
    public function test_user_display_name_strips_category_suffixes_and_preserves_raw_name(): void
    {
        $admin = new User(
            usr_id: 100,
            username: 'test.sysadmin',
            name: 'It Nazrul (Test Admin)',
            email: 'test.sysadmin@example.com',
            passwordHash: 'hash',
            itamRole: ItamRole::SystemItAdmin,
        );

        $staff = new User(
            usr_id: 101,
            username: 'test.employee',
            name: 'Azril Employee (Test Staff)',
            email: 'test.employee@example.com',
            passwordHash: 'hash',
            itamRole: ItamRole::Employee,
        );

        $singleName = new User(
            usr_id: 102,
            username: 'nazrul',
            name: 'Nazrul (Test Admin)',
            email: 'nazrul@example.com',
            passwordHash: 'hash',
            itamRole: ItamRole::Auditor,
        );

        $this->assertSame('It Nazrul (Test Admin)', $admin->name);
        $this->assertSame('It Nazrul', $admin->display_name);
        $this->assertSame('IN', $admin->initials);

        $this->assertSame('Azril Employee', $staff->display_name);
        $this->assertSame('AE', $staff->initials);

        $this->assertSame('Nazrul', $singleName->display_name);
        $this->assertSame('N', $singleName->initials);
    }

    public function test_asset_display_assigned_user_strips_category_suffix(): void
    {
        $asset = new Asset([
            'assigned_user' => 'Azril Employee (Test Staff)',
            'department' => 'Quality Department',
        ]);

        $this->assertSame('Azril Employee', $asset->display_assigned_user);
        $this->assertSame('Azril Employee (Quality Department)', $asset->formatted_assigned);
    }

    public function test_location_office_options_are_restricted_to_jitra_and_kulim(): void
    {
        $this->assertSame(['Jitra Office', 'Kulim Office'], Location::OFFICE_OPTIONS);
    }
}
