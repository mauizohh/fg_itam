<?php

namespace App\Livewire\Transactions;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TransferAsset extends Component
{
    public string $assetId = 'IT-MON-2026-00391';

    public string $currentCustodian = 'Sarah Lim';

    public string $currentLocation = 'KL Office - Finance';

    public string $transferTo = 'Daniel Wong';

    public string $newDepartment = 'Engineering';

    public string $newLocation = 'KL Office - Engineering';

    public string $effectiveDate = '2026-08-15';

    public string $reason = 'Role transfer';

    public function mount()
    {
        if ($id = request()->query('asset_id')) {
            $this->assetId = $id;
            $asset = Asset::find($id);
            if ($asset) {
                $this->currentCustodian = $asset->assigned_user ?: 'Sarah Lim';
                $this->currentLocation = $asset->assigned_location ?: 'KL Office - Finance';
            }
        }
    }

    public function submit()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $this->validate([
            'assetId' => 'required',
            'transferTo' => 'required',
        ]);

        $asset = Asset::find($this->assetId);
        if ($asset) {
            $oldCustodian = $asset->assigned_user;
            $asset->update([
                'assigned_user' => $this->transferTo,
                'department' => $this->newDepartment,
                'assigned_location' => $this->newLocation,
            ]);

            Transaction::create([
                'type' => 'Transfer',
                'asset_id' => $asset->id,
                'from_user' => $oldCustodian,
                'to_user' => $this->transferTo,
                'to_department' => $this->newDepartment,
                'to_location' => $this->newLocation,
                'transaction_date' => $this->effectiveDate,
                'remarks' => $this->reason,
                'performed_by' => $actor->name,
            ]);

            AuditLogService::record('Transfer asset', 'Asset', $asset->id, "Transferred from {$oldCustodian}", "Transferred to {$this->transferTo}");
        }

        session()->flash('success', "Asset {$this->assetId} transferred to {$this->transferTo} successfully!");

        return redirect()->route('assets.assigned');
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $assets = Asset::all();
        $employees = Employee::where('status', 'Active')->get();
        $departments = Department::all();

        return view('livewire.transactions.transfer-asset', compact('assets', 'employees', 'departments'))
            ->layout('layouts.app', ['title' => 'Transfer Asset', 'breadcrumb' => 'IT Asset Management / Transactions / Transfer Asset']);
    }
}
