<?php

namespace App\Livewire\Administration;

use App\Enums\ItamRole;
use App\Models\User;
use App\Support\ItamPermissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class Roles extends Component
{
    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasPermission('admin.roles'), 403);

        $permissions = ItamPermissions::matrixForUi();
        $roles = ItamRole::cases();

        return view('livewire.administration.roles', compact('permissions', 'roles'))
            ->layout('layouts.app', ['title' => 'Roles & Permissions', 'breadcrumb' => 'IT Asset Management / Administration / Roles']);
    }
}
