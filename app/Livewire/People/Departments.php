<?php

namespace App\Livewire\People;

use App\Models\Department;
use Livewire\Component;

class Departments extends Component
{
    public function render()
    {
        $departments = Department::all();

        return view('livewire.people.departments', compact('departments'))
            ->layout('layouts.app', ['title' => 'Departments', 'breadcrumb' => 'IT Asset Management / People / Departments']);
    }
}
