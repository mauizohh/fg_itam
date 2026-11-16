<?php

namespace App\Livewire\People;

use App\Models\Asset;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EmployeeDetail extends Component
{
    public int $employeeId;

    public function mount(int $id)
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('people.view'), 403);

        $this->employeeId = $id;
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('people.view'), 403);

        $employee = Employee::find($this->employeeId);
        if ($actor->isDepartmentScoped()) {
            abort_if($employee === null, 404);
            abort_unless($actor->canAccessDepartment($employee->department), 403);
        }

        $employee = $employee ?? Employee::firstOrFail();

        // Assets assigned to this employee
        $assignedAssets = Asset::query()
            ->where(function ($query) use ($employee): void {
                $query->where('assigned_user', $employee->name)
                    ->orWhere('assigned_user', 'like', "%{$employee->name}%");
            })
            ->get();

        $assetValue = $actor->canViewFinancialData()
            ? (float) $assignedAssets->sum('purchase_cost')
            : 0.0;

        return view('livewire.people.employee-detail', compact('employee', 'assignedAssets', 'assetValue'))
            ->layout('layouts.app', ['title' => 'Employee Detail - '.$employee->name, 'breadcrumb' => 'IT Asset Management / People / '.$employee->name]);
    }
}
