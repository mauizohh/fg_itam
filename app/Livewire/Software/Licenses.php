<?php

namespace App\Livewire\Software;

use App\Models\License;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Licenses extends Component
{
    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('software.view'), 403);

        $licenses = License::all();
        $purchasedSeats = 1240;
        $assignedSeats = 1082;
        $availableSeats = 158;
        $overallocated = 0;

        return view('livewire.software.licenses', compact(
            'licenses',
            'purchasedSeats',
            'assignedSeats',
            'availableSeats',
            'overallocated'
        ))->layout('layouts.app', ['title' => 'License Utilization', 'breadcrumb' => 'IT Asset Management / Software / Licenses']);
    }
}
