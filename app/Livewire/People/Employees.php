<?php

namespace App\Livewire\People;

use App\Models\Asset;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Employees extends Component
{
    use WithPagination;

    public string $search = '';

    public string $departmentFilter = 'All';

    public string $statusFilter = 'Active';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDepartmentFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('people.view'), 403);

        Department::ensureSynced();
        Employee::ensureSynced();

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

        $employees = $query->orderBy('name')->paginate(20);

        // Compute real-time asset & pending returns counts aligned with EmployeeDetail
        $activeAssets = Asset::query()
            ->whereNotNull('assigned_user')
            ->where('assigned_user', '!=', '')
            ->whereIn('status', ['Assigned', 'Loan'])
            ->get(['id', 'assigned_user', 'status', 'expected_return_date', 'purchase_cost']);

        foreach ($employees as $emp) {
            $empName = strtolower(trim($emp->name));
            $matched = $activeAssets->filter(function (Asset $asset) use ($empName): bool {
                $assigned = strtolower(trim((string) $asset->assigned_user));

                return $assigned === $empName
                    || str_contains($assigned, $empName)
                    || str_contains($empName, $assigned);
            });

            $assignedCount = $matched->count();
            $pendingCount = $matched->filter(
                fn (Asset $a): bool => $a->status === 'Loan' || ! empty($a->expected_return_date)
            )->count();

            $emp->setAttribute('assigned_assets_count_computed', $assignedCount);
            $emp->setAttribute('pending_returns_count_computed', $pendingCount);
            $emp->assigned_assets_count = $assignedCount;
            $emp->pending_returns_count = $pendingCount;
        }

        $departments = $actor->scopeDepartmentQuery(Department::query(), 'name')->orderBy('name')->get();

        return view('livewire.people.employees', compact('employees', 'departments'))
            ->layout('layouts.app', ['title' => 'Employees', 'breadcrumb' => 'IT Asset Management / People / Employees']);
    }
}
