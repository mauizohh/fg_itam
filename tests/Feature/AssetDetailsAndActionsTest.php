<?php

namespace Tests\Feature;

use App\Livewire\Assets\AssetDetail;
use App\Livewire\Dashboard;
use App\Livewire\Maintenance\Warranty;
use App\Livewire\Transactions\AssignAsset;
use App\Livewire\Transactions\TransferAsset;
use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Transaction;
use App\Models\User;
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
}
