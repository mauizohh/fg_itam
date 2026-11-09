<?php

namespace App\Livewire\Administration;

use Livewire\Component;

class Roles extends Component
{
    public function render()
    {
        $permissions = [
            ['perm' => 'View all assets', 'admin' => 'Yes', 'it_admin' => 'Yes', 'it_support' => 'Yes', 'finance' => 'Yes', 'auditor' => 'Yes'],
            ['perm' => 'Create / edit assets', 'admin' => 'Yes', 'it_admin' => 'Yes', 'it_support' => 'Limited', 'finance' => 'No', 'auditor' => 'No'],
            ['perm' => 'Assign / return assets', 'admin' => 'Yes', 'it_admin' => 'Yes', 'it_support' => 'Yes', 'finance' => 'No', 'auditor' => 'No'],
            ['perm' => 'View financial data', 'admin' => 'Yes', 'it_admin' => 'Yes', 'it_support' => 'No', 'finance' => 'Yes', 'auditor' => 'Read'],
            ['perm' => 'Manage users / roles', 'admin' => 'Yes', 'it_admin' => 'No', 'it_support' => 'No', 'finance' => 'No', 'auditor' => 'No'],
            ['perm' => 'Run audits', 'admin' => 'Yes', 'it_admin' => 'Yes', 'it_support' => 'Limited', 'finance' => 'No', 'auditor' => 'Yes'],
            ['perm' => 'Delete / dispose records', 'admin' => 'Yes', 'it_admin' => 'Yes', 'it_support' => 'No', 'finance' => 'No', 'auditor' => 'No'],
        ];

        return view('livewire.administration.roles', compact('permissions'))
            ->layout('layouts.app', ['title' => 'Roles & Permissions', 'breadcrumb' => 'IT Asset Management / Administration / Roles']);
    }
}
