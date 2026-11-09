<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Employee;
use Livewire\Component;
use Livewire\WithPagination;

class AssignedAssets extends Component
{
    use WithPagination;

    public string $search = '';
    public string $assignedToFilter = 'All';
    public string $departmentFilter = 'All';
    public string $locationFilter = 'All';

    public function render()
    {
        $query = Asset::where('status', 'Assigned');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%")
                  ->orWhere('assigned_user', 'like', "%{$this->search}%");
            });
        }

        if ($this->assignedToFilter !== 'All') {
            $query->where('assigned_user', $this->assignedToFilter);
        }

        if ($this->departmentFilter !== 'All') {
            $query->where('department', $this->departmentFilter);
        }

        if ($this->locationFilter !== 'All') {
            $query->where('assigned_location', 'like', "%{$this->locationFilter}%");
        }

        $assignments = $query->orderByDesc('assigned_date')->paginate(15);
        $departments = Department::all();
        $employees = Employee::where('status', 'Active')->get();

        return view('livewire.assets.assigned-assets', compact('assignments', 'departments', 'employees'))
            ->layout('layouts.app', ['title' => 'Assigned Assets', 'breadcrumb' => 'IT Asset Management / Assets / Assigned']);
    }
}
