<?php

namespace App\Livewire\Transactions;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReturnAsset extends Component
{
    public string $assetId = '';

    public string $assetSearch = '';

    public string $returnedBy = '';

    public string $returnDate = '';

    public string $returnLocation = 'Jitra Office';

    public string $condition = 'Good';

    public string $nextAction = 'Available for reassignment';

    public string $missingAccessories = 'None';

    public string $remarks = 'Device passed visual inspection';

    public bool $checkPowerOn = true;

    public bool $checkCharger = true;

    public bool $checkDataHandling = true;

    public function mount(): void
    {
        Employee::ensureSynced();

        $this->returnDate = now()->format('Y-m-d');
        $requestedId = (string) request()->query('asset_id', '');
        if ($requestedId !== '') {
            $this->assetId = $requestedId;
            $asset = Asset::find($requestedId);
            if ($asset) {
                $this->returnedBy = $asset->display_assigned_user ?: ($asset->assigned_user ?: '');
                if ($asset->assigned_location && in_array($asset->assigned_location, Location::OFFICE_OPTIONS, true)) {
                    $this->returnLocation = $asset->assigned_location;
                }
                if ($asset->condition) {
                    $this->condition = $asset->condition;
                }
            }
        } else {
            $first = Asset::whereIn('status', ['Assigned', 'Loan'])->orderBy('id')->first();
            if ($first) {
                $this->assetId = $first->id;
                $this->returnedBy = $first->display_assigned_user ?: ($first->assigned_user ?: '');
                if ($first->assigned_location && in_array($first->assigned_location, Location::OFFICE_OPTIONS, true)) {
                    $this->returnLocation = $first->assigned_location;
                }
                if ($first->condition) {
                    $this->condition = $first->condition;
                }
            }
        }
    }

    public function updatedAssetId(string $value): void
    {
        $asset = Asset::find($value);
        if ($asset) {
            $this->returnedBy = $asset->display_assigned_user ?: ($asset->assigned_user ?: '');
            if ($asset->assigned_location && in_array($asset->assigned_location, Location::OFFICE_OPTIONS, true)) {
                $this->returnLocation = $asset->assigned_location;
            }
            if ($asset->condition) {
                $this->condition = $asset->condition;
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
            'returnLocation' => ['required', 'string', 'in:'.implode(',', Location::OFFICE_OPTIONS)],
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
                'performed_by' => $actor->display_name,
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

        $query = Asset::whereIn('status', ['Assigned', 'Loan']);
        if (trim($this->assetSearch) !== '') {
            $term = trim($this->assetSearch);
            $query->where(function ($sq) use ($term) {
                $sq->where('id', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('assigned_user', 'like', "%{$term}%")
                    ->orWhere('department', 'like', "%{$term}%")
                    ->orWhere('serial_number', 'like', "%{$term}%");
            });
        }

        $assignedAssets = $query->orderBy('id')->get();
        $selectedAsset = $this->assetId ? Asset::find($this->assetId) : null;
        $employees = Employee::where('status', 'Active')->orderBy('name')->get();

        $employeeOptions = $employees->map(fn ($emp) => [
            'id' => (string) $emp->id,
            'name' => $emp->name,
            'department' => $emp->department ?: '',
            'email' => $emp->email ?: '',
        ])->values();

        if ($this->returnedBy !== '' && ! $employeeOptions->contains('name', $this->returnedBy)) {
            $employeeOptions->prepend([
                'id' => 'custom',
                'name' => $this->returnedBy,
                'department' => '',
                'email' => '',
            ]);
        }

        return view('livewire.transactions.return-asset', compact('assignedAssets', 'selectedAsset', 'employees', 'employeeOptions'))
            ->layout('layouts.app', ['title' => 'Return Asset', 'breadcrumb' => 'IT Asset Management / Transactions / Return Asset']);
    }
}
