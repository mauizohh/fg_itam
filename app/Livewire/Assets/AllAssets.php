<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Department;
use App\Models\Location;
use App\Services\AssetIdGeneratorService;
use App\Services\AuditLogService;
use Livewire\Component;
use Livewire\WithPagination;

class AllAssets extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'All';
    public string $categoryFilter = 'All';
    public string $locationFilter = 'All';

    // Modal state for Add Asset
    public bool $showAddModal = false;
    public string $newId = '';
    public string $newCategory = 'LAP';
    public string $newName = '';
    public string $newBrand = '';
    public string $newModel = '';
    public string $newSerial = '';
    public string $newLocation = 'IT Store';
    public string $newStatus = 'Available';
    public ?float $newCost = null;

    public function mount()
    {
        if (request()->has('search')) {
            $this->search = request()->query('search');
        }
    }

    public function openAddModal()
    {
        $this->newId = AssetIdGeneratorService::generate($this->newCategory);
        $this->showAddModal = true;
    }

    public function updatedNewCategory($val)
    {
        $this->newId = AssetIdGeneratorService::generate($val);
    }

    public function saveAsset()
    {
        $this->validate([
            'newId' => 'required|string',
            'newName' => 'required|string|max:255',
            'newSerial' => 'required|string|unique:fg_itam_assets,serial_number',
        ]);

        $cat = Category::find($this->newCategory);

        $asset = Asset::create([
            'id' => $this->newId,
            'asset_tag' => 'TAG-' . substr($this->newId, 3),
            'name' => $this->newName,
            'category_id' => $this->newCategory,
            'category_name' => $cat ? $cat->name : 'Equipment',
            'brand' => $this->newBrand ?: 'Standard',
            'model' => $this->newModel ?: $this->newName,
            'serial_number' => $this->newSerial,
            'status' => $this->newStatus,
            'assigned_location' => $this->newLocation,
            'purchase_cost' => $this->newCost ?: 0.00,
            'verification_status' => 'Verified',
            'last_physical_verification' => now(),
        ]);

        AuditLogService::record('Register asset', 'Asset', $asset->id, null, 'Registered in IT Store');

        $this->showAddModal = false;
        $this->reset(['newName', 'newBrand', 'newModel', 'newSerial', 'newCost']);
        session()->flash('success', "Asset {$asset->id} registered successfully!");
    }

    public function render()
    {
        $query = Asset::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('id', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%")
                  ->orWhere('serial_number', 'like', "%{$this->search}%")
                  ->orWhere('assigned_user', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter !== 'All') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter !== 'All') {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->locationFilter !== 'All') {
            $query->where('assigned_location', 'like', "%{$this->locationFilter}%");
        }

        $assets = $query->orderBy('id')->paginate(15);
        $categories = Category::all();

        return view('livewire.assets.all-assets', compact('assets', 'categories'))
            ->layout('layouts.app', ['title' => 'All Assets', 'breadcrumb' => 'IT Asset Management / Assets']);
    }
}
