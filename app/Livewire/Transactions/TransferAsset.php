<?php

namespace App\Livewire\Transactions;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TransferAsset extends Component
{
    public string $assetId = '';

    public string $assetSearch = '';

    public string $currentCustodian = '';

    public string $currentLocation = 'Jitra Office';

    public string $transferTo = '';

    public string $newDepartment = 'Finance';

    public string $newLocation = 'Kulim Office';

    public string $effectiveDate = '';

    public string $reason = '';

    public function mount(): void
    {
        $this->effectiveDate = now()->format('Y-m-d');

        $requestedId = (string) request()->query('asset_id', '');
        $asset = null;
        if ($requestedId !== '') {
            $asset = Asset::find($requestedId);
        }

        if (! $asset) {
            $asset = Asset::where('status', 'Assigned')->orderBy('id')->first();
        }

        if ($asset) {
            $this->applyAssetDefaults($asset);
        }
    }

    public function updatedAssetId(string $value): void
    {
        $asset = Asset::find($value);
        if ($asset) {
            $this->applyAssetDefaults($asset);
        }
    }

    private function applyAssetDefaults(Asset $asset): void
    {
        $this->assetId = $asset->id;
        $this->currentCustodian = $asset->display_assigned_user ?: ($asset->assigned_user ?: 'Unassigned');
        $this->currentLocation = in_array($asset->assigned_location, Location::OFFICE_OPTIONS, true)
            ? $asset->assigned_location
            : Location::OFFICE_OPTIONS[0];
        $this->newDepartment = $asset->department ?: 'Finance';
        $this->newLocation = $this->currentLocation === Location::OFFICE_OPTIONS[0]
            ? Location::OFFICE_OPTIONS[1]
            : Location::OFFICE_OPTIONS[0];
    }

    public function submit()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $this->validate([
            'assetId' => 'required|exists:fg_itam_assets,id',
            'newDepartment' => 'required|string|max:100',
            'transferTo' => 'nullable|string|max:150',
            'newLocation' => ['required', 'string', 'in:'.implode(',', Location::OFFICE_OPTIONS)],
        ]);

        $asset = Asset::find($this->assetId);
        if ($asset) {
            $oldCustodian = $asset->assigned_user;
            $newCustodian = trim($this->transferTo) !== '' ? trim($this->transferTo) : null;

            $asset->update([
                'assigned_user' => $newCustodian,
                'department' => $this->newDepartment,
                'assigned_location' => $this->newLocation,
            ]);

            Transaction::create([
                'type' => 'Transfer',
                'asset_id' => $asset->id,
                'from_user' => $oldCustodian,
                'to_user' => $newCustodian,
                'to_department' => $this->newDepartment,
                'to_location' => $this->newLocation,
                'transaction_date' => $this->effectiveDate,
                'remarks' => $this->reason,
                'performed_by' => $actor->display_name,
            ]);

            $fromLabel = $oldCustodian ?: ($asset->department ?: 'Department');
            $toLabel = $newCustodian ?: $this->newDepartment;

            AuditLogService::record('Transfer asset', 'Asset', $asset->id, "Transferred from {$fromLabel}", "Transferred to {$toLabel}");

            session()->flash('success', "Asset {$this->assetId} transferred to {$toLabel} successfully!");
        }

        return redirect()->route('assets.assigned');
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $query = Asset::query()->whereIn('status', ['Assigned', 'Loan']);

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

        $assets = $query->orderBy('id')->get();

        $selectedAsset = $this->assetId ? Asset::find($this->assetId) : null;
        $employees = Employee::where('status', 'Active')->get();
        $departments = Department::all();

        return view('livewire.transactions.transfer-asset', compact('assets', 'selectedAsset', 'employees', 'departments'))
            ->layout('layouts.app', ['title' => 'Transfer Asset', 'breadcrumb' => 'IT Asset Management / Transactions / Transfer Asset']);
    }
}
