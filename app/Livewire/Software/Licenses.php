<?php

namespace App\Livewire\Software;

use App\Models\License;
use Livewire\Component;

class Licenses extends Component
{
    public function render()
    {
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
