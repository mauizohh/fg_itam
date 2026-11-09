<?php

namespace App\Livewire\Administration;

use Livewire\Component;

class Users extends Component
{
    public string $search = '';
    public string $roleFilter = 'All';
    public string $statusFilter = 'Active';

    public function render()
    {
        $users = [
            ['name' => 'Amir Admin', 'email' => 'amir@example.com', 'role' => 'System Administrator', 'last_login' => 'Today 09:12', 'status' => 'Active'],
            ['name' => 'Nora Support', 'email' => 'nora@example.com', 'role' => 'IT Support', 'last_login' => 'Today 08:50', 'status' => 'Active'],
            ['name' => 'Lee Finance', 'email' => 'lee@example.com', 'role' => 'Finance', 'last_login' => 'Yesterday', 'status' => 'Active'],
            ['name' => 'Audit User', 'email' => 'audit@example.com', 'role' => 'Auditor', 'last_login' => '05 Aug 2026', 'status' => 'Active'],
        ];

        return view('livewire.administration.users', compact('users'))
            ->layout('layouts.app', ['title' => 'System Users', 'breadcrumb' => 'IT Asset Management / Administration / Users']);
    }
}
