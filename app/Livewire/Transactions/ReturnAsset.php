<?php

namespace App\Livewire\Transactions;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReturnAsset extends Component
{
    public string $returnMode = 'single'; // 'single' or 'all'

    public array $selectedAssetIds = [];

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

    public function mount(?string $asset_id = null, ?string $employee = null, ?string $mode = null): void
    {
        Employee::ensureSynced();

        $this->returnDate = now()->format('Y-m-d');
        $requestedId = $asset_id ?? (string) (request()->query('asset_id') ?? request()->input('asset_id', ''));
        $requestedEmployee = $employee ?? (string) (request()->query('employee') ?? request()->input('employee', ''));
        $requestedMode = $mode ?? (string) (request()->query('mode') ?? request()->input('mode', ''));

        if ($requestedId !== '') {
            $this->returnMode = 'single';
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
        } elseif ($requestedEmployee !== '') {
            $this->returnedBy = $requestedEmployee;
            if ($requestedMode === 'all') {
                $this->returnMode = 'all';
            }
            $this->syncAssetsForReturnee();
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

    public function updatedReturnedBy(string $value): void
    {
        $this->syncAssetsForReturnee();
    }

    public function setReturnMode(string $mode): void
    {
        $this->returnMode = in_array($mode, ['single', 'all'], true) ? $mode : 'single';
        if ($this->returnMode === 'all') {
            $this->syncAssetsForReturnee();
        }
    }

    public function syncAssetsForReturnee(): void
    {
        if (trim($this->returnedBy) === '') {
            $this->selectedAssetIds = [];

            return;
        }

        $assets = $this->returneeAssets;
        $this->selectedAssetIds = $assets->pluck('id')->toArray();

        if ($this->assetId === '' || ! in_array($this->assetId, $this->selectedAssetIds, true)) {
            $this->assetId = $assets->first()?->id ?? '';
        }

        if ($assets->isNotEmpty()) {
            $first = $assets->first();
            if ($first->assigned_location && in_array($first->assigned_location, Location::OFFICE_OPTIONS, true)) {
                $this->returnLocation = $first->assigned_location;
            }
            if ($first->condition) {
                $this->condition = $first->condition;
            }
        }
    }

    public function getReturneeAssetsProperty()
    {
        if (trim($this->returnedBy) === '') {
            return collect();
        }

        $name = trim($this->returnedBy);

        return Asset::query()
            ->where(function ($q) use ($name): void {
                $q->where('assigned_user', $name)
                    ->orWhere('assigned_user', 'like', "%{$name}%")
                    ->orWhere('department', $name);
            })
            ->whereIn('status', ['Assigned', 'Loan'])
            ->orderBy('id')
            ->get();
    }

    public function toggleSelectAll(): void
    {
        $allIds = $this->returneeAssets->pluck('id')->toArray();
        if (count($this->selectedAssetIds) === count($allIds)) {
            $this->selectedAssetIds = [];
        } else {
            $this->selectedAssetIds = $allIds;
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

        if ($this->returnMode === 'all') {
            $this->validate([
                'returnedBy' => 'required',
                'returnLocation' => ['required', 'string', 'in:'.implode(',', Location::OFFICE_OPTIONS)],
                'selectedAssetIds' => 'required|array|min:1',
            ], [
                'selectedAssetIds.min' => 'Please select at least one asset to return.',
            ]);

            $assetsToReturn = Asset::whereIn('id', $this->selectedAssetIds)
                ->whereIn('status', ['Assigned', 'Loan'])
                ->get();

            if ($assetsToReturn->isEmpty()) {
                $this->addError('selectedAssetIds', 'No valid assigned assets found to return.');

                return;
            }

            DB::transaction(function () use ($assetsToReturn, $actor): void {
                $newStatus = ($this->nextAction === 'Maintenance') ? 'In Repair' : (($this->nextAction === 'Retired') ? 'Retired' : 'Available');

                foreach ($assetsToReturn as $asset) {
                    $asset->update([
                        'status' => $newStatus,
                        'assigned_user' => null,
                        'expected_return_date' => null,
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

                Employee::syncAssetCounts();
            });

            $count = $assetsToReturn->count();
            session()->flash('success', "Successfully returned {$count} asset(s) from {$this->returnedBy}!");

            return redirect()->route('assets.available');
        }

        // Single asset return mode
        $this->validate([
            'assetId' => 'required',
            'returnedBy' => 'required',
            'returnLocation' => ['required', 'string', 'in:'.implode(',', Location::OFFICE_OPTIONS)],
        ]);

        $asset = Asset::find($this->assetId);
        if ($asset) {
            DB::transaction(function () use ($asset, $actor): void {
                $newStatus = ($this->nextAction === 'Maintenance') ? 'In Repair' : (($this->nextAction === 'Retired') ? 'Retired' : 'Available');

                $asset->update([
                    'status' => $newStatus,
                    'assigned_user' => null,
                    'expected_return_date' => null,
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
                Employee::syncAssetCounts();
            });

            session()->flash('success', "Asset {$this->assetId} returned successfully!");
        }

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
            $query->where(function ($sq) use ($term): void {
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
        $returneeAssets = $this->returneeAssets;

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

        return view('livewire.transactions.return-asset', compact('assignedAssets', 'selectedAsset', 'employees', 'employeeOptions', 'returneeAssets'))
            ->layout('layouts.app', ['title' => 'Return Asset', 'breadcrumb' => 'IT Asset Management / Transactions / Return Asset']);
    }
}
