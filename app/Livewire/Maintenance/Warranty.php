<?php

namespace App\Livewire\Maintenance;

use App\Models\Asset;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Warranty extends Component
{
    use WithPagination;

    public bool $showWarrantyModal = false;

    public string $warrantyAssetId = '';

    public string $warrantyProvider = '';

    public ?string $warrantyStart = null;

    public ?string $warrantyEnd = null;

    public string $supportContract = '';

    public string $search = '';

    public string $statusFilter = 'all';

    public string $sortField = 'warranty_default';

    public string $sortDirection = 'asc';

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
                $this->warrantyStart = $asset->warranty_start
                    ? substr((string) $asset->warranty_start, 0, 10)
                    : ($asset->purchase_date ? substr((string) $asset->purchase_date, 0, 10) : null);
                $this->warrantyEnd = $asset->warranty_end ? substr((string) $asset->warranty_end, 0, 10) : null;
                $this->supportContract = (string) ($asset->support_contract ?? '');
            }
        } else {
            $first = Asset::whereNull('warranty_end')->orWhere('warranty_end', '<=', now()->addDays(30))->first();
            $this->warrantyAssetId = $first ? $first->id : '';
            $this->warrantyProvider = $first ? (string) ($first->warranty_provider ?? '') : '';
            $this->warrantyStart = $first && $first->purchase_date ? substr((string) $first->purchase_date, 0, 10) : null;
            $this->warrantyEnd = now()->addYears(3)->format('Y-m-d');
            $this->supportContract = '';
        }

        $this->resetValidation();
        $this->showWarrantyModal = true;
    }

    public function updatedWarrantyAssetId(string $value): void
    {
        $asset = Asset::find($value);
        if ($asset) {
            $this->warrantyProvider = (string) ($asset->warranty_provider ?? '');
            $this->warrantyStart = $asset->warranty_start
                ? substr((string) $asset->warranty_start, 0, 10)
                : ($asset->purchase_date ? substr((string) $asset->purchase_date, 0, 10) : null);
            $this->warrantyEnd = $asset->warranty_end ? substr((string) $asset->warranty_end, 0, 10) : null;
            $this->supportContract = (string) ($asset->support_contract ?? '');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSortField(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
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

        $today = now()->toDateString();
        $baseQuery = fn () => $actor->scopeDepartmentQuery(Asset::query(), 'department');

        $query = $actor->scopeDepartmentQuery(Asset::query(), 'department');

        // Apply Search
        if (trim($this->search) !== '') {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('id', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%")
                    ->orWhere('serial_number', 'like', "%{$term}%")
                    ->orWhere('warranty_provider', 'like', "%{$term}%");
            });
        }

        // Apply Status Filter
        match ($this->statusFilter) {
            'active' => $query->whereNotNull('warranty_end')->where('warranty_end', '>=', $today),
            'expiring_30' => $query->whereNotNull('warranty_end')->whereBetween('warranty_end', [$today, now()->addDays(30)->toDateString()]),
            'expired' => $query->whereNotNull('warranty_end')->where('warranty_end', '<', $today),
            'unknown' => $query->whereNull('warranty_end'),
            default => null, // 'all'
        };

        // Apply Sorting
        if ($this->sortField === 'warranty_default') {
            // Requirement 6: Most near to end of warranty date first (>= today, ASC), expired warranty at bottom
            $query->orderByRaw('
                CASE 
                    WHEN warranty_end >= ? THEN 0 
                    WHEN warranty_end IS NOT NULL THEN 1 
                    ELSE 2 
                END ASC,
                warranty_end ASC
            ', [$today]);
        } elseif ($this->sortField === 'warranty_end') {
            $query->orderBy('warranty_end', $this->sortDirection);
        } elseif ($this->sortField === 'id') {
            $query->orderBy('id', $this->sortDirection);
        } elseif ($this->sortField === 'name') {
            $query->orderBy('name', $this->sortDirection);
        } elseif ($this->sortField === 'warranty_provider') {
            $query->orderBy('warranty_provider', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $assets = $query->paginate(15);
        $allCandidateAssets = $actor->scopeDepartmentQuery(Asset::query(), 'department')->orderBy('id')->get();

        $coveredCount = $baseQuery()->whereNotNull('warranty_end')->where('warranty_end', '>=', $today)->count();
        $expiresIn30 = $baseQuery()->whereNotNull('warranty_end')->whereBetween('warranty_end', [$today, now()->addDays(30)->toDateString()])->count();
        $expiredCount = $baseQuery()->whereNotNull('warranty_end')->where('warranty_end', '<', $today)->count();
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
