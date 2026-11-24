<?php

namespace Tests\Feature;

use App\Livewire\Administration\Locations;
use App\Livewire\Administration\Users as AdminUsers;
use App\Livewire\Assets\AllAssets;
use App\Livewire\Employee\MyAssets;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class UiDisplayAndLocationsTest extends TestCase
{
    use DatabaseTransactions;

    private function loadUserByLogin(string $loginName): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['username' => $loginName]);
        $this->assertInstanceOf(User::class, $user, "Failed to hydrate Admidio user [{$loginName}].");

        return $user;
    }

    public function test_header_and_admin_users_hide_user_category_suffixes(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');
        $employee = $this->loadUserByLogin('test.employee');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('It Nazrul')
            ->assertDontSee('(Test Admin)');

        $this->actingAs($employee)
            ->get(route('my-assets'))
            ->assertOk()
            ->assertSee('Azril Employee')
            ->assertDontSee('(Test Staff)');

        Livewire::actingAs($admin)
            ->test(AdminUsers::class)
            ->set('search', 'test.')
            ->assertSee('It Nazrul')
            ->assertSee('Azril Employee')
            ->assertDontSee('(Test Admin)')
            ->assertDontSee('(Test Staff)')
            ->assertDontSee('(Test IT)')
            ->assertDontSee('(Test Dept Mgr)')
            ->assertDontSee('(Test Account)');
    }

    public function test_my_assets_hides_signed_in_as_banner_and_empty_state_user_name(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        Livewire::actingAs($admin)
            ->test(MyAssets::class)
            ->assertSee('View assigned assets, acknowledge receipt, and track return obligations.')
            ->assertDontSee('Signed in as')
            ->assertSee('No active IT equipment is currently assigned under your name.')
            ->assertDontSee('under your name (')
            ->assertDontSee('(Test Admin)');

        // Employee with assigned equipment continues to see their assigned item
        $employee = $this->loadUserByLogin('test.employee');

        Livewire::actingAs($employee)
            ->test(MyAssets::class)
            ->assertDontSee('Signed in as')
            ->assertSee('IT-LAP-2026-00803')
            ->assertDontSee('(Test Staff)')
            ->assertSee('<th class="text-center">Acknowledgement</th>', false)
            ->assertSee('flex flex-col items-center justify-center gap-1', false)
            ->assertDontSee('bg-amber-50', false);
    }

    public function test_location_options_only_allow_jitra_office_and_kulim_office(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        Livewire::actingAs($admin)
            ->test(AllAssets::class)
            ->call('openAddModal')
            ->assertSee('<option value="Jitra Office">Jitra Office</option>', false)
            ->assertSee('<option value="Kulim Office">Kulim Office</option>', false)
            ->assertDontSee('<option value="IT Store">', false)
            ->assertDontSee('<option value="KL Office">', false)
            ->assertDontSee('<option value="KL Office - Level 5">', false)
            ->assertDontSee('<option value="KL Office - Level 6">', false)
            ->assertDontSee('<option value="Server Room">', false)
            ->assertDontSee('<option value="Penang Office">', false)
            ->assertDontSee('<option value="Repair Vendor">', false)
            ->set('newName', 'Dell Latitude 5450')
            ->set('newSerial', 'SN-JITRA-99901')
            ->set('newLocation', 'KL Office')
            ->call('saveAsset')
            ->assertHasErrors(['newLocation'])
            ->set('newLocation', 'Kulim Office')
            ->call('saveAsset')
            ->assertHasNoErrors();

        $this->actingAs($admin)
            ->get(route('assets.assigned'))
            ->assertOk()
            ->assertSee('Jitra Office')
            ->assertSee('Kulim Office')
            ->assertDontSee('<option value="Penang Office">', false)
            ->assertDontSee('<option value="KL Office">', false)
            ->assertDontSee('<option value="Server Room">', false);

        $this->actingAs($admin)
            ->get(route('assets.available'))
            ->assertOk()
            ->assertSee('Jitra Office')
            ->assertSee('Kulim Office')
            ->assertDontSee('<option value="IT Store">', false)
            ->assertDontSee('<option value="Penang Office">', false)
            ->assertDontSee('<option value="KL Office">', false)
            ->assertDontSee('<option value="Server Room">', false);

        $this->actingAs($admin)
            ->get(route('transactions.return'))
            ->assertOk()
            ->assertSee('Jitra Office')
            ->assertSee('Kulim Office')
            ->assertDontSee('<option value="IT Store">', false)
            ->assertDontSee('<option value="KL Office">', false)
            ->assertDontSee('<option value="Server Room">', false);

        $this->actingAs($admin)
            ->get(route('transactions.assign'))
            ->assertOk()
            ->assertSee('Jitra Office')
            ->assertSee('Kulim Office');

        $this->actingAs($admin)
            ->get(route('transactions.transfer'))
            ->assertOk()
            ->assertSee('Jitra Office')
            ->assertSee('Kulim Office');

        Livewire::actingAs($admin)
            ->test(Locations::class)
            ->assertSee('Jitra Office')
            ->assertSee('Kulim Office')
            ->assertDontSee('Kuala Lumpur Office')
            ->assertDontSee('Penang Office');
    }

    public function test_dashboard_does_not_display_add_asset_and_audit_scan_buttons(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Add Asset')
            ->assertDontSee('Audit Scan');
    }
}
