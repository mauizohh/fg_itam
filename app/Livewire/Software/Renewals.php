<?php

namespace App\Livewire\Software;

use App\Models\Renewal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Renewals extends Component
{
    public bool $showModal = false;

    public string $newId = 'REN-0102';

    public string $serviceName = '';

    public string $owner = 'IT Operations';

    public string $renewalDate = '2026-11-30';

    public float $annualCost = 12000.00;

    public function createRenewal()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('software.manage'), 403);

        $this->validate([
            'newId' => 'required',
            'serviceName' => 'required',
            'annualCost' => 'required|numeric',
        ]);

        Renewal::create([
            'id' => $this->newId,
            'service_name' => $this->serviceName,
            'owner' => $this->owner,
            'renewal_date' => $this->renewalDate,
            'annual_cost' => $this->annualCost,
            'status' => 'Planned',
        ]);

        $this->showModal = false;
        session()->flash('success', "Renewal contract {$this->newId} registered!");
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('software.view'), 403);

        $renewals = Renewal::orderBy('renewal_date')->get();

        return view('livewire.software.renewals', compact('renewals'))
            ->layout('layouts.app', ['title' => 'Software Renewals', 'breadcrumb' => 'IT Asset Management / Software / Renewals']);
    }
}
