<?php

namespace App\Livewire\People;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Employees extends Component
{
    public string $search = '';

    public string $departmentFilter = 'All';

    public string $statusFilter = 'Active';

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('people.view'), 403);

        $query = $actor->scopeDepartmentQuery(Employee::query(), 'department');

        if (! empty($this->search)) {
            $query->where(function ($q): void {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('department', 'like', "%{$this->search}%");
            });
        }

        if ($this->departmentFilter !== 'All') {
            $query->where('department', $this->departmentFilter);
        }

        if ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        $employees = $query->orderBy('name')->get();
        $departments = $actor->scopeDepartmentQuery(Department::query(), 'name')->get();

        return view('livewire.people.employees', compact('employees', 'departments'))
            ->layout('layouts.app', ['title' => 'Employees', 'breadcrumb' => 'IT Asset Management / People / Employees']);
    }
}
