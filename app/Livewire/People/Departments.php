<?php

namespace App\Livewire\People;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Departments extends Component
{
    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('people.view'), 403);

        Department::ensureSynced();

        $departments = $actor->scopeDepartmentQuery(Department::query(), 'name')->orderBy('name')->get();
        $topDepartments = $departments->sortByDesc('active_assets_count')->take(4);

        return view('livewire.people.departments', compact('departments', 'topDepartments'))
            ->layout('layouts.app', ['title' => 'Departments', 'breadcrumb' => 'IT Asset Management / People / Departments']);
    }
}
