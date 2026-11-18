<?php

namespace App\Livewire;

use App\Enums\ItamRole;
use App\Models\Asset;
use App\Models\AssetAcknowledgement;
use App\Models\AuditLog;
use App\Models\Renewal;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
                    strtolower(trim($actor->display_name)),
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

        // 1. Upcoming Actions Aggregation
        $upcomingActions = $this->resolveUpcomingActions($actor, $scopedBase);

        // 2. Recent Activities (strictly asset-related, scoped per role)
        $activityQuery = AuditLog::query()->forAssets()->orderByDesc('timestamp');

        if ($actor->hasRole(ItamRole::Employee)) {
            $myAssetIds = $scopedBase()->pluck('id')->toArray();
            $activityQuery->forEmployee($actor, $myAssetIds);
        } elseif ($actor->isDepartmentScoped()) {
            $deptAssetIds = $scopedBase()->pluck('id')->toArray();
            $activityQuery->forDepartment($actor->departmentScopeNames(), $deptAssetIds);
        }

        $recentActivities = $activityQuery->limit(8)->get();

        // Fetch corresponding assets for metadata (name, model, current custodian)
        $activityAssetIds = $recentActivities->pluck('object_id')->unique()->filter()->values();
        $activityAssets = Asset::whereIn('id', $activityAssetIds)->get()->keyBy('id');

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
            'upcomingActions',
            'recentActivities',
            'activityAssets'
        ))->layout('layouts.app', ['title' => 'Dashboard', 'breadcrumb' => 'IT Asset Management / Dashboard']);
    }

    /**
     * Resolve aggregated upcoming operational action categories for the actor.
     *
     * @param  \Closure(): Builder  $scopedBase
     * @return Collection<int, array{
     *     title: string,
     *     count: int,
     *     description: string,
     *     badge_class: string,
     *     route: string,
     *     action_label: string
     * }>
     */
    private function resolveUpcomingActions(User $actor, \Closure $scopedBase): Collection
    {
        $categories = collect();

        // 1. Software Renewals
        if ($actor->hasPermission('software.view') || ! $actor->hasRole(ItamRole::Employee)) {
            $renewalsCount = Renewal::where(function ($q): void {
                $q->whereIn('status', ['Approval Needed', 'In Review', 'Planned'])
                    ->orWhere('renewal_date', '<=', now()->addDays(90));
            })->count();

            $categories->push([
                'title' => 'Software Renewals',
                'count' => $renewalsCount,
                'description' => 'Contracts pending approval or renewal',
                'badge_class' => $renewalsCount > 0 ? 'bg-red-100 text-[#a92d31]' : 'bg-emerald-100 text-emerald-800',
                'route' => route('software.renewals'),
                'action_label' => 'See More',
            ]);
        }

        // 2. Asset Replacements
        if ($actor->hasPermission('assets.view') || ! $actor->hasRole(ItamRole::Employee)) {
            $replacementsCount = $scopedBase()
                ->where(function ($q): void {
                    $q->whereNotNull('replacement_due')
                        ->orWhereIn('status', ['Damaged', 'In Repair']);
                })
                ->count();

            $replacementRoute = $actor->hasPermission('maintenance.view')
                ? route('maintenance.repairs')
                : ($actor->hasPermission('assets.view') ? route('assets.index') : route('dashboard'));

            $categories->push([
                'title' => 'Asset Replacements',
                'count' => $replacementsCount,
                'description' => 'Scheduled cycle replacement or damaged',
                'badge_class' => $replacementsCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800',
                'route' => $replacementRoute,
                'action_label' => 'See More',
            ]);
        }

        // 3. Warranty Expirations
        if ($actor->hasPermission('maintenance.view') || ! $actor->hasRole(ItamRole::Employee)) {
            $warrantyCount = $scopedBase()
                ->whereNotNull('warranty_end')
                ->whereBetween('warranty_end', [now()->subDays(30), now()->addDays(60)])
                ->count();

            $categories->push([
                'title' => 'Warranty Expirations',
                'count' => $warrantyCount,
                'description' => 'Coverage ending within 60 days',
                'badge_class' => $warrantyCount > 0 ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800',
                'route' => route('maintenance.warranty'),
                'action_label' => 'See More',
            ]);
        }

        // 4. Return Obligations / Active Loans
        $loansCount = $scopedBase()
            ->where(function ($q): void {
                $q->where('status', 'Loan')
                    ->orWhereNotNull('expected_return_date');
            })
            ->count();

        $returnRoute = $actor->hasRole(ItamRole::Employee)
            ? route('my-assets')
            : ($actor->hasPermission('transactions.manage') ? route('transactions.loan') : route('transactions.history'));

        $categories->push([
            'title' => 'Return Obligations',
            'count' => $loansCount,
            'description' => 'Temporary loans & scheduled returns',
            'badge_class' => $loansCount > 0 ? 'bg-purple-100 text-purple-800' : 'bg-emerald-100 text-emerald-800',
            'route' => $returnRoute,
            'action_label' => 'See More',
        ]);

        // 5. Custody Acknowledgements (for Employee or Admin with pending items)
        if ($actor->hasRole(ItamRole::Employee)) {
            $unacknowledgedCount = $scopedBase()
                ->whereNotIn('id', AssetAcknowledgement::where('usr_id', $actor->usr_id)->where('status', 'Acknowledged')->pluck('asset_id'))
                ->count();

            $categories->push([
                'title' => 'Pending Acknowledgements',
                'count' => $unacknowledgedCount,
                'description' => 'Equipment receipt sign-off required',
                'badge_class' => $unacknowledgedCount > 0 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800',
                'route' => route('my-assets'),
                'action_label' => 'See More',
            ]);
        } elseif ($actor->hasPermission('assets.view')) {
            $unacknowledgedCount = Asset::where('status', 'Assigned')
                ->whereNotIn('id', AssetAcknowledgement::where('status', 'Acknowledged')->pluck('asset_id'))
                ->count();

            if ($unacknowledgedCount > 0) {
                $categories->push([
                    'title' => 'Pending Acknowledgements',
                    'count' => $unacknowledgedCount,
                    'description' => 'Assigned assets awaiting employee receipt',
                    'badge_class' => 'bg-rose-100 text-rose-800',
                    'route' => route('assets.assigned'),
                    'action_label' => 'See More',
                ]);
            }
        }

        return $categories;
    }
}
