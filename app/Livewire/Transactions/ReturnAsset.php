<?php

namespace App\Livewire\Transactions;

use App\Models\Asset;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReturnAsset extends Component
{
    public string $assetId = 'IT-LAP-2026-00123';

    public string $returnedBy = 'Sarah Lim';

    public string $returnDate = '2026-08-14';

    public string $returnLocation = 'IT Store';

    public string $condition = 'Good';

    public string $nextAction = 'Available for reassignment';

    public string $missingAccessories = 'None';

    public string $remarks = 'Device passed visual inspection';

    public bool $checkPowerOn = true;

    public bool $checkCharger = true;

    public bool $checkDataHandling = true;

    public function mount()
    {
        if ($id = request()->query('asset_id')) {
            $this->assetId = $id;
            $asset = Asset::find($id);
            if ($asset && $asset->assigned_user) {
                $this->returnedBy = $asset->assigned_user;
            }
        }
    }

    public function completeReturn()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $this->validate([
            'assetId' => 'required',
            'returnedBy' => 'required',
        ]);

        $asset = Asset::find($this->assetId);
        if ($asset) {
            $oldStatus = $asset->status;
            $newStatus = ($this->nextAction === 'Maintenance') ? 'In Repair' : (($this->nextAction === 'Retired') ? 'Retired' : 'Available');

            $asset->update([
                'status' => $newStatus,
                'assigned_user' => null,
                'assigned_location' => $this->returnLocation,
                'condition' => $this->condition,
                'remarks' => $this->remarks,
            ]);

            Transaction::create([
                'type' => 'Return',
                'asset_id' => $asset->id,
                'from_user' => $this->returnedBy,
                'to_location' => $this->returnLocation,
                'return_date' => $this->returnDate,
                'condition_at_time' => $this->condition,
                'checklist_power_on' => $this->checkPowerOn,
                'checklist_charger' => $this->checkCharger,
                'checklist_data_wiped' => $this->checkDataHandling,
                'remarks' => $this->remarks,
                'performed_by' => $actor->name,
            ]);

            AuditLogService::record('Return asset', 'Asset', $asset->id, "Assigned to {$this->returnedBy}", "Returned to {$this->returnLocation}");
        }

        session()->flash('success', "Asset {$this->assetId} returned successfully!");

        return redirect()->route('assets.available');
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $assignedAssets = Asset::where('status', 'Assigned')->get();

        return view('livewire.transactions.return-asset', compact('assignedAssets'))
            ->layout('layouts.app', ['title' => 'Return Asset', 'breadcrumb' => 'IT Asset Management / Transactions / Return Asset']);
    }
}
