<?php

namespace App\Livewire\Maintenance;

use App\Models\Asset;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Warranty extends Component
{
    public bool $showWarrantyModal = false;

    public string $warrantyAssetId = '';

    public string $warrantyProvider = '';

    public ?string $warrantyStart = null;

    public ?string $warrantyEnd = null;

    public string $supportContract = '';

    public function openWarrantyModal(?string $assetId = null): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('maintenance.manage') || $actor->hasPermission('assets.edit'), 403);

        if ($assetId !== null) {
            $asset = Asset::find($assetId);
            if ($asset) {
                $this->warrantyAssetId = $asset->id;
                $this->warrantyProvider = (string) ($asset->warranty_provider ?? '');
                $this->warrantyStart = $asset->warranty_start ? substr((string) $asset->warranty_start, 0, 10) : null;
                $this->warrantyEnd = $asset->warranty_end ? substr((string) $asset->warranty_end, 0, 10) : null;
                $this->supportContract = (string) ($asset->support_contract ?? '');
            }
        } else {
            $first = Asset::whereNull('warranty_end')->orWhere('warranty_end', '<=', now()->addDays(30))->first();
            $this->warrantyAssetId = $first ? $first->id : '';
            $this->warrantyProvider = $first ? (string) ($first->warranty_provider ?? '') : '';
            $this->warrantyStart = null;
            $this->warrantyEnd = now()->addYears(3)->format('Y-m-d');
            $this->supportContract = '';
        }

        $this->resetValidation();
        $this->showWarrantyModal = true;
    }

    public function cancelWarrantyModal(): void
    {
        $this->showWarrantyModal = false;
        $this->resetValidation();
    }

    public function saveWarranty(): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('maintenance.manage') || $actor->hasPermission('assets.edit'), 403);

        $this->validate([
            'warrantyAssetId' => 'required|exists:fg_itam_assets,id',
            'warrantyProvider' => 'nullable|string|max:150',
            'warrantyStart' => 'nullable|date',
            'warrantyEnd' => 'required|date',
            'supportContract' => 'nullable|string|max:150',
        ]);

        $asset = Asset::findOrFail($this->warrantyAssetId);
        $before = "End: {$asset->warranty_end}, Provider: {$asset->warranty_provider}";

        $asset->update([
            'warranty_provider' => $this->warrantyProvider ?: null,
            'warranty_start' => $this->warrantyStart ?: null,
            'warranty_end' => $this->warrantyEnd,
            'support_contract' => $this->supportContract ?: null,
        ]);

        $after = "End: {$this->warrantyEnd}, Provider: {$this->warrantyProvider}";

        AuditLogService::record(
            'Update warranty',
            'Asset',
            $asset->id,
            $before,
            $after
        );

        $this->showWarrantyModal = false;
        session()->flash('success', "Warranty updated successfully for asset {$asset->id}!");
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('maintenance.view'), 403);

        $baseQuery = fn () => $actor->scopeDepartmentQuery(Asset::query(), 'department');

        $assets = $actor->scopeDepartmentQuery(Asset::whereNotNull('warranty_end'), 'department')
            ->orderBy('warranty_end')
            ->get();

        $allCandidateAssets = $actor->scopeDepartmentQuery(Asset::query(), 'department')->orderBy('id')->get();

        $coveredCount = $baseQuery()->whereNotNull('warranty_end')->where('warranty_end', '>=', now()->toDateString())->count();
        $expiresIn30 = $baseQuery()->whereNotNull('warranty_end')->whereBetween('warranty_end', [now()->toDateString(), now()->addDays(30)->toDateString()])->count();
        $expiredCount = $baseQuery()->whereNotNull('warranty_end')->where('warranty_end', '<', now()->toDateString())->count();
        $unknownCount = $baseQuery()->whereNull('warranty_end')->count();

        return view('livewire.maintenance.warranty', compact(
            'assets',
            'allCandidateAssets',
            'coveredCount',
            'expiresIn30',
            'expiredCount',
            'unknownCount'
        ))->layout('layouts.app', ['title' => 'Warranty Management', 'breadcrumb' => 'IT Asset Management / Maintenance / Warranty']);
    }
}
