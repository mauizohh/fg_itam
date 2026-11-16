<?php

namespace App\Livewire\Maintenance;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Warranty extends Component
{
    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('maintenance.view'), 403);

        $assets = $actor->scopeDepartmentQuery(Asset::whereNotNull('warranty_end'), 'department')
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
