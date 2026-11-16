<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Repair;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AssetsInRepair extends Component
{
    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.view'), 403);

        $repairQuery = Repair::query();
        if ($actor->isDepartmentScoped()) {
            $scopedAssetIds = $actor->scopeDepartmentQuery(Asset::query(), 'department')->pluck('id');
            $repairQuery->whereIn('asset_id', $scopedAssetIds);
        }

        $repairs = (clone $repairQuery)->orderBy('opened_date', 'desc')->get();
        $openRepairs = (clone $repairQuery)->whereIn('status', ['Repairing', 'Overdue', 'Awaiting Part'])->count();
        $overdueCount = (clone $repairQuery)->where('status', 'Overdue')->count();
        $awaitingQuote = (clone $repairQuery)->where('status', 'Awaiting Part')->count();
        $repairSpend = $actor->canViewFinancialData() ? (float) (clone $repairQuery)->sum('cost') : 0.0;

        return view('livewire.assets.assets-in-repair', compact(
            'repairs',
            'openRepairs',
            'overdueCount',
            'awaitingQuote',
            'repairSpend'
        ))->layout('layouts.app', ['title' => 'Assets in Repair', 'breadcrumb' => 'IT Asset Management / Assets / In Repair']);
    }
}
