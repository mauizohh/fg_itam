<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class RetiredAssets extends Component
{
    public string $search = '';

    public string $dispositionFilter = 'All';

    public string $yearFilter = '2026';

    public bool $showRetireModal = false;

    public string $retireAssetId = '';

    public string $retireReason = 'End of life';

    public string $retireDisposition = 'Recycle';

    public string $retireSanitization = 'Verified';

    public function retireAsset()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.dispose'), 403);

        $this->validate([
            'retireAssetId' => 'required|exists:fg_itam_assets,id',
            'retireReason' => 'required',
        ]);

        $asset = Asset::find($this->retireAssetId);
        $oldStatus = $asset->status;

        $asset->update([
            'status' => 'Retired',
            'retired_date' => now(),
            'retirement_reason' => $this->retireReason,
            'disposition' => $this->retireDisposition,
            'data_sanitization' => $this->retireSanitization,
        ]);

        AuditLogService::record('Retire asset', 'Asset', $asset->id, $oldStatus, "Retired ({$this->retireDisposition})");

        $this->showRetireModal = false;
        $this->reset(['retireAssetId']);
        session()->flash('success', "Asset {$asset->id} retired successfully!");
    }

    public function render()
    {
        /** @var User $actor */
        $actor = Auth::user();
        abort_unless($actor->hasPermission('assets.retired.view'), 403);

        $query = Asset::where('status', 'Retired');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%")
                    ->orWhere('retirement_reason', 'like', "%{$this->search}%");
            });
        }

        if ($this->dispositionFilter !== 'All') {
            $query->where('disposition', $this->dispositionFilter);
        }

        $retired = $query->orderByDesc('retired_date')->get();
        $availableForRetire = Asset::where('status', '!=', 'Retired')->get();

        return view('livewire.assets.retired-assets', compact('retired', 'availableForRetire'))
            ->layout('layouts.app', ['title' => 'Retired Assets', 'breadcrumb' => 'IT Asset Management / Assets / Retired']);
    }
}
