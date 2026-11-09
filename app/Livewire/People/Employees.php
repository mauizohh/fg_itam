<?php

namespace App\Livewire\People;

use App\Models\Department;
use App\Models\Employee;
use Livewire\Component;

class Employees extends Component
{
    public string $search = '';
    public string $departmentFilter = 'All';
    public string $statusFilter = 'Active';

    public function render()
    {
        $query = Employee::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', "%{$this->search}%")
                  ->orWhere('department', 'like', "%{$this->search}%");
        }

        if ($this->departmentFilter !== 'All') {
            $query->where('department', $this->departmentFilter);
        }

        if ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        $employees = $query->orderBy('name')->get();
        $departments = Department::all();

        return view('livewire.people.employees', compact('employees', 'departments'))
            ->layout('layouts.app', ['title' => 'Employees', 'breadcrumb' => 'IT Asset Management / People / Employees']);
    }
}
