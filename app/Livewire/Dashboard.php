<?php

namespace App\Livewire;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\Repair;
use App\Models\Renewal;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $totalAssets = Asset::count();
        $assigned = Asset::where('status', 'Assigned')->count();
        $available = Asset::where('status', 'Available')->count();
        $repair = Asset::where('status', 'In Repair')->count();
        $attentionNeeded = Asset::whereIn('status', ['In Repair', 'Missing', 'Damaged'])->count();

        $assignedPercent = $totalAssets > 0 ? round(($assigned / $totalAssets) * 100, 1) : 0;
        $availablePercent = $totalAssets > 0 ? round(($available / $totalAssets) * 100, 1) : 0;
        $repairPercent = $totalAssets > 0 ? round(($repair / $totalAssets) * 100, 1) : 0;

        $expiringWarranties = Asset::whereNotNull('warranty_end')->count();
        $recentActivities = AuditLog::orderByDesc('timestamp')->limit(6)->get();

        return view('livewire.dashboard', compact(
            'totalAssets',
            'assigned',
            'assignedPercent',
            'available',
            'availablePercent',
            'repair',
            'repairPercent',
            'attentionNeeded',
            'expiringWarranties',
            'recentActivities'
        ))->layout('layouts.app', ['title' => 'Dashboard', 'breadcrumb' => 'IT Asset Management / Dashboard']);
    }
}
