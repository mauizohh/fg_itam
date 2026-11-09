<?php

namespace App\Livewire\Maintenance;

use App\Models\Asset;
use Carbon\Carbon;
use Livewire\Component;

class Warranty extends Component
{
    public function render()
    {
        $assets = Asset::whereNotNull('warranty_end')
            ->orderBy('warranty_end')
            ->get();

        $coveredCount = 846;
        $expiresIn30 = 12;
        $expiredCount = 224;
        $unknownCount = 18;

        return view('livewire.maintenance.warranty', compact(
            'assets',
            'coveredCount',
            'expiresIn30',
            'expiredCount',
            'unknownCount'
        ))->layout('layouts.app', ['title' => 'Warranty Management', 'breadcrumb' => 'IT Asset Management / Maintenance / Warranty']);
    }
}
