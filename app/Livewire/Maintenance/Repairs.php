<?php

namespace App\Livewire\Maintenance;

use App\Models\Asset;
use App\Models\Repair;
use App\Services\AuditLogService;
use Livewire\Component;

class Repairs extends Component
{
    public string $search = '';
    public string $statusFilter = 'Open';
    public string $vendorFilter = 'All';

    public bool $showModal = false;
    public string $newCaseId = 'RPR-0275';
    public string $assetId = 'IT-LAP-2026-00418';
    public string $issue = 'Battery failure';
    public string $vendor = 'TechCare';
    public float $cost = 350.00;

    public function createRepair()
    {
        $this->validate([
            'newCaseId' => 'required',
            'assetId' => 'required',
            'issue' => 'required',
        ]);

        Repair::create([
            'id' => $this->newCaseId,
            'asset_id' => $this->assetId,
            'issue' => $this->issue,
            'opened_date' => now(),
            'target_return_date' => now()->addDays(7),
            'vendor' => $this->vendor,
            'cost' => $this->cost,
            'status' => 'Repairing',
        ]);

        $asset = Asset::find($this->assetId);
        if ($asset) {
            $asset->update(['status' => 'In Repair']);
        }

        AuditLogService::record('Open repair', 'Asset', $this->assetId, 'Available', "Sent to {$this->vendor} for repair");

        $this->showModal = false;
        session()->flash('success', "Repair case {$this->newCaseId} logged!");
    }

    public function render()
    {
        $query = Repair::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->search}%")
                  ->orWhere('asset_id', 'like', "%{$this->search}%")
                  ->orWhere('issue', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter === 'Open') {
            $query->whereIn('status', ['Repairing', 'Overdue', 'Awaiting Part']);
        } elseif ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->vendorFilter !== 'All') {
            $query->where('vendor', $this->vendorFilter);
        }

        $repairs = $query->orderByDesc('opened_date')->get();
        $assets = Asset::all();

        return view('livewire.maintenance.repairs', compact('repairs', 'assets'))
            ->layout('layouts.app', ['title' => 'Repairs', 'breadcrumb' => 'IT Asset Management / Maintenance / Repairs']);
    }
}
