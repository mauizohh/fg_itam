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
    public string $assetId = 'IT-LAP-2026-00418';

    public string $assignTo = 'Sarah Lim';

    public string $department = 'Finance';

    public string $location = 'Jitra Office';

    public string $assignedDate = '2026-08-14';

    public string $expectedReturn = 'Not required';

    public string $condition = 'Good';

    public string $accessories = 'Charger, Dock';

    public string $remarks = 'Standard employee laptop assignment';

    public function mount()
    {
        if ($id = request()->query('asset_id')) {
            $this->assetId = $id;
        }
    }

    public function submit()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $this->validate([
            'assetId' => 'required',
            'assignTo' => 'required',
            'location' => ['required', 'string', 'in:'.implode(',', Location::OFFICE_OPTIONS)],
        ]);

        $asset = Asset::find($this->assetId);
        if ($asset) {
            $oldUser = $asset->assigned_user;
            $asset->update([
                'status' => 'Assigned',
                'assigned_user' => $this->assignTo,
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
                'to_user' => $this->assignTo,
                'to_department' => $this->department,
                'to_location' => $this->location,
                'transaction_date' => $this->assignedDate,
                'condition_at_time' => $this->condition,
                'accessories' => $this->accessories,
                'remarks' => $this->remarks,
                'performed_by' => $actor->display_name,
            ]);

            AuditLogService::record('Assign asset', 'Asset', $asset->id, $oldUser ?: 'Jitra Office', "Assigned to {$this->assignTo}");
        }

        session()->flash('success', "Asset {$this->assetId} assigned to {$this->assignTo} successfully!");

        return redirect()->route('assets.assigned');
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('transactions.manage'), 403);

        $availableAssets = Asset::whereIn('status', ['Available', 'Assigned'])->get();
        $employees = Employee::where('status', 'Active')->get();
        $departments = Department::all();

        return view('livewire.transactions.assign-asset', compact('availableAssets', 'employees', 'departments'))
            ->layout('layouts.app', ['title' => 'Assign Asset', 'breadcrumb' => 'IT Asset Management / Transactions / Assign Asset']);
    }
}
