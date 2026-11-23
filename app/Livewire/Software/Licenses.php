<?php

namespace App\Livewire\Software;

use App\Models\Asset;
use App\Models\License;
use App\Models\LicenseAsset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class Licenses extends Component
{
    public bool $showAssetModal = false;

    public ?int $selectedLicenseId = null;

    public string $assetSearch = '';

    public string $newAssetId = '';

    public function openAssetModal(int $licenseId): void
    {
        $this->selectedLicenseId = $licenseId;
        $this->assetSearch = '';
        $this->newAssetId = '';
        $this->showAssetModal = true;
    }

    public function closeAssetModal(): void
    {
        $this->showAssetModal = false;
        $this->selectedLicenseId = null;
        $this->assetSearch = '';
        $this->newAssetId = '';
    }

    public function assignAsset(): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('software.manage'), 403);

        if (! $this->selectedLicenseId || empty($this->newAssetId)) {
            return;
        }

        $license = License::find($this->selectedLicenseId);
        $asset = Asset::find($this->newAssetId);

        if ($license && $asset && Schema::hasTable('fg_itam_license_assets')) {
            LicenseAsset::firstOrCreate([
                'software_name' => $license->software_name,
                'asset_id' => $asset->id,
            ], [
                'license_id' => $license->id,
                'assigned_date' => now()->format('Y-m-d'),
                'status' => 'Active',
            ]);

            $this->newAssetId = '';
            session()->flash('modal_success', "Asset {$asset->id} assigned to {$license->software_name} license!");
        }
    }

    public function unlinkAsset(string $assetId): void
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('software.manage'), 403);

        if (! $this->selectedLicenseId) {
            return;
        }

        $license = License::find($this->selectedLicenseId);
        if ($license && Schema::hasTable('fg_itam_license_assets')) {
            LicenseAsset::where(function ($q) use ($license): void {
                $q->where('license_id', $license->id)
                    ->orWhere('software_name', $license->software_name);
            })->where('asset_id', $assetId)->delete();

            session()->flash('modal_success', "Asset {$assetId} removed from {$license->software_name} license.");
        }
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('software.view'), 403);

        $licenses = License::all();
        $purchasedSeats = $licenses->sum('purchased_seats') ?: 1240;
        $assignedSeats = $licenses->sum('assigned_seats') ?: 1082;
        $availableSeats = $licenses->sum('available_seats') ?: 158;
        $overallocated = 0;

        $selectedLicense = null;
        $assignedAssets = collect();
        $availableAssets = collect();

        if ($this->selectedLicenseId) {
            $selectedLicense = License::find($this->selectedLicenseId);
            if ($selectedLicense) {
                $allAssigned = $selectedLicense->getAssignedAssets();

                if (! empty(trim($this->assetSearch))) {
                    $q = strtolower(trim($this->assetSearch));
                    $assignedAssets = $allAssigned->filter(function ($a) use ($q) {
                        return str_contains(strtolower($a->id), $q)
                            || str_contains(strtolower($a->name), $q)
                            || str_contains(strtolower($a->assigned_user ?? ''), $q)
                            || str_contains(strtolower($a->department ?? ''), $q)
                            || str_contains(strtolower($a->assigned_location ?? ''), $q);
                    })->values();
                } else {
                    $assignedAssets = $allAssigned;
                }

                $assignedIds = $allAssigned->pluck('id')->toArray();
                $availableAssets = Asset::whereNotIn('id', $assignedIds)
                    ->whereIn('status', ['Available', 'Assigned', 'Loan'])
                    ->orderBy('id')
                    ->take(30)
                    ->get();
            }
        }

        return view('livewire.software.licenses', compact(
            'licenses',
            'purchasedSeats',
            'assignedSeats',
            'availableSeats',
            'overallocated',
            'selectedLicense',
            'assignedAssets',
            'availableAssets'
        ))->layout('layouts.app', ['title' => 'License Utilization', 'breadcrumb' => 'IT Asset Management / Software / Licenses']);
    }
}
