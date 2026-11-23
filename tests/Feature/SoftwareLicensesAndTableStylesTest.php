<?php

namespace Tests\Feature;

use App\Livewire\Software\Licenses;
use App\Livewire\Software\SoftwareCatalog;
use App\Livewire\Transactions\ReturnAsset;
use App\Models\Asset;
use App\Models\License;
use App\Models\LicenseAsset;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class SoftwareLicensesAndTableStylesTest extends TestCase
{
    use DatabaseTransactions;

    private function loadUserByLogin(string $loginName): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['username' => $loginName]);
        $this->assertInstanceOf(User::class, $user, "Failed to hydrate Admidio user [{$loginName}].");

        return $user;
    }

    public function test_department_summary_removes_owner_and_centers_numeric_columns(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $response = $this->actingAs($admin)->get(route('people.departments'));
        $response->assertOk();

        // Ensure Owner header is removed
        $response->assertDontSee('<th>Owner</th>', false);

        // Ensure headers are centered
        $response->assertSee('<th class="text-center">Active Assets</th>', false);
        $response->assertSee('<th class="text-center">Available</th>', false);
        $response->assertSee('<th class="text-center">Repair</th>', false);
        $response->assertSee('<th class="text-center">Missing</th>', false);

        // Ensure table cells are centered
        $response->assertSee('text-center font-bold font-heading', false);
    }

    public function test_employee_directory_centers_assigned_assets_and_pending_returns(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $response = $this->actingAs($admin)->get(route('people.employees'));
        $response->assertOk();

        $response->assertSee('<th class="text-center">Assigned Assets</th>', false);
        $response->assertSee('<th class="text-center">Pending Returns</th>', false);
    }

    public function test_return_inspection_checklist_has_interactive_checked_buttons(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $asset = Asset::firstOrCreate(
            ['id' => 'IT-LAP-2026-09999'],
            [
                'name' => 'Inspection Laptop',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad T14',
                'serial_number' => 'SN-INSPECT-01',
                'status' => 'Assigned',
                'assigned_user' => 'Azril Employee',
                'assigned_location' => 'Jitra Office',
                'condition' => 'Good',
            ]
        );

        $response = $this->actingAs($admin)->get(route('transactions.return', ['asset_id' => $asset->id]));
        $response->assertOk();

        // Ensure interactive checkable inputs are present for all 3 checklist tasks
        $response->assertSee('wire:model.live="checkPowerOn"', false);
        $response->assertSee('wire:model.live="checkCharger"', false);
        $response->assertSee('wire:model.live="checkDataHandling"', false);
        $response->assertSee('Tick items as actions are completed');

        // Test Livewire component toggling checklist items
        Livewire::actingAs($admin)
            ->test(ReturnAsset::class, ['asset_id' => $asset->id])
            ->assertSet('checkPowerOn', false)
            ->set('checkPowerOn', true)
            ->assertSet('checkPowerOn', true);
    }

    public function test_transaction_ledger_standardized_type_pills(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        Transaction::create([
            'type' => 'Transfer',
            'asset_id' => 'IT-MON-2026-00391',
            'from_user' => 'Test User A',
            'to_user' => 'Test User B',
            'transaction_date' => now()->format('Y-m-d'),
            'remarks' => 'Dept transfer test',
        ]);

        Transaction::create([
            'type' => 'Assign',
            'asset_id' => 'IT-MON-2026-00391',
            'from_user' => 'Jitra Office',
            'to_user' => 'Test User B',
            'transaction_date' => now()->format('Y-m-d'),
            'remarks' => 'New assignment test',
        ]);

        $response = $this->actingAs($admin)->get(route('transactions.history'));
        $response->assertOk();

        // Both Transfer and Assign should be rendered in pill normal badges
        $this->assertEquals('pill normal', Transaction::typePillClass('Assign'));
        $this->assertEquals('pill normal', Transaction::typePillClass('Transfer'));
        $this->assertEquals('pill low', Transaction::typePillClass('Return'));
        $this->assertEquals('pill high', Transaction::typePillClass('Loan'));

        $response->assertSee('<span class="pill normal">Assign</span>', false);
        $response->assertSee('<span class="pill normal">Transfer</span>', false);
    }

    public function test_software_licenses_allows_viewing_assigned_asset_id_with_clickable_link(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $license = License::firstOrCreate(
            ['software_name' => 'Endpoint Security'],
            [
                'purchased_seats' => 520,
                'assigned_seats' => 510,
                'available_seats' => 10,
                'utilization_percent' => 98,
                'status' => 'Review',
            ]
        );

        $asset = Asset::firstOrCreate(
            ['id' => 'IT-LAP-2026-00803'],
            [
                'name' => 'Lenovo ThinkPad T14 Gen 5',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad T14',
                'serial_number' => 'LNV-EMP-0803',
                'status' => 'Assigned',
                'assigned_user' => 'Azril Employee',
                'assigned_location' => 'Jitra Office',
            ]
        );

        LicenseAsset::firstOrCreate([
            'software_name' => $license->software_name,
            'asset_id' => $asset->id,
        ], [
            'license_id' => $license->id,
            'assigned_date' => now()->format('Y-m-d'),
            'status' => 'Active',
        ]);

        $response = $this->actingAs($admin)->get(route('software.licenses'));
        $response->assertOk();
        $response->assertSee('View Assets');

        // Test opening modal in Livewire
        Livewire::actingAs($admin)
            ->test(Licenses::class)
            ->call('openAssetModal', $license->id)
            ->assertSet('showAssetModal', true)
            ->assertSee($asset->id)
            ->assertSee(route('assets.detail', $asset->id));
    }

    public function test_software_catalog_centers_managed_devices_column(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $response = $this->actingAs($admin)->get(route('software.index'));
        $response->assertOk();

        // Check header centering
        $response->assertSee('<th class="text-center">Managed Devices</th>', false);

        // Check cell centering
        $response->assertSee('text-center font-bold font-heading text-gray-800', false);

        // Test opening devices modal in SoftwareCatalog
        Livewire::actingAs($admin)
            ->test(SoftwareCatalog::class)
            ->call('openDeviceModal', 'Endpoint Security')
            ->assertSet('showDeviceModal', true);
    }
}
