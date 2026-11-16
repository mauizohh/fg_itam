<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssetDetail extends Component
{
    public string $assetId;

    public function mount(string $id)
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.view'), 403);

        $this->assetId = $id;
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.view'), 403);

        $asset = Asset::find($this->assetId);

        if ($actor->isDepartmentScoped()) {
            abort_if($asset === null, 404);
            abort_unless($actor->canAccessDepartment($asset->department), 403);
        }

        if (! $asset) {
            // Fallback sample asset
            $asset = new Asset([
                'id' => $this->assetId,
                'name' => 'Dell Latitude 7450',
                'serial_number' => 'DL7450-93821',
                'hostname' => 'FIN-LT-023',
                'status' => 'Assigned',
                'assigned_user' => 'Sarah Lim',
                'department' => 'Finance',
                'assigned_location' => 'KL Office',
                'purchase_date' => '2026-08-01',
                'purchase_cost' => 5200.00,
                'warranty_end' => '2029-08-01',
                'replacement_due' => 'Aug 2030',
                'verification_status' => 'Verified',
                'last_physical_verification' => '2026-06-15',
            ]);
        }

        $transactions = Transaction::where('asset_id', $this->assetId)->orderByDesc('created_at')->get();
        $auditLogs = AuditLog::where('object_id', $this->assetId)->orderByDesc('timestamp')->get();

        return view('livewire.assets.asset-detail', compact('asset', 'transactions', 'auditLogs'))
            ->layout('layouts.app', ['title' => 'Asset Detail - '.$this->assetId, 'breadcrumb' => 'IT Asset Management / Assets / '.$this->assetId]);
    }
}
