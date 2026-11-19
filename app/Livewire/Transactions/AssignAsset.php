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

class AssignAsset extends Component
{
    public string $assetId = '';

    public string $assetSearch = '';

    public string $categoryFilter = '';

    public string $assignTo = '';

    public string $department = 'Finance';

    public string $location = 'Jitra Office';

    public string $assignedDate = '';

    public string $expectedReturn = 'Not required';

    public string $condition = 'Good';

    public string $accessories = '';

    public string $remarks = '';

    public function mount(): void
    {
        $this->assignedDate = now()->format('Y-m-d');

        $requestedId = (string) request()->query('asset_id', '');
        $asset = null;
        if ($requestedId !== '') {
            $asset = Asset::find($requestedId);
        }

        if (! $asset) {
            $asset = Asset::where('status', 'Available')->orderBy('id')->first();
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
        $this->location = in_array($asset->assigned_location, Location::OFFICE_OPTIONS, true)
            ? $asset->assigned_location
            : Location::OFFICE_OPTIONS[0];
        $this->department = $asset->department ?: 'Finance';
        $this->condition = $asset->condition ?: 'Good';
        $this->accessories = $asset->accessories ?: '';
    }

    public function submit()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $this->validate([
            'assetId' => 'required|exists:fg_itam_assets,id',
            'department' => 'required|string|max:100',
            'assignTo' => 'nullable|string|max:150',
            'location' => ['required', 'string', 'in:'.implode(',', Location::OFFICE_OPTIONS)],
        ]);

        $asset = Asset::find($this->assetId);
        if ($asset) {
            $oldUser = $asset->assigned_user;
            $assignedUser = trim($this->assignTo) !== '' ? trim($this->assignTo) : null;

            $asset->update([
                'status' => 'Assigned',
                'assigned_user' => $assignedUser,
                'department' => $this->department,
                'assigned_location' => $this->location,
                'assigned_date' => $this->assignedDate,
                'condition' => $this->condition,
                'accessories' => $this->accessories,
                'remarks' => $this->remarks,
            ]);

            Transaction::create([
                'type' => 'Assign',
                'asset_id' => $asset->id,
                'to_user' => $assignedUser,
                'to_department' => $this->department,
                'to_location' => $this->location,
                'transaction_date' => $this->assignedDate,
                'condition_at_time' => $this->condition,
                'accessories' => $this->accessories,
                'remarks' => $this->remarks,
                'performed_by' => $actor->display_name,
            ]);

            $targetDesc = $assignedUser
                ? "Assigned to {$assignedUser} ({$this->department})"
                : "Assigned to {$this->department} department";

            AuditLogService::record('Assign asset', 'Asset', $asset->id, $oldUser ?: $asset->assigned_location, $targetDesc);

            $targetLabel = $assignedUser ?: $this->department;
            session()->flash('success', "Asset {$this->assetId} assigned to {$targetLabel} successfully!");
        }

        return redirect()->route('assets.assigned');
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $query = Asset::query()->where('status', 'Available');

        if ($this->categoryFilter !== '') {
            $query->where('category_id', $this->categoryFilter);
        }

        if (trim($this->assetSearch) !== '') {
            $term = trim($this->assetSearch);
            $query->where(function ($sq) use ($term) {
                $sq->where('id', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('brand', 'like', "%{$term}%")
                    ->orWhere('model', 'like', "%{$term}%")
                    ->orWhere('serial_number', 'like', "%{$term}%");
            });
        }

        $availableAssets = $query->orderBy('id')->get();

        $selectedAsset = $this->assetId ? Asset::find($this->assetId) : null;
        $employees = Employee::where('status', 'Active')->get();
        $departments = Department::all();

        return view('livewire.transactions.assign-asset', compact('availableAssets', 'selectedAsset', 'employees', 'departments'))
            ->layout('layouts.app', ['title' => 'Assign Asset', 'breadcrumb' => 'IT Asset Management / Transactions / Assign Asset']);
    }
}
