<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.view'), 403);

        $query = $actor->scopeDepartmentQuery(Asset::where('status', 'Assigned'), 'department');

        if (! empty($this->search)) {
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

        Department::ensureSynced();
        Employee::ensureSynced();

        $assignments = $query->orderByDesc('assigned_date')->paginate(15);
        $departments = $actor->scopeDepartmentQuery(Department::query(), 'name')->orderBy('name')->get();

        $assignedCustodians = Asset::query()
            ->whereIn('status', ['Assigned', 'Loan'])
            ->whereNotNull('assigned_user')
            ->where('assigned_user', '!=', '')
            ->distinct()
            ->pluck('assigned_user')
            ->all();

        $employeeQuery = Employee::where('status', 'Active');
        if (! empty($assignedCustodians)) {
            $employeeQuery->where(function ($q) use ($assignedCustodians): void {
                $q->whereIn('name', $assignedCustodians)
                    ->orWhere('assigned_assets_count', '>', 0);
            });
        }
        $employees = $actor->scopeDepartmentQuery($employeeQuery, 'department')->orderBy('name')->get();
        if ($employees->isEmpty()) {
            $employees = $actor->scopeDepartmentQuery(Employee::where('status', 'Active'), 'department')->orderBy('name')->get();
        }

        return view('livewire.assets.assigned-assets', compact('assignments', 'departments', 'employees'))
            ->layout('layouts.app', ['title' => 'Assigned Assets', 'breadcrumb' => 'IT Asset Management / Assets / Assigned']);
    }
}
