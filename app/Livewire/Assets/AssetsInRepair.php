<?php

namespace App\Livewire\Assets;

use App\Models\Repair;
use Livewire\Component;

class AssetsInRepair extends Component
{
    public function render()
    {
        $repairs = Repair::orderBy('opened_date', 'desc')->get();
        $openRepairs = 25;
        $overdueCount = 4;
        $awaitingQuote = 3;
        $repairSpend = 18430.00;

        return view('livewire.assets.assets-in-repair', compact(
            'repairs',
            'openRepairs',
            'overdueCount',
            'awaitingQuote',
            'repairSpend'
        ))->layout('layouts.app', ['title' => 'Assets in Repair', 'breadcrumb' => 'IT Asset Management / Assets / In Repair']);
    }
}
