<?php

namespace App\Livewire;

use App\Enums\ItamRole;
use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('dashboard.view'), 403);

        $scopedBase = function () use ($actor) {
            $q = Asset::query();
            if ($actor->hasRole(ItamRole::Employee)) {
                $names = array_values(array_unique(array_filter([
                    strtolower(trim($actor->name)),
                    strtolower(trim($actor->username)),
                ])));
                $q->where(function ($sub) use ($names): void {
                    foreach ($names as $name) {
                        $sub->orWhereRaw('LOWER(assigned_user) = ?', [$name]);
                    }
                });
            } else {
                $actor->scopeDepartmentQuery($q, 'department');
            }

            return $q;
        };

        $totalAssets = $scopedBase()->count();
        $assigned = $scopedBase()->where('status', 'Assigned')->count();
        $available = $scopedBase()->where('status', 'Available')->count();
        $repair = $scopedBase()->where('status', 'In Repair')->count();
        $attentionNeeded = $scopedBase()->whereIn('status', ['In Repair', 'Missing', 'Damaged'])->count();

        $assignedPercent = $totalAssets > 0 ? round(($assigned / $totalAssets) * 100, 1) : 0;
        $availablePercent = $totalAssets > 0 ? round(($available / $totalAssets) * 100, 1) : 0;
        $repairPercent = $totalAssets > 0 ? round(($repair / $totalAssets) * 100, 1) : 0;

        $expiringWarranties = $scopedBase()->whereNotNull('warranty_end')->count();
        $recentActivities = ($actor->hasPermission('admin.audit_logs') || $actor->hasRole(ItamRole::ItSupport))
            ? AuditLog::orderByDesc('timestamp')->limit(6)->get()
            : collect();

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
