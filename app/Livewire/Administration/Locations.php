<?php

namespace App\Livewire\Administration;

use App\Models\Location;
use Livewire\Component;

class Locations extends Component
{
    public string $selectedOffice = 'Kuala Lumpur Office';

    public function selectOffice(string $office)
    {
        $this->selectedOffice = $office;
    }

    public function render()
    {
        $locations = Location::where('office', $this->selectedOffice)->get();

        $assetsTotal = ($this->selectedOffice === 'Kuala Lumpur Office') ? 782 : 120;
        $employeesTotal = ($this->selectedOffice === 'Kuala Lumpur Office') ? 286 : 45;
        $exceptionsTotal = ($this->selectedOffice === 'Kuala Lumpur Office') ? 8 : 2;

        return view('livewire.administration.locations', compact('locations', 'assetsTotal', 'employeesTotal', 'exceptionsTotal'))
            ->layout('layouts.app', ['title' => 'Locations', 'breadcrumb' => 'IT Asset Management / Administration / Locations']);
    }
}
