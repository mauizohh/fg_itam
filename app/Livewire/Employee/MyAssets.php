<?php

namespace App\Livewire\Employee;

use App\Models\Asset;
use App\Models\AssetAcknowledgement;
use App\Models\AuditLog;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class MyAssets extends Component
{
    public string $remarks = '';

    public ?string $selectedAssetId = null;

    /**
     * Record receipt acknowledgement for a personally assigned asset.
     */
    public function openAcknowledge(string $assetId): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasPermission('my_assets.acknowledge'), 403);

        $this->selectedAssetId = $assetId;
        $this->remarks = '';
    }

    /**
     * Cancel acknowledgement modal.
     */
    public function cancelAcknowledge(): void
    {
        $this->selectedAssetId = null;
        $this->remarks = '';
    }

    /**
     * Persist acknowledgement in fg_itam_asset_acknowledgements and audit log.
     */
    public function acknowledge(string $assetId): void
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasPermission('my_assets.acknowledge'), 403);

        $asset = Asset::query()
            ->where('id', $assetId)
            ->where(function ($query) use ($user): void {
                foreach ($this->matchNames($user) as $candidate) {
                    $query->orWhereRaw('LOWER(assigned_user) = ?', [strtolower($candidate)]);
                }
            })
            ->firstOrFail();

        AssetAcknowledgement::updateOrCreate(
            [
                'asset_id' => $asset->id,
                'usr_id' => $user->usr_id,
            ],
            [
                'employee_name' => $user->display_name,
                'status' => 'Acknowledged',
                'acknowledged_at' => now(),
                'remarks' => trim($this->remarks) !== '' ? trim($this->remarks) : 'Equipment receipt & return obligation acknowledged.',
            ]
        );

        AuditLogService::record(
            'Acknowledge asset',
            'Asset',
            $asset->id,
            'Pending Acknowledgement',
            "Acknowledged by {$user->display_name}"
        );

        $this->selectedAssetId = null;
        $this->remarks = '';
        session()->flash('success', "Equipment {$asset->id} ({$asset->name}) acknowledged successfully.");
    }

    /**
     * Candidate custodian strings for the authenticated Admidio employee.
     *
     * @return list<string>
     */
    private function matchNames(User $user): array
    {
        return array_values(array_unique(array_filter([
            trim($user->name),
            trim($user->display_name),
            trim($user->username),
        ])));
    }

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->hasPermission('my_assets.view'), 403);

        $names = $this->matchNames($user);

        $myAssets = Asset::query()
            ->where(function ($query) use ($names): void {
                foreach ($names as $candidate) {
                    $query->orWhereRaw('LOWER(assigned_user) = ?', [strtolower($candidate)]);
                }
            })
            ->orderByDesc('assigned_date')
            ->get();

        $acknowledgements = AssetAcknowledgement::query()
            ->where('usr_id', $user->usr_id)
            ->get()
            ->keyBy('asset_id');

        $myTransactions = Transaction::query()
            ->where(function ($query) use ($names): void {
                foreach ($names as $candidate) {
                    $lower = strtolower($candidate);
                    $query->orWhereRaw('LOWER(to_user) = ?', [$lower])
                        ->orWhereRaw('LOWER(from_user) = ?', [$lower]);
                }
            })
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $myAssetIds = $myAssets->pluck('id')->toArray();

        $recentActivities = AuditLog::query()
            ->forAssets()
            ->forEmployee($user, $myAssetIds)
            ->orderByDesc('timestamp')
            ->limit(10)
            ->get();

        $activityAssetIds = $recentActivities->pluck('object_id')->unique()->filter()->values();
        $activityAssets = Asset::whereIn('id', $activityAssetIds)->get()->keyBy('id');

        $returnObligationsCount = $myAssets->filter(
            fn (Asset $asset): bool => $asset->status === 'Loan' || ! empty($asset->expected_return_date)
        )->count();

        return view('livewire.employee.my-assets', [
            'user' => $user,
            'myAssets' => $myAssets,
            'acknowledgements' => $acknowledgements,
            'myTransactions' => $myTransactions,
            'recentActivities' => $recentActivities,
            'activityAssets' => $activityAssets,
            'returnObligationsCount' => $returnObligationsCount,
        ])->layout('layouts.app', [
            'title' => 'My Equipment',
            'breadcrumb' => 'IT Asset Management / Self-Service / My Equipment',
        ]);
    }
}
