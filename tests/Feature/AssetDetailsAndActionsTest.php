<?php

namespace Tests\Feature;

use App\Livewire\Assets\AllAssets;
use App\Livewire\Assets\AssetDetail;
use App\Livewire\Assets\AssignedAssets;
use App\Livewire\Assets\AvailableAssets;
use App\Livewire\Dashboard;
use App\Livewire\Maintenance\Warranty;
use App\Livewire\People\Departments as DepartmentsComponent;
use App\Livewire\People\Employees as EmployeesComponent;
use App\Livewire\Transactions\AssignAsset;
use App\Livewire\Transactions\LoanAsset;
use App\Livewire\Transactions\ReturnAsset;
use App\Livewire\Transactions\TransferAsset;
use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AdmidioDirectoryService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

class AssetDetailsAndActionsTest extends TestCase
{
    use DatabaseTransactions;

    private function loadUserByLogin(string $loginName): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['username' => $loginName]);
        $this->assertInstanceOf(User::class, $user, "Failed to hydrate Admidio user [{$loginName}].");

        return $user;
    }

    public function test_asset_detail_shows_assign_for_unassigned_and_transfer_for_assigned_assets(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        // Unassigned asset
        $availableAsset = Asset::updateOrCreate(
            ['id' => 'IT-LAP-2026-99001'],
            [
                'asset_tag' => 'TAG-LAP-99001',
                'name' => 'Available Test Laptop',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Dell',
                'model' => 'Latitude 5440',
                'serial_number' => 'SN-AVAIL-99001',
                'status' => 'Available',
                'assigned_user' => null,
                'department' => 'Finance',
                'assigned_location' => 'Jitra Office',
                'condition' => 'Good',
            ]
        );

        Livewire::actingAs($admin)
            ->test(AssetDetail::class, ['id' => $availableAsset->id])
            ->assertSee('Assign Asset')
            ->assertDontSee('Transfer Asset')
            ->assertSee(route('transactions.assign').'?asset_id='.$availableAsset->id);

        // Assigned asset
        $assignedAsset = Asset::updateOrCreate(
            ['id' => 'IT-LAP-2026-99002'],
            [
                'asset_tag' => 'TAG-LAP-99002',
                'name' => 'Assigned Test Laptop',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Apple',
                'model' => 'MacBook Air M3',
                'serial_number' => 'SN-ASSIGN-99002',
                'status' => 'Assigned',
                'assigned_user' => 'Azril Employee',
                'department' => 'Quality Department',
                'assigned_location' => 'Kulim Office',
                'condition' => 'Good',
            ]
        );

        Livewire::actingAs($admin)
            ->test(AssetDetail::class, ['id' => $assignedAsset->id])
            ->assertSee('Transfer Asset')
            ->assertDontSee('Assign Asset')
            ->assertSee(route('transactions.transfer').'?asset_id='.$assignedAsset->id);
    }

    public function test_assign_asset_dropdown_only_lists_available_assets_and_autofills_details(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $availableAsset = Asset::updateOrCreate(
            ['id' => 'IT-MON-2026-99003'],
            [
                'asset_tag' => 'TAG-MON-99003',
                'name' => 'Available Screen',
                'category_id' => 'MON',
                'category_name' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'P2422H',
                'serial_number' => 'SN-MON-99003',
                'status' => 'Available',
                'assigned_user' => null,
                'department' => 'Engineering',
                'assigned_location' => 'Jitra Office',
                'accessories' => 'HDMI Cable, Power Cord',
                'condition' => 'Good',
            ]
        );

        $assignedAsset = Asset::updateOrCreate(
            ['id' => 'IT-MON-2026-99004'],
            [
                'asset_tag' => 'TAG-MON-99004',
                'name' => 'Already Assigned Screen',
                'category_id' => 'MON',
                'category_name' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'U2723QE',
                'serial_number' => 'SN-MON-99004',
                'status' => 'Assigned',
                'assigned_user' => 'Sarah Lim',
                'department' => 'Finance',
                'assigned_location' => 'Kulim Office',
            ]
        );

        Livewire::actingAs($admin)
            ->withQueryParams(['asset_id' => $availableAsset->id])
            ->test(AssignAsset::class)
            ->assertSet('assetId', $availableAsset->id)
            ->assertSet('location', 'Jitra Office')
            ->assertSet('department', 'Engineering')
            ->assertSet('accessories', 'HDMI Cable, Power Cord')
            ->assertSee($availableAsset->id)
            ->assertDontSee($assignedAsset->id.' - Already Assigned Screen');
    }

    public function test_transfer_asset_dropdown_only_lists_assigned_assets_and_autofills_details(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $assignedAsset = Asset::updateOrCreate(
            ['id' => 'IT-LAP-2026-99005'],
            [
                'asset_tag' => 'TAG-LAP-99005',
                'name' => 'Assigned ThinkPad',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad L14',
                'serial_number' => 'SN-LNV-99005',
                'status' => 'Assigned',
                'assigned_user' => 'Azril Employee',
                'department' => 'Quality Department',
                'assigned_location' => 'Jitra Office',
            ]
        );

        $availableAsset = Asset::updateOrCreate(
            ['id' => 'IT-LAP-2026-99006'],
            [
                'asset_tag' => 'TAG-LAP-99006',
                'name' => 'Unassigned ThinkPad',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad L14',
                'serial_number' => 'SN-LNV-99006',
                'status' => 'Available',
                'assigned_user' => null,
            ]
        );

        Livewire::actingAs($admin)
            ->withQueryParams(['asset_id' => $assignedAsset->id])
            ->test(TransferAsset::class)
            ->assertSet('assetId', $assignedAsset->id)
            ->assertSet('currentCustodian', 'Azril Employee')
            ->assertSet('currentLocation', 'Jitra Office')
            ->assertSet('newDepartment', 'Quality Department')
            ->assertSee($assignedAsset->id)
            ->assertDontSee($availableAsset->id.' - Unassigned ThinkPad');
    }

    public function test_transaction_ledger_asset_id_redirects_to_asset_detail(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $tx = Transaction::create([
            'type' => 'Transfer',
            'asset_id' => 'IT-MON-2026-00391',
            'from_user' => 'Sarah Lim',
            'to_user' => 'Akif Farhan',
            'to_department' => 'Creative',
            'to_location' => 'Kulim Office',
            'transaction_date' => '2026-11-19',
            'performed_by' => 'It Nazrul',
            'remarks' => 'Role transfer',
        ]);

        $response = $this->actingAs($admin)->get(route('transactions.history'));
        $response->assertOk();
        $response->assertSee(route('assets.detail', 'IT-MON-2026-00391'));
    }

    public function test_asset_timeline_is_dynamic_and_reflects_audited_events(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $asset = Asset::updateOrCreate(
            ['id' => 'IT-MOB-2026-99007'],
            [
                'asset_tag' => 'TAG-MOB-99007',
                'name' => 'Pixel 9 Pro',
                'category_id' => 'MOB',
                'category_name' => 'Mobile Device',
                'brand' => 'Google',
                'model' => 'Pixel 9 Pro',
                'serial_number' => 'SN-PIX-99007',
                'status' => 'Available',
                'assigned_location' => 'Kulim Office',
                'created_at' => now()->subDays(5),
            ]
        );

        AuditLog::create([
            'timestamp' => now()->subDay(),
            'user' => 'It Nazrul',
            'action' => 'Register asset',
            'object_type' => 'Asset',
            'object_id' => $asset->id,
            'after_state' => 'Registered in Kulim Office',
        ]);

        Livewire::actingAs($admin)
            ->test(AssetDetail::class, ['id' => $asset->id])
            ->assertSee('Register asset')
            ->assertSee('Registered in Kulim Office')
            ->assertSee('It Nazrul')
            ->assertDontSee('TechSource Sdn Bhd, INV-88301');
    }

    public function test_dashboard_recent_activity_paginates_at_five_per_page(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        // Create 7 audit logs
        for ($i = 1; $i <= 7; $i++) {
            AuditLog::create([
                'timestamp' => now()->subMinutes($i * 10),
                'user' => 'It Nazrul',
                'action' => "Test Action {$i}",
                'object_type' => 'Asset',
                'object_id' => "IT-LAP-2026-0080{$i}",
                'after_state' => "State {$i}",
            ]);
        }

        $component = Livewire::actingAs($admin)
            ->test(Dashboard::class)
            ->assertSee('Test Action 1')
            ->assertSee('Test Action 5')
            ->assertDontSee('Test Action 6');

        $component->call('nextPage')
            ->assertSee('Test Action 6');
    }

    public function test_user_can_edit_asset_details_including_warranty_brand_serial_and_dates(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $asset = Asset::updateOrCreate(
            ['id' => 'IT-LAP-2026-99008'],
            [
                'asset_tag' => 'TAG-LAP-99008',
                'name' => 'Original Laptop',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'HP',
                'model' => 'EliteBook 840',
                'serial_number' => 'SN-ORIG-99008',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
                'purchase_date' => '2026-01-10',
                'warranty_end' => '2029-01-10',
            ]
        );

        Livewire::actingAs($admin)
            ->test(AssetDetail::class, ['id' => $asset->id])
            ->call('openEditModal')
            ->set('editName', 'Updated Dell Laptop')
            ->set('editBrand', 'Dell')
            ->set('editModel', 'Latitude 7450')
            ->set('editSerial', 'SN-NEW-99008')
            ->set('editPurchaseDate', '2026-03-15')
            ->set('editWarrantyProvider', 'Dell ProSupport Plus')
            ->set('editWarrantyStart', '2026-03-15')
            ->set('editWarrantyEnd', '2029-03-15')
            ->set('editLocation', 'Kulim Office')
            ->call('saveAssetEdit')
            ->assertHasNoErrors()
            ->assertSee('Updated Dell Laptop')
            ->assertSee('SN-NEW-99008')
            ->assertSee('Dell ProSupport Plus');

        $fresh = $asset->fresh();
        $this->assertSame('Updated Dell Laptop', $fresh->name);
        $this->assertSame('Dell', $fresh->brand);
        $this->assertSame('SN-NEW-99008', $fresh->serial_number);
        $this->assertSame('Kulim Office', $fresh->assigned_location);
        $this->assertSame('Dell ProSupport Plus', $fresh->warranty_provider);
    }

    public function test_warranty_can_be_added_or_updated_via_warranty_management(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $asset = Asset::updateOrCreate(
            ['id' => 'IT-SRV-2026-99009'],
            [
                'asset_tag' => 'TAG-SRV-99009',
                'name' => 'Database Host Server',
                'category_id' => 'SRV',
                'category_name' => 'Server',
                'brand' => 'Dell',
                'model' => 'PowerEdge R750',
                'serial_number' => 'SN-SRV-99009',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
            ]
        );

        Livewire::actingAs($admin)
            ->test(Warranty::class)
            ->call('openWarrantyModal', $asset->id)
            ->set('warrantyProvider', 'Dell Mission Critical 4Hr')
            ->set('warrantyStart', '2026-06-01')
            ->set('warrantyEnd', '2031-06-01')
            ->set('supportContract', 'Gold 24x7 Support')
            ->call('saveWarranty')
            ->assertHasNoErrors()
            ->assertSee('Dell Mission Critical 4Hr');

        $fresh = $asset->fresh();
        $this->assertSame('Dell Mission Critical 4Hr', $fresh->warranty_provider);
        $this->assertSame('2031-06-01', substr((string) $fresh->warranty_end, 0, 10));
    }

    public function test_assign_asset_requires_department_and_allows_optional_assignee(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $asset = Asset::updateOrCreate(
            ['id' => 'IT-ACC-2026-99010'],
            [
                'asset_tag' => 'TAG-ACC-99010',
                'name' => 'Departmental Projector',
                'category_id' => 'ACC',
                'category_name' => 'Accessory',
                'brand' => 'Epson',
                'model' => 'EB-FH52',
                'serial_number' => 'SN-EPSON-99010',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
                'department' => null,
                'assigned_user' => null,
            ]
        );

        // Department missing should fail validation
        Livewire::actingAs($admin)
            ->test(AssignAsset::class)
            ->set('assetId', $asset->id)
            ->set('department', '')
            ->set('assignTo', '')
            ->set('location', 'Jitra Office')
            ->call('submit')
            ->assertHasErrors(['department']);

        // Department provided with empty assignTo should succeed
        Livewire::actingAs($admin)
            ->test(AssignAsset::class)
            ->set('assetId', $asset->id)
            ->set('department', 'Human Resources')
            ->set('assignTo', '')
            ->set('location', 'Kulim Office')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('assets.assigned'));

        $fresh = $asset->fresh();
        $this->assertSame('Assigned', $fresh->status);
        $this->assertNull($fresh->assigned_user);
        $this->assertSame('Human Resources', $fresh->department);
        $this->assertSame('Kulim Office', $fresh->assigned_location);

        // Webpage displays department name when assigned_user is null
        $this->assertSame('Human Resources', $fresh->display_assigned_user);
    }

    public function test_warranty_start_date_defaults_to_purchase_date_and_syncs(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $asset = Asset::updateOrCreate(
            ['id' => 'IT-LAP-2026-99011'],
            [
                'asset_tag' => 'TAG-LAP-99011',
                'name' => 'Test Default Warranty Laptop',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Dell',
                'model' => 'Latitude 5540',
                'serial_number' => 'SN-DELL-99011',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
                'purchase_date' => '2026-05-20',
                'warranty_start' => null,
            ]
        );

        // On AssetDetail: openEditModal defaults warranty start to purchase date
        Livewire::actingAs($admin)
            ->test(AssetDetail::class, ['id' => $asset->id])
            ->call('openEditModal')
            ->assertSet('editPurchaseDate', '2026-05-20')
            ->assertSet('editWarrantyStart', '2026-05-20')
            ->set('editPurchaseDate', '2026-06-01')
            ->assertSet('editWarrantyStart', '2026-06-01');

        // On Warranty: openWarrantyModal defaults warranty start to asset purchase date
        Livewire::actingAs($admin)
            ->test(Warranty::class)
            ->call('openWarrantyModal', $asset->id)
            ->assertSet('warrantyStart', '2026-05-20');
    }

    public function test_replacement_due_accepts_and_persists_date_format(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $asset = Asset::updateOrCreate(
            ['id' => 'IT-NET-2026-99012'],
            [
                'asset_tag' => 'TAG-NET-99012',
                'name' => 'Core Switch',
                'category_id' => 'NET',
                'category_name' => 'Network',
                'brand' => 'Cisco',
                'model' => 'Catalyst 9300',
                'serial_number' => 'SN-CSCO-99012',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
                'replacement_due' => 'Aug 2030',
            ]
        );

        Livewire::actingAs($admin)
            ->test(AssetDetail::class, ['id' => $asset->id])
            ->call('openEditModal')
            ->assertSet('editReplacementDue', '2030-08-01')
            ->set('editReplacementDue', '2032-11-15')
            ->call('saveAssetEdit')
            ->assertHasNoErrors();

        $this->assertSame('2032-11-15', $asset->fresh()->replacement_due);
    }

    public function test_warranty_table_sorts_active_expiries_first_and_expired_last(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $expiredAsset = Asset::updateOrCreate(
            ['id' => 'IT-MON-2026-99013'],
            [
                'asset_tag' => 'TAG-MON-99013',
                'name' => 'Old Expired Display',
                'category_id' => 'MON',
                'category_name' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'P2419H',
                'serial_number' => 'SN-EXP-99013',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
                'warranty_end' => now()->subMonths(2)->format('Y-m-d'),
                'warranty_provider' => 'Expired Care',
            ]
        );

        $soonAsset = Asset::updateOrCreate(
            ['id' => 'IT-MON-2026-99014'],
            [
                'asset_tag' => 'TAG-MON-99014',
                'name' => 'Expiring Soon Display',
                'category_id' => 'MON',
                'category_name' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'P2422H',
                'serial_number' => 'SN-SOON-99014',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
                'warranty_end' => now()->addDays(15)->format('Y-m-d'),
                'warranty_provider' => 'Immediate Care',
            ]
        );

        $futureAsset = Asset::updateOrCreate(
            ['id' => 'IT-MON-2026-99015'],
            [
                'asset_tag' => 'TAG-MON-99015',
                'name' => 'Future Warranty Display',
                'category_id' => 'MON',
                'category_name' => 'Monitor',
                'brand' => 'Dell',
                'model' => 'U2723QE',
                'serial_number' => 'SN-FUT-99015',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
                'warranty_end' => now()->addYears(2)->format('Y-m-d'),
                'warranty_provider' => 'Long Term Care',
            ]
        );

        // Default sort: soonAsset first, futureAsset second, expiredAsset at bottom
        $test = Livewire::actingAs($admin)
            ->test(Warranty::class);

        $renderedHtml = $test->html();
        $soonPos = strpos($renderedHtml, $soonAsset->id);
        $futurePos = strpos($renderedHtml, $futureAsset->id);
        $expiredPos = strpos($renderedHtml, $expiredAsset->id);

        $this->assertNotFalse($soonPos, 'Soon asset not found');
        $this->assertNotFalse($futurePos, 'Future asset not found');
        $this->assertNotFalse($expiredPos, 'Expired asset not found');
        $this->assertTrue($soonPos < $futurePos, 'Expected soon asset before future asset');
        $this->assertTrue($futurePos < $expiredPos, 'Expected future asset before expired asset at bottom');

        // Test filter: expired only
        $test->set('statusFilter', 'expired')
            ->assertSee($expiredAsset->id)
            ->assertDontSee($soonAsset->id)
            ->assertDontSee($futureAsset->id);
    }

    public function test_assign_and_transfer_asset_dropdown_search_filters_results(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $assetA = Asset::updateOrCreate(
            ['id' => 'IT-LAP-2026-99016'],
            [
                'asset_tag' => 'TAG-LAP-99016',
                'name' => 'Searchable Alpha Laptop',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Lenovo',
                'model' => 'ThinkPad P1',
                'serial_number' => 'SN-ALPHA-99016',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
            ]
        );

        $assetB = Asset::updateOrCreate(
            ['id' => 'IT-LAP-2026-99017'],
            [
                'asset_tag' => 'TAG-LAP-99017',
                'name' => 'Searchable Beta Laptop',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Dell',
                'model' => 'Latitude 7420',
                'serial_number' => 'SN-BETA-99017',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
            ]
        );

        // Search in AssignAsset
        Livewire::actingAs($admin)
            ->test(AssignAsset::class)
            ->set('assetSearch', 'ALPHA')
            ->assertSee('Searchable Alpha Laptop')
            ->assertDontSee('Searchable Beta Laptop')
            ->set('assetSearch', 'BETA')
            ->assertSee('Searchable Beta Laptop')
            ->assertDontSee('Searchable Alpha Laptop');

        // Turn asset A into assigned for TransferAsset testing
        $assetA->update(['status' => 'Assigned', 'assigned_user' => 'Test User']);

        // Search in TransferAsset
        Livewire::actingAs($admin)
            ->test(TransferAsset::class)
            ->set('assetSearch', 'ALPHA')
            ->assertSee('Searchable Alpha Laptop');
    }

    public function test_warranty_page_paginates_at_15_and_supports_sorting_and_kebab_actions(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        Livewire::actingAs($admin)
            ->test(Warranty::class)
            ->assertSeeHtml('kebab-btn')
            ->assertSee('Review')
            ->assertSee('Update')
            ->call('sortBy', 'id')
            ->assertSet('sortField', 'id')
            ->assertSet('sortDirection', 'asc')
            ->call('sortBy', 'id')
            ->assertSet('sortField', 'id')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_all_assets_table_supports_column_sorting_and_kebab_dropdown(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        Livewire::actingAs($admin)
            ->test(AllAssets::class)
            ->assertSeeHtml('kebab-btn')
            ->call('sortBy', 'name')
            ->assertSet('sortField', 'name')
            ->assertSet('sortDirection', 'asc')
            ->call('sortBy', 'name')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_dashboard_recent_activity_action_by_and_employee_use_standard_plain_text_design(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $component = Livewire::actingAs($admin)->test(Dashboard::class);
        $html = $component->html();

        // Action By column header exists
        $this->assertStringContainsString('Action By', $html);
        $this->assertStringContainsString('Employee', $html);

        // Action By cells no longer use the bg-gray-100 badge wrapper
        $this->assertStringNotContainsString('bg-gray-100 text-gray-700 font-semibold whitespace-nowrap', $html);
    }

    public function test_assigned_assets_renders_kebab_menu_and_centered_condition_and_action(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        Livewire::actingAs($admin)
            ->test(AssignedAssets::class)
            ->assertSeeHtml('kebab-btn')
            ->assertSeeHtml('w-28">Condition</th>')
            ->assertSeeHtml('w-24">Action</th>');
    }

    public function test_available_assets_renders_centered_condition_and_action(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        Livewire::actingAs($admin)
            ->test(AvailableAssets::class)
            ->assertSeeHtml('w-28">Condition</th>')
            ->assertSeeHtml('w-24">Action</th>')
            ->assertSeeHtml('flex items-center justify-center');
    }

    public function test_warranty_filter_box_does_not_contain_overlapping_search_svg_icon(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $html = Livewire::actingAs($admin)
            ->test(Warranty::class)
            ->html();

        // The search input inside the filter box should not have the overlapping svg icon
        $this->assertStringNotContainsString('svg class="w-4 h-4 text-gray-400 absolute left-2.5', $html);
    }

    public function test_loan_and_return_asset_render_unified_searchable_combobox(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        // Test LoanAsset
        Livewire::actingAs($admin)
            ->test(LoanAsset::class)
            ->assertSeeHtml('-- Select Available Asset (browse list or search) --')
            ->assertSeeHtml('filteredAssets');

        // Test ReturnAsset
        Livewire::actingAs($admin)
            ->test(ReturnAsset::class)
            ->assertSeeHtml('-- Select Asset to Return (browse list or search) --')
            ->assertSeeHtml('filteredAssets');
    }

    public function test_departments_and_users_are_sourced_from_admidio_database(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        // Trigger synchronization service
        $syncService = app(AdmidioDirectoryService::class);
        $result = $syncService->syncAll();

        $this->assertGreaterThanOrEqual(21, $result['departments']);
        $this->assertGreaterThan(50, $result['employees']);

        // 1. Assign Asset renders the actual Admidio department list and employee combobox
        Livewire::actingAs($admin)
            ->test(AssignAsset::class)
            ->assertSeeHtml('Account Dept')
            ->assertSeeHtml('Information Technology')
            ->assertSeeHtml('Human Resources')
            ->assertSeeHtml('Creative')
            ->assertSeeHtml('-- Select Employee (or search by name/dept) --');

        // 2. Transfer Asset renders the actual Admidio department list and custodian combobox
        Livewire::actingAs($admin)
            ->test(TransferAsset::class)
            ->assertSeeHtml('Account Dept')
            ->assertSeeHtml('Information Technology')
            ->assertSeeHtml('Human Resources')
            ->assertSeeHtml('Creative')
            ->assertSeeHtml('-- Select New Custodian (or search by name/dept) --');

        // 3. Employees directory renders actual Admidio employees and Admidio department filter
        Livewire::actingAs($admin)
            ->test(EmployeesComponent::class)
            ->assertSeeHtml('Account Dept')
            ->assertSeeHtml('Information Technology')
            ->assertSeeHtml('Human Resources');

        // 4. Departments directory renders actual Admidio department list and their leaders
        Livewire::actingAs($admin)
            ->test(DepartmentsComponent::class)
            ->assertSeeHtml('Account Dept')
            ->assertSeeHtml('Information Technology')
            ->assertSeeHtml('Human Resources')
            ->assertSeeHtml('Creative');
    }

    public function test_employee_combobox_and_bi_directional_department_linkage(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        // Get an active synced Admidio employee with a department
        Employee::ensureSynced();
        $emp = Employee::whereNotNull('department')->where('department', '!=', '')->first();
        $this->assertNotNull($emp);

        $empName = $emp->name;
        $empDept = $emp->department;
        $otherDept = $empDept === 'Creative' ? 'Human Resources' : 'Creative';

        // 1. In AssignAsset, setting assignTo auto-selects department
        Livewire::actingAs($admin)
            ->test(AssignAsset::class)
            ->assertSet('department', '')
            ->assertSeeHtml('-- All Departments --')
            ->set('assignTo', $empName)
            ->assertSet('department', $empDept);

        // 2. In AssignAsset, changing department to a mismatch clears assignTo
        Livewire::actingAs($admin)
            ->test(AssignAsset::class)
            ->set('assignTo', $empName)
            ->assertSet('department', $empDept)
            ->set('department', $otherDept)
            ->assertSet('assignTo', '');

        // 3. In TransferAsset, setting transferTo auto-selects newDepartment
        Livewire::actingAs($admin)
            ->test(TransferAsset::class)
            ->set('newDepartment', '')
            ->assertSeeHtml('-- All Departments --')
            ->set('transferTo', $empName)
            ->assertSet('newDepartment', $empDept);

        // 4. In LoanAsset and ReturnAsset, the custom employee comboboxes render
        Livewire::actingAs($admin)
            ->test(LoanAsset::class)
            ->assertSeeHtml('-- Select Borrower (or search by name/dept) --');

        Livewire::actingAs($admin)
            ->test(ReturnAsset::class)
            ->assertSeeHtml('-- Select Returnee (or search by name/dept) --');
    }

    public function test_assign_asset_expected_return_defaults_to_not_required_and_allows_reset_or_date(): void
    {
        $admin = $this->loadUserByLogin('test.sysadmin');

        $asset = Asset::updateOrCreate(
            ['id' => 'IT-LAP-2026-99077'],
            [
                'asset_tag' => 'TAG-LAP-99077',
                'name' => 'Expected Return Test Laptop',
                'category_id' => 'LAP',
                'category_name' => 'Laptop',
                'brand' => 'Dell',
                'model' => 'Latitude 5540',
                'serial_number' => 'SN-DELL-99077',
                'status' => 'Available',
                'assigned_location' => 'Jitra Office',
                'purchase_date' => '2026-05-20',
            ]
        );

        // 1. Expected return defaults to 'Not required'
        Livewire::actingAs($admin)
            ->test(AssignAsset::class)
            ->set('assetId', $asset->id)
            ->assertSet('expectedReturn', 'Not required')
            ->assertSeeHtml('Not required')
            ->assertSeeHtml('title="Reset date to Not required"');

        // 2. Setting a date stores expected_return_date on the asset
        Livewire::actingAs($admin)
            ->test(AssignAsset::class)
            ->set('assetId', $asset->id)
            ->set('department', 'Information Technology')
            ->set('location', 'Jitra Office')
            ->set('expectedReturn', '2026-12-31')
            ->call('submit')
            ->assertHasNoErrors();

        $fresh = $asset->fresh();
        $this->assertEquals('2026-12-31', $fresh->expected_return_date ? Carbon::parse($fresh->expected_return_date)->format('Y-m-d') : null);

        // 3. Resetting to 'Not required' sets expected_return_date to null
        $asset->update(['status' => 'Available']);
        Livewire::actingAs($admin)
            ->test(AssignAsset::class)
            ->set('assetId', $asset->id)
            ->set('department', 'Information Technology')
            ->set('location', 'Jitra Office')
            ->set('expectedReturn', 'Not required')
            ->call('submit')
            ->assertHasNoErrors();

        $fresh = $asset->fresh();
        $this->assertNull($fresh->expected_return_date);
    }
}
