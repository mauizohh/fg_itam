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

        $departments = $actor->scopeDepartmentQuery(Department::query(), 'name')->get();

        return view('livewire.people.departments', compact('departments'))
            ->layout('layouts.app', ['title' => 'Departments', 'breadcrumb' => 'IT Asset Management / People / Departments']);
    }
}
