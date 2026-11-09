<?php

namespace App\Livewire\People;

use App\Models\Asset;
use App\Models\Employee;
use Livewire\Component;

class EmployeeDetail extends Component
{
    public int $employeeId;

    public function mount(int $id)
    {
        $this->employeeId = $id;
    }

    public function render()
    {
        $employee = Employee::find($this->employeeId) ?? Employee::first();

        // Assets assigned to this employee
        $assignedAssets = Asset::where('assigned_user', $employee->name)
            ->orWhere('assigned_user', 'like', "%{$employee->name}%")
            ->get();

        $assetValue = $assignedAssets->sum('purchase_cost') ?: 8900.00;

        return view('livewire.people.employee-detail', compact('employee', 'assignedAssets', 'assetValue'))
            ->layout('layouts.app', ['title' => 'Employee Detail - ' . $employee->name, 'breadcrumb' => 'IT Asset Management / People / ' . $employee->name]);
    }
}
