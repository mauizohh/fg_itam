<?php

namespace App\Livewire\Administration;

use App\Models\Asset;
use App\Models\AuditException;
use App\Models\Employee;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Locations extends Component
{
    public string $selectedOffice = 'Jitra Office';

    public function selectOffice(string $office): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.locations'), 403);

        if (in_array($office, Location::OFFICE_OPTIONS, true)) {
            $this->selectedOffice = $office;
        }
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('admin.locations'), 403);

        $locations = Location::where('office', $this->selectedOffice)->get();

        $assetsTotal = Asset::where('assigned_location', 'like', "%{$this->selectedOffice}%")->count();
        $employeesTotal = Employee::where('location', 'like', "%{$this->selectedOffice}%")
            ->where('status', 'Active')
            ->count();
        $exceptionsTotal = AuditException::where('status', 'Open')
            ->where(function ($query): void {
                $query->where('expected_info', 'like', "%{$this->selectedOffice}%")
                    ->orWhere('observed_info', 'like', "%{$this->selectedOffice}%");
            })
            ->count();

        return view('livewire.administration.locations', compact('locations', 'assetsTotal', 'employeesTotal', 'exceptionsTotal'))
            ->layout('layouts.app', ['title' => 'Locations', 'breadcrumb' => 'IT Asset Management / Administration / Locations']);
    }
}
